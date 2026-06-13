from pathlib import Path
from dotenv import load_dotenv
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

BASE_DIR = Path(__file__).resolve().parent
DOTENV_PATHS = [
    BASE_DIR / '.env',
    BASE_DIR.parent / 'VoksSmartWaste' / '.env'
]
for dotenv_path in DOTENV_PATHS:
    if dotenv_path.exists():
        load_dotenv(dotenv_path)

from routers.predict_router import router as predict_router
from routers.chat_router import router as chat_router

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=['*'],
    allow_credentials=True,
    allow_methods=['*'],
    allow_headers=['*'],
)

app.include_router(predict_router)
app.include_router(chat_router)

@app.get('/')
def root():
    return {
        'message': 'WISE API Running',
        'endpoints': ['/predict', '/chat']
    }

@app.get('/health')
def health():
    return {
        'status': 'healthy',
        'predict_route': '/predict',
        'chat_route': '/chat'
    }
