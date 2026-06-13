import os
from pathlib import Path
from dotenv import load_dotenv
import requests
from typing import Optional

BASE_DIR = Path(__file__).resolve().parent.parent
DOTENV_PATHS = [
    BASE_DIR / '.env',
    BASE_DIR / 'Frontend' / '.env'
]
for dotenv_path in DOTENV_PATHS:
    if dotenv_path.exists():
        load_dotenv(dotenv_path)

OPENROUTER_API_KEY = os.getenv('OPENROUTER_API_KEY')
OPENROUTER_MODEL = os.getenv('OPENROUTER_MODEL', 'google/gemma-2-9b-it:free')
OPENROUTER_URL = 'https://openrouter.ai/api/v1/chat/completions'


def build_prompt(
    message: str,
    predicted_class: Optional[str] = None,
    category: Optional[str] = None,
    confidence: Optional[float] = None
) -> str:
    context_lines = []

    if predicted_class:
        context_lines.append(f"Kelas spesifik: {predicted_class}")
    if category:
        context_lines.append(f"Kategori utama: {category}")
    if confidence is not None:
        context_lines.append(f"Confidence: {confidence:.2f}")

    context_text = "\n".join(context_lines) if context_lines else "Tidak ada konteks prediksi gambar."

    return f"""
Anda adalah asisten edukasi pengelolaan sampah untuk aplikasi WISE.

Konteks prediksi:
{context_text}

Pesan pengguna:
{message}

Tugas Anda:
1. Berikan jawaban singkat, jelas, dan edukatif.
2. Jika ada konteks prediksi, gunakan untuk memberi rekomendasi pengolahan sampah yang tepat.
3. Jangan mengubah hasil klasifikasi.
4. Jika confidence rendah, sarankan pengguna mengunggah gambar yang lebih jelas.
5. Gunakan bahasa Indonesia yang sederhana.
""".strip()


def chat_with_gemma(
    message: str,
    predicted_class: Optional[str] = None,
    category: Optional[str] = None,
    confidence: Optional[float] = None
) -> str:
    if not OPENROUTER_API_KEY:
        return "OPENROUTER_API_KEY belum diset di environment."

    prompt = build_prompt(message, predicted_class, category, confidence)

    headers = {
        "Authorization": f"Bearer {OPENROUTER_API_KEY}",
        "Content-Type": "application/json",
        "HTTP-Referer": "http://localhost",
        "X-Title": "WISE API"
    }

    payload = {
        "model": OPENROUTER_MODEL,
        "messages": [
            {
                "role": "system",
                "content": "Anda adalah asisten edukasi sampah yang ringkas, akurat, dan membantu."
            },
            {
                "role": "user",
                "content": prompt
            }
        ],
        "temperature": 0.4
    }

    response = requests.post(OPENROUTER_URL, headers=headers, json=payload, timeout=60)
    response.raise_for_status()

    data = response.json()
    return data["choices"][0]["message"]["content"]