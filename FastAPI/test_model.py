from tensorflow.keras.models import load_model
from PIL import Image
import numpy as np

# Load model
model = load_model("model/wise_mobilenetv2_final.keras")

print("Model berhasil dimuat!")
print("Input Shape :", model.input_shape)
print("Output Shape:", model.output_shape)

# Test gambar
img_path = "test.jpg"  # ganti dengan gambar sampah

image = Image.open(img_path).convert("RGB")
image = image.resize((224, 224))

image = np.array(image).astype("float32") / 255.0
image = np.expand_dims(image, axis=0)

prediction = model.predict(image)

print("\nPrediksi:")
print(prediction)
print("Class Index:", np.argmax(prediction))
print("Confidence :", np.max(prediction))