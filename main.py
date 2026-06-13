from fastapi import FastAPI
from routers.predict_router import router as predict_router
from routers.chat_router import router as chat_router

app = FastAPI(
    title="WISE API",
    description="FastAPI backend untuk integrasi Machine Learning dan Gemma melalui OpenRouter",
    version="1.0.0"
)

app.include_router(predict_router)
app.include_router(chat_router)

@app.get("/", tags=["Root"])
def root():
    return {
        "message": "WISE API running",
        "status": "ok"
    }