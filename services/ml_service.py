import joblib
import numpy as np

model = joblib.load("models/model_adidas.pkl")


def predict_sales(
    price_per_unit,
    units_sold,
    operating_margin
):

    features = np.array([[
        price_per_unit,
        units_sold,
        operating_margin
    ]])

    prediction = model.predict(features)

    return float(prediction[0])