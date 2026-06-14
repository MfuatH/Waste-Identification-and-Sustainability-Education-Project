import os
from pathlib import Path
from dotenv import load_dotenv
import requests
from typing import Optional
import time

BASE_DIR = Path(__file__).resolve().parent.parent
DOTENV_PATHS = [
    BASE_DIR / '.env',
    BASE_DIR / 'Frontend' / '.env'
]
for dotenv_path in DOTENV_PATHS:
    if dotenv_path.exists():
        load_dotenv(dotenv_path)

OPENROUTER_API_KEY = os.getenv('OPENROUTER_API_KEY')
OPENROUTER_MODEL = os.getenv('OPENROUTER_MODEL',)
OPENROUTER_URL = 'https://openrouter.ai/api/v1/chat/completions'

# Cache untuk mengurangi API calls
_response_cache = {}

# Pemetaan jenis sampah ke rekomendasi SDGs
WASTE_CLASS_INFO = {
    "botol_plastik": {"sdgs": [12, 13, 14], "category": "Organik"},
    "botol_kaca": {"sdgs": [12, 15], "category": "Anorganik"},
    "kaleng_minuman": {"sdgs": [12, 13, 14, 15], "category": "Anorganik"},
    "kardus": {"sdgs": [6, 12], "category": "Anorganik"},
    "kertas": {"sdgs": [6, 12, 13, 15], "category": "Anorganik"},
    "bungkus_plastik_makanan": {"sdgs": [12, 14], "category": "Anorganik"},
    "cup_plastik": {"sdgs": [12, 14, 15], "category": "Anorganik"},
    "sisa_makanan": {"sdgs": [2, 12, 13], "category": "Organik"},
    "buah_sayur": {"sdgs": [2, 12, 13], "category": "Organik"},
    "pakaian": {"sdgs": [1, 10, 12], "category": "Anorganik"},
    "sepatu": {"sdgs": [1, 10, 12], "category": "Anorganik"},
    "battery": {"sdgs": [3, 6, 12, 15], "category": "E-Waste"},
    "accu": {"sdgs": [3, 6, 12], "category": "E-Waste"}
}

# Deskripsi SDGs
SDG_DESCRIPTIONS = {
    1: "Tidak ada Kemiskinan",
    2: "Tanpa Kelaparan",
    3: "Kesehatan dan Kesejahteraan",
    6: "Air Bersih dan Sanitasi",
    10: "Pengurangan Kesenjangan",
    12: "Konsumsi dan Produksi Berkelanjutan",
    13: "Penanganan Perubahan Iklim",
    14: "Kehidupan di Bawah Air",
    15: "Kehidupan di Darat"
}


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

    return f"""Anda adalah WISE AI Assistant - asisten ramah yang membantu edukasi pengelolaan sampah dan sustainability.

Konteks prediksi sampah saat ini:
{context_text}

Pesan pengguna:
{message}

PANDUAN RESPONS Anda:
1. TONE & MANNER:
   - Gunakan bahasa ramah, hangat, dan conversational
   - Panggil pengguna dengan "Kamu" atau "Anda" secara natural
   - Gunakan emoji relevant untuk membuat percakapan lebih engaging 😊
   - Hindari terkesan formal atau robotic

2. JIKA PERTANYAAN RELEVAN (tentang sampah, daur ulang, sustainability):
   - Berikan jawaban jelas, edukatif, dan actionable
   - Gunakan data/fakta jika relevan
   - Hubungkan dengan SDGs jika memungkinkan
   - Jika ada konteks prediksi, gunakan untuk rekomendasi spesifik

3. JIKA PERTANYAAN DILUAR KONTEKS (tidak sesuai dengan tema WISE):
   - Tegur dengan SOPAN dan RAMAH (jangan kaku)
   - Jelaskan bahwa WISE fokus pada edukasi sampah & sustainability
   - Tawarkan topik yang BISA kami bantu
   - Contoh topik: cara daur ulang, jenis sampah, pengolahan limbah, sustainability, SDGs, kompos, daur ulang, pengelolaan limbah elektronik, dll
   - Berakhir dengan ajakan untuk bertanya tentang topik yang sesuai

4. CONTOH TEGUR SOPAN:
   - "Hmm, sepertinya pertanyaan ini diluar fokus WISE ya 😊. Kami spesialisasi di edukasi sampah & sustainability. Mau tanya seputar cara daur ulang, pengelolaan sampah, atau sustainability?"
   - "Menarik pertanyaannya, tapi itu diluar expertise kami nih 💚. WISE fokus di waste management dan edukasi lingkungan. Ada yang ingin tahu tentang pengelolaan sampah?"

5. JIKA CONFIDENCE PREDIKSI RENDAH:
   - Sarankan pengguna upload gambar yang lebih jelas
   - Berikan tips cara mengambil foto sampah yang baik

Ingat: Setiap respons harus terasa seperti percakapan dengan teman yang peduli lingkungan, bukan bot formal!
""".strip()


