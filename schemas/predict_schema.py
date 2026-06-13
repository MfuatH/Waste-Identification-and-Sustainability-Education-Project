from pydantic import BaseModel, Field
from typing import Optional

class PredictResponse(BaseModel):
    predicted_class: str = Field(..., description="Kelas spesifik hasil prediksi model")
    category: str = Field(..., description="Kategori utama: organik, anorganik, atau e-waste")
    confidence: float = Field(..., description="Nilai confidence dari model")
    confidence_status: str = Field(..., description="Status confidence: high, medium, low")
    recommendation: Optional[str] = Field(None, description="Rekomendasi pengolahan sampah")