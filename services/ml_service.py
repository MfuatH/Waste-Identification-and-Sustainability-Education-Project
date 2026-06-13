import json
from pathlib import Path
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


def load_artifacts():
    global _model, _class_names, _class_to_category

    if _model is None:
        _model = tf.keras.models.load_model(MODEL_PATH, compile=False)

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