def chat_with_gemma(
    message: str,
    predicted_class: Optional[str] = None,
    category: Optional[str] = None,
    confidence: Optional[float] = None
) -> str:
    if not OPENROUTER_API_KEY:
        return "⚠️ OPENROUTER_API_KEY belum diset. Silakan setting env variable terlebih dahulu."

    # Generate cache key dari message (untuk pertanyaan yang sama, gunakan cache)
    cache_key = f"{message}_{predicted_class}_{category}".lower()
    if cache_key in _response_cache:
        print(f"Using cached response for: {cache_key}")
        return _response_cache[cache_key]

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
                "content": """Kamu adalah WISE AI Assistant - teman ramah yang passionate tentang edukasi sampah dan sustainability. 
Kamu bicara natural, warm, dan engaging. Tidak formal-formal amat. Percakapan dengan kamu seperti ngobrol sama teman yang peduli lingkungan. 
Gunakan emoji, natural language, dan jangan ragu untuk menegur dengan sopan kalau pertanyaan out of scope. 
Selalu helpful dan supportive terhadap setiap usaha pengguna untuk sustainable living! 💚"""
            },
            {
                "role": "user",
                "content": prompt
            }
        ],
        "temperature": 0.7,
        "top_p": 0.9
    }

    # Retry logic untuk handle rate limit
    max_retries = 3
    retry_delay = 2  # detik
    
    for attempt in range(max_retries):
        try:
            response = requests.post(OPENROUTER_URL, headers=headers, json=payload, timeout=60)
            
            # Jika success
            if response.status_code == 200:
                data = response.json()
                result = data["choices"][0]["message"]["content"]
                # Cache the response
                _response_cache[cache_key] = result
                return result
            
            # Jika rate limited (429)
            elif response.status_code == 429:
                if attempt < max_retries - 1:
                    wait_time = retry_delay * (2 ** attempt)  # Exponential backoff
                    print(f"Rate limited. Retry dalam {wait_time} detik...")
                    time.sleep(wait_time)
                    continue
                else:
                    return "⏱️ Maaf, API sedang sibuk. Coba lagi dalam beberapa saat ya 😊"
            
            # Error lainnya
            else:
                try:
                    error_detail = response.json().get('error', {}).get('message', 'Unknown error')
                except:
                    error_detail = response.text
                return f"❌ Terjadi error: {error_detail}"
                
        except requests.exceptions.Timeout:
            return "⏱️ Request timeout. Coba lagi ya 😊"
        except requests.exceptions.ConnectionError:
            return "🌐 Koneksi error. Pastikan internet kamu stabil ya 💚"
        except Exception as e:
            print(f"Error: {str(e)}")
            return f"⚠️ Terjadi kesalahan: {str(e)}"
    
    return "⏱️ Gagal setelah beberapa kali percobaan. Coba lagi nanti 😊"


