from fastapi import APIRouter, HTTPException
from schemas.chat_schema import ChatRequest, ChatResponse
from services.chat_service import chat_with_gemma

router = APIRouter(
    prefix="/chat",
    tags=["Chat"]
)

@router.post("", response_model=ChatResponse, summary="Chat with Gemma for waste recommendation")
async def chat(request: ChatRequest):
    """
    Endpoint untuk mengirim pertanyaan pengguna ke Gemma,
    dengan atau tanpa konteks hasil prediksi gambar.
    """
    try:
        reply = chat_with_gemma(
            message=request.message,
            predicted_class=request.predicted_class,
            category=request.category,
            confidence=request.confidence
        )
        return ChatResponse(reply=reply)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Chat failed: {str(e)}")