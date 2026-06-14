from fastapi import APIRouter, UploadFile, File, HTTPException
from services.ml_service import predict_waste
from services.chat_service import get_waste_recommendation, get_youtube_recommendation

router = APIRouter(
    prefix="/predict",
    tags=["Prediction"]
)

@router.post("", summary="Predict waste type from image")
async def predict(file: UploadFile = File(...)):
    """
    Endpoint untuk memprediksi jenis sampah berdasarkan gambar.
    Mengembalikan kelas spesifik, kategori utama, confidence, status confidence, rekomendasi pengolahan, dan YouTube link.
    """
    # Validasi tipe file
    if not file.content_type or not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="File harus berupa gambar (JPEG, PNG, BMP, GIF)")

    # Baca konten gambar
    file_bytes = await file.read()

    try:
        # Panggil service ml_service.py untuk prediksi
        result = predict_waste(file_bytes)
        
        # Dapatkan rekomendasi pengolahan dari Gemma
        recommendation = get_waste_recommendation(
            predicted_class=result["predicted_class"],
            category=result["category"],
            confidence=result["confidence"]
        )
        
        # Dapatkan YouTube link rekomendasi dari Gemma
        youtube_link = get_youtube_recommendation(
            predicted_class=result["predicted_class"],
            category=result["category"]
        )
        
        return {
            "filename": file.filename,
            "predicted_class": result["predicted_class"],
            "category": result["category"],
            "confidence": result["confidence"],
            "confidence_status": result["confidence_status"],
            "recommendation": recommendation,  # Rekomendasi pengolahan dari Gemma
            "youtube": youtube_link  # YouTube link dari Gemma
        }
    except Exception as e:
        # Tangani error internal
        raise HTTPException(status_code=500, detail=f"Prediction failed: {str(e)}")