def build_recommendation_prompt(
    predicted_class: str,
    category: str,
    confidence: float
) -> str:
    """Build prompt untuk menghasilkan rekomendasi pengolahan sampah dari Gemma dengan struktur yang jelas."""
    waste_info = WASTE_CLASS_INFO.get(predicted_class.lower(), {})
    sdgs = waste_info.get("sdgs", [])
    sdg_list = ", ".join([f"SDG {sdg}: {SDG_DESCRIPTIONS.get(sdg, 'Unknown')}" for sdg in sdgs])
    readable_class = predicted_class.replace('_', ' ').title()
    
    return f"""Kamu diminta untuk memberikan REKOMENDASI PENGOLAHAN SAMPAH dalam format yang TERSTRUKTUR dan RAPI.

KONTEKS SAMPAH:
- Jenis Sampah: {readable_class}
- Kategori: {category}
- SDGs Relevan: {sdg_list}

BERIKAN RESPONS DENGAN FORMAT BERIKUT (PERSIS seperti ini):

REKOMENDASI PENGOLAHAN UNTUK SAMPAH {readable_class.upper()}:
1. [Cara Pengolahan Pertama - penjelasan singkat dan praktis]
2. [Cara Pengolahan Kedua - penjelasan singkat dan praktis]
3. [Cara Pengolahan Ketiga - penjelasan singkat dan praktis]
(Tambahkan lebih banyak nomor jika ada, maksimal 5)

DAMPAK SUSTAINABLE DEVELOPMENT GOALS (SDGs):
1. [Nomor dan nama SDG + penjelasan dampak singkat]
2. [Nomor dan nama SDG + penjelasan dampak singkat]
(Hanya untuk SDG yang relevan)

PENUTUP:
[Kalimat motivasi singkat dan ramah untuk pengguna - 1-2 kalimat saja]

PANDUAN PENULISAN:
- Gunakan bahasa yang ramah, natural, dan engaging
- Tambahkan emoji relevant di berbagai tempat
- Setiap poin harus konkret dan actionable
- Hindari penjelasan panjang-panjang - singkat dan to the point
- Gunakan Bahasa Indonesia

JANGAN TAMBAHKAN APAPUN SELAIN FORMAT DI ATAS - tidak ada intro, tidak ada kalimat tambahan di awal atau akhir!""".strip()


def get_formatted_waste_recommendation(
    predicted_class: str,
    category: str,
    confidence: float
) -> dict:
    """
    Menghasilkan rekomendasi pengolahan sampah terformat dengan struktur rapi.
    
    Returns:
        dict dengan keys: intro, recommendations, sdgs, closing, low_confidence_warning
    """
    if not OPENROUTER_API_KEY:
        return {
            "intro": "",
            "recommendations": [],
            "sdgs": [],
            "closing": "⚠️ API key tidak tersedia.",
            "low_confidence_warning": ""
        }
    
    readable_class = predicted_class.replace('_', ' ').title()
    
    # Generate cache key
    cache_key = f"formatted_recommendation_{predicted_class}_{category}".lower()
    if cache_key in _response_cache:
        print(f"Using cached formatted recommendation for: {predicted_class}")
        return _response_cache[cache_key]
    
    prompt = build_recommendation_prompt(predicted_class, category, confidence)
    
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
                "content": "Kamu adalah asisten yang ahli dalam memberikan rekomendasi pengelolaan sampah dengan struktur terorganisir, ramah, dan actionable. Selalu ikuti format yang diminta dengan tepat."
            },
            {
                "role": "user",
                "content": prompt
            }
        ],
        "temperature": 0.6,
        "top_p": 0.8
    }
    
    max_retries = 3
    retry_delay = 2
    
    for attempt in range(max_retries):
        try:
            response = requests.post(OPENROUTER_URL, headers=headers, json=payload, timeout=60)
            
            if response.status_code == 200:
                data = response.json()
                raw_response = data["choices"][0]["message"]["content"]
                
                # Parse response ke struktur yang diinginkan
                formatted_result = _parse_recommendation_response(raw_response, readable_class, confidence)
                
                # Cache the response
                _response_cache[cache_key] = formatted_result
                return formatted_result
            
            elif response.status_code == 429:
                if attempt < max_retries - 1:
                    wait_time = retry_delay * (2 ** attempt)
                    print(f"Rate limited. Retry dalam {wait_time} detik...")
                    time.sleep(wait_time)
                    continue
                else:
                    return {
                        "intro": "",
                        "recommendations": [],
                        "sdgs": [],
                        "closing": "⏱️ Server sedang sibuk. Coba lagi dalam beberapa saat ya 😊",
                        "low_confidence_warning": ""
                    }
            
            else:
                try:
                    error_detail = response.json().get('error', {}).get('message', 'Unknown error')
                except:
                    error_detail = response.text
                return {
                    "intro": "",
                    "recommendations": [],
                    "sdgs": [],
                    "closing": f"❌ Error: {error_detail}",
                    "low_confidence_warning": ""
                }
        
        except requests.exceptions.Timeout:
            return {
                "intro": "",
                "recommendations": [],
                "sdgs": [],
                "closing": "⏱️ Request timeout. Coba lagi ya 😊",
                "low_confidence_warning": ""
            }
        except requests.exceptions.ConnectionError:
            return {
                "intro": "",
                "recommendations": [],
                "sdgs": [],
                "closing": "🌐 Koneksi error. Pastikan internet stabil ya 💚",
                "low_confidence_warning": ""
            }
        except Exception as e:
            print(f"Error getting recommendation: {str(e)}")
            return {
                "intro": "",
                "recommendations": [],
                "sdgs": [],
                "closing": f"⚠️ Terjadi kesalahan: {str(e)}",
                "low_confidence_warning": ""
            }
    
    return {
        "intro": "",
        "recommendations": [],
        "sdgs": [],
        "closing": "⏱️ Gagal mengambil rekomendasi. Coba lagi nanti 😊",
        "low_confidence_warning": ""
    }


