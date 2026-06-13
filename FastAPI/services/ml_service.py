import importlib
import json
import sys
from pathlib import Path
from types import ModuleType
from typing import Dict, Any
from io import BytesIO

import numpy as np
import tensorflow as tf
from PIL import Image

BASE_DIR = Path(__file__).resolve().parent.parent
MODEL_DIR = BASE_DIR / "models"
MODEL_PATH = MODEL_DIR / "wise_mobilenetv2_final_rebuilt.keras"
CLASS_NAMES_PATH = MODEL_DIR / "class_names.json"
CLASS_TO_CATEGORY_PATH = MODEL_DIR / "class_to_category.json"

IMAGE_SIZE = (224, 224)

_model = None
_class_names = None
_class_to_category = None


def _ensure_keras_engine_aliases() -> None:
    try:
        import keras

        root = Path(keras.__file__).resolve().parent
        src_dir = root / "src"
        if (src_dir / "engine").exists():
            return

        engine_pkg = ModuleType("keras.src.engine")
        engine_pkg.__path__ = []
        engine_pkg.__package__ = "keras.src.engine"
        engine_pkg.__spec__ = importlib.util.spec_from_loader("keras.src.engine", loader=None, is_package=True)
        sys.modules["keras.src.engine"] = engine_pkg

        alias_map = {
            "keras.src.engine.functional": "keras.src.models.functional",
            "keras.src.engine.sequential": "keras.src.models.sequential",
        }

        for alias, target in alias_map.items():
            if importlib.util.find_spec(alias) is None and importlib.util.find_spec(target) is not None:
                target_mod = importlib.import_module(target)
                alias_mod = ModuleType(alias)
                alias_mod.__dict__.update(target_mod.__dict__)
                alias_mod.__spec__ = importlib.util.spec_from_loader(alias, loader=None, is_package=False)
                sys.modules[alias] = alias_mod
    except Exception:
        pass


def _patch_batchnorm_axis_support() -> None:
    try:
        import keras
        bn_module = importlib.import_module("keras.src.layers.normalization.batch_normalization")
        BatchNormalization = getattr(bn_module, "BatchNormalization", None)
        if BatchNormalization is None:
            return

        original_init = BatchNormalization.__init__

        def patched_init(self, axis=-1, *args, **kwargs):
            if isinstance(axis, (list, tuple)):
                axis = axis[0] if len(axis) == 1 else tuple(axis)
            return original_init(self, axis=axis, *args, **kwargs)

        BatchNormalization.__init__ = patched_init

        original_from_config = BatchNormalization.from_config

        def patched_from_config(cls, config):
            if isinstance(config, dict) and "axis" in config and isinstance(config["axis"], (list, tuple)):
                config = config.copy()
                axis = config["axis"]
                config["axis"] = axis[0] if len(axis) == 1 else tuple(axis)
            return original_from_config.__func__(cls, config) if hasattr(original_from_config, "__func__") else original_from_config(config)

        BatchNormalization.from_config = classmethod(patched_from_config)
    except Exception:
        pass


def _patch_depthwise_conv2d_groups_support() -> None:
    try:
        import keras
        from keras.layers import DepthwiseConv2D

        original_init = DepthwiseConv2D.__init__

        def patched_init(self, *args, **kwargs):
            if isinstance(kwargs, dict) and 'groups' in kwargs:
                kwargs = kwargs.copy()
                kwargs.pop('groups', None)
            return original_init(self, *args, **kwargs)

        DepthwiseConv2D.__init__ = patched_init

        original_from_config = DepthwiseConv2D.from_config

        def patched_from_config(cls, config):
            if isinstance(config, dict) and 'groups' in config:
                config = config.copy()
                config.pop('groups', None)
            return original_from_config.__func__(cls, config) if hasattr(original_from_config, '__func__') else original_from_config(config)

        DepthwiseConv2D.from_config = classmethod(patched_from_config)
    except Exception:
        pass


def _patch_keras_compatibility() -> None:
    _ensure_keras_engine_aliases()
    _patch_batchnorm_axis_support()
    _patch_depthwise_conv2d_groups_support()


