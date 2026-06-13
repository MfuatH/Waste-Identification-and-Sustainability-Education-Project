from pydantic import BaseModel
from typing import Optional

class ChatRequest(BaseModel):
    message: str
    predicted_class: Optional[str] = None
    category: Optional[str] = None
    confidence: Optional[float] = None

class ChatResponse(BaseModel):
    reply: str