def _parse_recommendation_response(response_text: str, predicted_class: str, confidence: float) -> dict:
    """
    Parse response dari Gemma dan ekstrak ke struktur yang diinginkan.
    
    Returns:
        dict dengan: intro, recommendations (list), sdgs (list), closing, low_confidence_warning
    """
    lines = response_text.strip().split('\n')
    
    recommendations = []
    sdgs = []
    closing = ""
    intro = f"Wah selamat kamu sudah melakukan identifikasi sampah, sampah kamu adalah {predicted_class}! 🎉"
    
    current_section = None
    
    for line in lines:
        line = line.strip()
        if not line:
            continue
        
        # Deteksi section headers
        if 'REKOMENDASI' in line.upper() and 'PENGOLAHAN' in line.upper():
            current_section = 'recommendations'
            continue
        elif 'DAMPAK' in line.upper() and ('SDG' in line.upper() or 'SUSTAINABLE' in line.upper()):
            current_section = 'sdgs'
            continue
        elif 'PENUTUP' in line.upper():
            current_section = 'closing'
            continue
        
        # Parse items
        if current_section == 'recommendations' and line and line[0].isdigit() and '.' in line[:3]:
            # Extract numbered item (e.g., "1. Some text")
            item = line.split('.', 1)[1].strip() if '.' in line else line
            recommendations.append(item)
        elif current_section == 'sdgs' and line and line[0].isdigit() and '.' in line[:3]:
            # Extract SDG item
            item = line.split('.', 1)[1].strip() if '.' in line else line
            sdgs.append(item)
        elif current_section == 'closing' and line:
            closing += line + " "
    
    closing = closing.strip()
    
    # Generate low confidence warning jika perlu
    low_confidence_warning = ""
    if confidence < 0.8:
        low_confidence_warning = f"⚠️ Confidence prediksi hanya {confidence*100:.1f}%. Untuk hasil yang lebih akurat, silakan upload ulang gambar sampah dengan pencahayaan lebih jelas dan gambaran yang lebih detail. Terima kasih! 😊"
    
    return {
        "intro": intro,
        "recommendations": recommendations,
        "sdgs": sdgs,
        "closing": closing,
        "low_confidence_warning": low_confidence_warning
    }


def get_waste_recommendation(
    predicted_class: str,
    category: str,
    confidence: float
) -> str:
    """
    Menghasilkan rekomendasi pengolahan sampah spesifik menggunakan Gemma AI (legacy function).
    
    Args:
        predicted_class: Jenis sampah yang terdeteksi (e.g., 'botol_plastik')
        category: Kategori utama (e.g., 'Anorganik')
        confidence: Confidence score dari model (0-1)
    
    Returns:
        String berisi rekomendasi pengolahan dari Gemma
    """
    # Gunakan function baru dan format ke HTML string
    formatted = get_formatted_waste_recommendation(predicted_class, category, confidence)
    
    html = f"""<div class='recommendations-container'>
        <p class='intro-text'>{formatted['intro']}</p>
    """
    
    if formatted['recommendations']:
        html += "<div class='recommendations-section'>"
        html += "<h4>Rekomendasi Pengolahan untuk sampah {0}:</h4>".format(predicted_class.replace('_', ' ').title())
        html += "<ol class='recommendations-list'>"
        for rec in formatted['recommendations']:
            html += f"<li>{rec}</li>"
        html += "</ol></div>"
    
    if formatted['sdgs']:
        html += "<div class='sdgs-section'>"
        html += "<h4>Dampak SDGs:</h4>"
        html += "<ol class='sdgs-list'>"
        for sdg in formatted['sdgs']:
            html += f"<li>{sdg}</li>"
        html += "</ol></div>"
    
    if formatted['closing']:
        html += f"<p class='closing-text'>{formatted['closing']}</p>"
    
    if formatted['low_confidence_warning']:
        html += f"<div class='warning-box'><p>{formatted['low_confidence_warning']}</p></div>"
    
    html += "</div>"
    
    return html