def _reconstruct_model_from_archive(archive_path: Path):
    """Rebuild a MobileNetV2-based model from the .keras archive and load weights.

    This is a best-effort fallback when `tf.keras.models.load_model` fails
    due to Keras/TF version incompatibilities.
    """
    import zipfile
    import json
    import tempfile
    import h5py
    import os
    import tensorflow as tf

    # read config.json from archive
    with zipfile.ZipFile(archive_path, 'r') as z:
        cfg = json.loads(z.read('config.json').decode('utf-8'))
        members = z.namelist()
        if 'model.weights.h5' in members:
            tmpdir = tempfile.gettempdir()
            weights_path = os.path.join(tmpdir, 'model.weights.h5')
            with open(weights_path, 'wb') as f:
                f.write(z.read('model.weights.h5'))
        else:
            raise RuntimeError('weights file not found in archive')

    # parse top layers parameters (best-effort)
    layers = cfg.get('config', {}).get('layers', [])
    # find Dense units and Dropout rates from top-level layers
    dense_units = [l['config'].get('units') for l in layers if l['class_name'] == 'Dense']
    dropout_rates = [l['config'].get('rate') for l in layers if l['class_name'] == 'Dropout']

    # defaults if not found
    d0 = dense_units[0] if len(dense_units) >= 1 and dense_units[0] is not None else 128
    d1 = dense_units[1] if len(dense_units) >= 2 and dense_units[1] is not None else 13
    r0 = float(dropout_rates[0]) if len(dropout_rates) >= 1 and dropout_rates[0] is not None else 0.35
    r1 = float(dropout_rates[1]) if len(dropout_rates) >= 2 and dropout_rates[1] is not None else 0.25

    # build model (assume input 224x224x3)
    input_tensor = tf.keras.Input(shape=(224, 224, 3), name='input_2')
    x = tf.keras.layers.Rescaling(1.0 / 255.0, name='mobilenetv2_preprocessing')(input_tensor)
    base = tf.keras.applications.MobileNetV2(include_top=False, input_tensor=x, weights=None, alpha=1.0)

    x = base.output
    x = tf.keras.layers.GlobalAveragePooling2D(name='global_average_pooling2d')(x)
    x = tf.keras.layers.Dropout(r0, name='dropout')(x)
    x = tf.keras.layers.Dense(d0, activation='relu', name='dense')(x)
    x = tf.keras.layers.Dropout(r1, name='dropout_1')(x)
    outputs = tf.keras.layers.Dense(d1, activation='softmax', name='dense_1')(x)

    model = tf.keras.Model(inputs=input_tensor, outputs=outputs)

    # attempt load_weights by_name then fallback to per-layer and shape matching
    try:
        model.load_weights(weights_path, by_name=True)
        return model
    except Exception:
        # manual per-layer loader
        with h5py.File(weights_path, 'r') as f:
            saved_map = {}
            for key in list(f.keys()):
                if key.startswith('layers\\'):
                    simple = key.split('\\')[-1]
                    saved_map[simple] = f[key]

            # name-based load
            loaded = 0
            for layer in model.layers:
                grp = saved_map.get(layer.name)
                if not grp:
                    continue
                vg = grp.get('vars')
                if not vg:
                    continue
                items = [vg[k][()] for k in sorted(list(vg.keys()), key=lambda x: int(x))]
                try:
                    layer.set_weights(items)
                    loaded += 1
                except Exception:
                    pass

            # shape-based matching for remaining
            model_layers_with_weights = [l for l in model.layers if l.weights]
            if loaded < len(model_layers_with_weights):
                shape_map = {}
                for key in list(f.keys()):
                    if not key.startswith('layers\\'):
                        continue
                    g = f[key]
                    vg = g.get('vars')
                    if vg is None:
                        continue
                    shapes = tuple(tuple(vg[k].shape) for k in sorted(list(vg.keys()), key=lambda x: int(x)))
                    shape_map.setdefault(shapes, []).append((key, g))

                used = set(saved_map.keys())
                for layer in model_layers_with_weights:
                    if layer.name in saved_map:
                        continue
                    expected_shapes = tuple(w.shape for w in layer.get_weights())
                    if expected_shapes in shape_map:
                        candidates = shape_map[expected_shapes]
                        pick = None
                        for k, g in candidates:
                            simple = k.split('\\')[-1]
                            if simple in used:
                                continue
                            pick = (k, g)
                            break
                        if pick is None:
                            pick = candidates[0]
                        _, g = pick
                        vg = g['vars']
                        items = [vg[k][()] for k in sorted(list(vg.keys()), key=lambda x: int(x))]
                        try:
                            layer.set_weights(items)
                        except Exception:
                            pass

    return model


def load_artifacts():
    global _model, _class_names, _class_to_category

    if _model is None:
        _patch_keras_compatibility()
        try:
            _model = tf.keras.models.load_model(MODEL_PATH, compile=False)
        except Exception:
            # try reconstructing model + weights from archive as a fallback
            _model = _reconstruct_model_from_archive(MODEL_PATH)

    if _class_names is None:
        with open(CLASS_NAMES_PATH, "r", encoding="utf-8") as f:
            _class_names = json.load(f)

    if _class_to_category is None:
        with open(CLASS_TO_CATEGORY_PATH, "r", encoding="utf-8") as f:
            _class_to_category = json.load(f)

    return _model, _class_names, _class_to_category


def confidence_status(confidence: float) -> str:
    if confidence >= 0.85:
        return "high"
    elif confidence >= 0.70:
        return "medium"
    return "low"


def preprocess_image(file_bytes: bytes) -> np.ndarray:
    image = Image.open(BytesIO(file_bytes)).convert("RGB")
    image = image.resize(IMAGE_SIZE)
    image_array = np.asarray(image, dtype=np.float32)
    image_array = np.expand_dims(image_array, axis=0)
    return image_array


def predict_waste(file_bytes: bytes) -> Dict[str, Any]:
    model, class_names, class_to_category = load_artifacts()

    image_array = preprocess_image(file_bytes)
    predictions = model.predict(image_array, verbose=0)[0]

    pred_idx = int(np.argmax(predictions))
    predicted_class = class_names[pred_idx]
    confidence = float(predictions[pred_idx])
    category = class_to_category.get(predicted_class, "anorganik")

    return {
        "predicted_class": predicted_class,
        "category": category,
        "confidence": round(confidence, 4),
        "confidence_status": confidence_status(confidence),
    }