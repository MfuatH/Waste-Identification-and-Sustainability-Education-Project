from fastapi import APIRouter, UploadFile, File, HTTPException
from services.ml_service import predict_waste

router = APIRouter(
    prefix="/predict",
    tags=["Prediction"]
)

@router.post("", summary="Predict waste type from image")
async def predict(file: UploadFile = File(...)):
    """
    Endpoint untuk memprediksi jenis sampah berdasarkan gambar.
    Mengembalikan kelas spesifik, kategori utama, confidence, dan status confidence.
    """
    # Validasi tipe file
    if not file.content_type or not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="File harus berupa gambar (JPEG, PNG, BMP, GIF)")

    # Baca konten gambar
    file_bytes = await file.read()

    try:
        # Panggil service ml_service.py
        result = predict_waste(file_bytes)
        return {
            "filename": file.filename,
            "predicted_class": result["predicted_class"],
            "category": result["category"],
            "confidence": result["confidence"],
            "confidence_status": result["confidence_status"]
        }
    except Exception as e:
        # Tangani error internal
        raise HTTPException(status_code=500, detail=f"Prediction failed: {str(e)}")