# ============================================================================
# YOUTUBE HARDCODED LINKS - Edit these URLs dengan link YouTube yang benar
# Format: "waste_type": {"title": "Judul Video", "url": "https://youtube.com/..."}
# ============================================================================
YOUTUBE_FALLBACK = {
    "botol_plastik": {"title": "video tutorial pengolahan botol plastik", "url": "https://youtu.be/9LovD6VCa40?si=sJOwy-eed7rcs6Mw"},
    "botol_kaca": {"title": "video tutorial pengolahan botol kaca", "url": "https://youtube.com/shorts/JoI5hzQyRD0?si=4Hoaipmt69wG9eXy"},
    "kaleng_minuman": {"title": "video tutorial pengolahan kaleng minuman]", "url": "https://youtube.com/shorts/AWDSZZt6TZ0?si=IQyxMPw3EcIBdqPS"},
    "kardus": {"title": "video tutorial pengolahan kardus", "url": "https://youtu.be/3b6YMmPIydk?si=INJ9d0kHPoWOwa6t"},
    "kertas": {"title": "video tutorial pengolahan kertas", "url": "https://youtube.com/shorts/olBErEr5eTo?si=smlGzZEbOz0QFMZx"},
    "bungkus_plastik_makanan": {"title": "video tutorial pengolahan bungkus plastik", "url": "https://youtu.be/MJd3bo_XRaU?si=fXVvb58X34DCVIrc"},
    "cup_plastik": {"title": "video tutorial pengolahan cup plastik]", "url": "https://youtube.com/shorts/_zgCSFJTMpo?si=dTJawLnCDGkjrfm7"},
    "sisa_makanan": {"title": "video tutorial pengolahan sisa makanan]", "url": "https://youtu.be/0qfGNQ499JA?si=KbvzZsKepUENvBoS"},
    "buah_sayur": {"title": "video tutorial pengolahan buah sayur]", "url": "https://youtu.be/J8STpSfvkwU?si=sKu1js-NfMVLG05e"},
    "pakaian": {"title": "video tutorial pengolahan pakaian]", "url": "https://youtu.be/q-_sT1AdzPQ?si=mKvHrpL_ko47wp-S"},
    "sepatu": {"title": "video tutorial pengolahan sepatu]", "url": "https://youtu.be/kNflGgtJyLA?si=zJ4go1vvhRAenlP_"},
    "battery": {"title": "[video tutorial pengolahan battery]", "url": "https://youtu.be/8035JvuCKWw?si=dQ5FiNdIKPb5FKQC"},
    "accu": {"title": "video tutorial pengolahan accu]", "url": "https://www.youtube.com/watch?v=7JWv-nkkggc"}
}


def get_youtube_recommendation(
    predicted_class: str,
    category: str
) -> dict:
    """
    Get YouTube tutorial link untuk sampah tertentu.
    
    SIMPLE VERSION: Hanya ambil dari YOUTUBE_FALLBACK (hardcoded links).
    User dapat mengedit links langsung di YOUTUBE_FALLBACK dictionary.
    
    Args:
        predicted_class: Jenis sampah (e.g., 'botol_plastik')
        category: Kategori sampah (e.g., 'Anorganik')
    
    Returns:
        dict dengan keys: title, url
    """
    
    predicted_class_lower = predicted_class.lower()
    
    # Jika ada di YOUTUBE_FALLBACK, return langsung
    if predicted_class_lower in YOUTUBE_FALLBACK:
        link = YOUTUBE_FALLBACK[predicted_class_lower]
        # Cek apakah user sudah isi atau masih template
        if "[ATUR:" in link.get("url", ""):
            print(f"⚠️ YouTube link untuk '{predicted_class}' belum diatur - masih template")
            return {
                "title": f"Silakan atur tutorial untuk {predicted_class}",
                "url": "#"
            }
        return link
    
    # Jika tidak ada di dictionary
    print(f"⚠️ Waste type '{predicted_class}' tidak ada di YOUTUBE_FALLBACK")
    return {
        "title": "Tutorial tidak tersedia",
        "url": "#"
    }


