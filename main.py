from fastapi import FastAPI
from pydantic import BaseModel

from services.openrouter_service import ask_gemma

# Inisialisasi FastAPI
app = FastAPI(
    title="AKSA AI Backend",
    description="Backend API untuk integrasi Gemma dan model Machine Learning",
    version="1.0.0"
)


# Request Schema
class ChatRequest(BaseModel):
    prompt: str


# Root Endpoint
@app.get("/")
def root():

    return {
        "success": True,
        "message": "AKSA AI Backend is running"
    }



# Health Check Endpoint
@app.get("/health")
def health_check():

    return {
        "status": "healthy"
    }


# Endpoint Chat AI Gemma
@app.post("/chat")
def chat(data: ChatRequest):

    result = ask_gemma(data.prompt)

    return result


# Endpoint Machine Learning
class PredictRequest(BaseModel):
    price_per_unit: float
    units_sold: int
    operating_margin: float


@app.post("/predict")
def predict(data: PredictRequest):

    result = predict_sales(
        data.price_per_unit,
        data.units_sold,
        data.operating_margin
    )

    return {
        "prediction": result
    }
