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


def build_youtube_prompt(
    predicted_class: str,
    category: str
) -> str:
    """Build prompt untuk mencari YouTube link rekomendasi edukasi sampah dari Gemma."""
    readable_class = predicted_class.replace('_', ' ').title()
    
    return f"""TASK: Carilah 1 video YouTube TERBAIK untuk edukasi tentang: {readable_class} ({category})

CRITICAL REQUIREMENTS:
1. Video PASTI masih TERSEDIA & AKTIF (bukan deleted/private/unavailable)
2. Channel TERPERCAYA: Government, NGO resmi, Universitas, TED-Ed, BBC Learning, National Geographic, dsb
3. Video punya 10K+ views (bukti berkualitas)
4. Preferensi: Bahasa Indonesia atau Inggris
5. JANGAN asal-asalan - lebih baik tidak ada link daripada link yang salah!

TRUSTED CHANNELS EXAMPLES:
- TED-Ed Bahasa Indonesia
- National Geographic Indonesia
- Government Environmental Agencies
- Reputable Educational Channels
- University or NGO official channels

RESPONS FORMAT - EXACTLY 2 LINES:
[Line 1] Video Title (dari channel resmi)
[Line 2] Link (https://www.youtube.com/watch?v=11CHARVIDEOEXAMPLE atau tidak ada)

CRITICAL RULES:
✓ Jika YAKIN ada video berkualitas tinggi: berikan link-nya
✓ Jika TIDAK YAKIN / TIDAK TAHU: return "I'm not confident" di line 2 (NO GUESSING!)
✓ Video ID = 11 karakter (mix of upper/lower/digit/special chars)
✓ JANGAN PERNAH generate/hallucinate video ID yang tidak pasti!
✓ Prioritas: video publikasi 2+ tahun lalu (established, stable)

EXAMPLE GOOD RESPONSE:
How to Recycle Plastic Properly - Educational Tutorial
https://www.youtube.com/watch?v=dQw4w9WgXcQ

EXAMPLE BAD RESPONSE (Jangan lakukan ini!):
Cara Daur Ulang Plastik
https://www.youtube.com/watch?v=randomstringhere (← WRONG! No guessing!)

Remember: CONFIDENCE FIRST. If you're not sure, say so!""".strip()


# Fallback YouTube links - hanya digunakan jika Gemma gagal
# Ini adalah safety net agar user tetap dapat akses tutorial
YOUTUBE_FALLBACK = {
    "botol_plastik": {"title": "🔍 Cari: Daur Ulang Botol Plastik", "url": "https://www.youtube.com/results?search_query=cara+daur+ulang+botol+plastik"},
    "botol_kaca": {"title": "🔍 Cari: Daur Ulang Botol Kaca", "url": "https://www.youtube.com/results?search_query=cara+daur+ulang+botol+kaca"},
    "kaleng_minuman": {"title": "🔍 Cari: Daur Ulang Kaleng", "url": "https://www.youtube.com/results?search_query=daur+ulang+kaleng+minuman"},
    "kardus": {"title": "🔍 Cari: Daur Ulang Kardus", "url": "https://www.youtube.com/results?search_query=cara+daur+ulang+kardus"},
    "kertas": {"title": "🔍 Cari: Daur Ulang Kertas", "url": "https://www.youtube.com/results?search_query=cara+daur+ulang+kertas"},
    "bungkus_plastik_makanan": {"title": "🔍 Cari: Mengurangi Plastik Makanan", "url": "https://www.youtube.com/results?search_query=reduce+plastik+makanan"},
    "cup_plastik": {"title": "🔍 Cari: Daur Ulang Cup Plastik", "url": "https://www.youtube.com/results?search_query=daur+ulang+cup+plastik"},
    "sisa_makanan": {"title": "🔍 Cari: Kompos Limbah Makanan", "url": "https://www.youtube.com/results?search_query=membuat+kompos+limbah+makanan"},
    "buah_sayur": {"title": "🔍 Cari: Kompos Buah Sayur", "url": "https://www.youtube.com/results?search_query=kompos+buah+sayur+busuk"},
    "pakaian": {"title": "🔍 Cari: Daur Ulang Pakaian", "url": "https://www.youtube.com/results?search_query=ide+daur+ulang+pakaian+bekas"},
    "sepatu": {"title": "🔍 Cari: Daur Ulang Sepatu", "url": "https://www.youtube.com/results?search_query=cara+daur+ulang+sepatu+bekas"},
    "battery": {"title": "🔍 Cari: Daur Ulang Baterai", "url": "https://www.youtube.com/results?search_query=pentingnya+daur+ulang+baterai"},
    "accu": {"title": "🔍 Cari: Pengelolaan Limbah Accu", "url": "https://www.youtube.com/results?search_query=pengelolaan+limbah+accu+kendaraan"}
}


def get_youtube_recommendation(
    predicted_class: str,
    category: str
) -> dict:
    """
    Mencari YouTube link rekomendasi edukasi sampah dari Gemma.
    
    Args:
        predicted_class: Jenis sampah (e.g., 'botol_plastik')
        category: Kategori sampah (e.g., 'Anorganik')
    
    Returns:
        dict dengan keys: title, url
    """
    
    if not OPENROUTER_API_KEY:
        print("No API key for YouTube recommendation")
        return {
            "title": "Tutorial Pengelolaan Sampah",
            "url": "#"
        }
    
    # Cache check
    cache_key = f"youtube_{predicted_class}_{category}".lower()
    if cache_key in _response_cache:
        print(f"Using cached YouTube link for: {predicted_class}")
        return _response_cache[cache_key]
    
    prompt = build_youtube_prompt(predicted_class, category)
    
    headers = {
        "Authorization": f"Bearer {OPENROUTER_API_KEY}",
        "Content-Type": "application/json",
        "HTTP-Referer": "http://localhost",
        "X-Title": "WISE API"
    }
    
    # System message dengan knowledge base tentang popular educational channels
    system_message = """Kamu adalah AI ahli yang mencari video YouTube berkualitas tinggi tentang pengelolaan sampah & sustainability.

PENTING - CRITICAL RULES:
1. HANYA berikan link video yang PASTI 100% MASIH TERSEDIA dan AKTIF
2. Video harus dari channel TERPERCAYA: Official Government, NGO, Universitas, Channel Edukasi terkenal
3. Jika TIDAK YAKIN video masih ada/aktif, JANGAN berikan link tersebut!
4. Better to return NOTHING than return a BROKEN link

KNOWN TRUSTED CHANNELS & TOPICS:
- Waste management channels: TED-Ed, National Geographic, BBC Learning, Crash Course
- Local waste management: Government environmental agencies, Local NGOs
- Recycling tutorials: Popular DIY channels dengan subscriber banyak

VIDEO QUALITY CRITERIA:
- Harus punya 10K+ views (bukti video berkualitas)
- Harus dari channel resmi atau terkenal
- Publikasi minimal 2 tahun lalu (cukup stable)
- Rating positif & banyak like

RESPON KAMU:
Jika YAKIN ada video yang cocok:
  Format: [Title]
          https://www.youtube.com/watch?v=VIDEOID

Jika TIDAK YAKIN / TIDAK TAHU:
  Jangan asal-asalan berikan link!
  Return: [I'm not confident]
          #

JANGAN PERNAH memberikan link yang tidak yakin!"""
    
    payload = {
        "model": OPENROUTER_MODEL,
        "messages": [
            {
                "role": "system",
                "content": system_message
            },
            {
                "role": "user",
                "content": prompt
            }
        ],
        "temperature": 0.0,  # Deterministic - no randomness
        "top_p": 1.0,
        "top_k": 1
    }
    
    max_retries = 3
    retry_delay = 1
    
    for attempt in range(max_retries):
        try:
            response = requests.post(OPENROUTER_URL, headers=headers, json=payload, timeout=30)
            
            if response.status_code == 200:
                data = response.json()
                raw_response = data["choices"][0]["message"]["content"].strip()
                
                # Parse response: expect 2 lines (title + url)
                lines = [line.strip() for line in raw_response.split('\n') if line.strip()]
                
                if len(lines) >= 2:
                    title = lines[0]
                    url = lines[1]
                    
                    # Skip jika Gemma bilang "not confident"
                    if "not confident" in url.lower() or url == "#":
                        print(f"Gemma not confident for: {predicted_class}")
                        return {
                            "title": title if title else "Sedang mencari tutorial terbaik",
                            "url": "#"
                        }
                    
                    # Validate URL adalah YouTube link yang valid
                    if _is_valid_youtube_url_strict(url):
                        result = {"title": title, "url": url}
                        _response_cache[cache_key] = result
                        print(f"✓ Got valid YouTube link from Gemma for: {predicted_class}")
                        return result
                    else:
                        print(f"✗ Gemma gave invalid/hallucinated URL: {url}")
                        # Retry with same attempt (tidak count sebagai failed attempt)
                        if attempt < max_retries - 1:
                            print(f"  Retrying (attempt {attempt + 1}/{max_retries})...")
                            time.sleep(retry_delay)
                            continue
                        else:
                            # Jika semua retry fail, return safe message
                            return {
                                "title": "Tutorial Pengelolaan Sampah",
                                "url": "#"
                            }
                
                # Jika parsing gagal
                print(f"✗ Gemma response invalid for: {predicted_class}")
                if attempt < max_retries - 1:
                    print(f"  Retrying (attempt {attempt + 1}/{max_retries})...")
                    time.sleep(retry_delay)
                    continue
                else:
                    return {
                        "title": "Tutorial Pengelolaan Sampah",
                        "url": "#"
                    }
            
            elif response.status_code == 429:
                if attempt < max_retries - 1:
                    wait_time = retry_delay * (2 ** attempt)
                    print(f"⚠️ Rate limited. Retry dalam {wait_time}s...")
                    time.sleep(wait_time)
                    continue
                else:
                    print("⚠️ Rate limited - all retries exhausted")
                    return {
                        "title": "API sedang overload",
                        "url": "#"
                    }
            
            else:
                print(f"✗ API error {response.status_code}")
                if attempt < max_retries - 1:
                    time.sleep(retry_delay)
                    continue
                else:
                    return {
                        "title": "Terjadi kesalahan",
                        "url": "#"
                    }
        
        except requests.exceptions.Timeout:
            print("⚠️ Timeout")
            if attempt < max_retries - 1:
                time.sleep(retry_delay)
                continue
            else:
                return {
                    "title": "Timeout - silakan coba lagi",
                    "url": "#"
                }
        except requests.exceptions.ConnectionError:
            print("⚠️ Connection error")
            if attempt < max_retries - 1:
                time.sleep(retry_delay)
                continue
            else:
                return {
                    "title": "Koneksi error",
                    "url": "#"
                }
        except Exception as e:
            print(f"✗ Error: {str(e)}")
            if attempt < max_retries - 1:
                time.sleep(retry_delay)
                continue
            else:
                return {
                    "title": "Error mencari tutorial",
                    "url": "#"
                }
    
    # Jika semua Gemma attempts fail, fallback ke hardcoded links
    print(f"⏹️ All Gemma attempts failed for: {predicted_class}. Using hardcoded fallback.")
    if predicted_class.lower() in YOUTUBE_FALLBACK:
        fallback = YOUTUBE_FALLBACK[predicted_class.lower()]
        print(f"   → Fallback: {fallback['title']}")
        return fallback
    
    # Last resort: generic message
    return {
        "title": "Tutorial Pengelolaan Sampah",
        "url": "#"
    }


def _try_get_youtube_from_gemma(
    predicted_class: str,
    category: str
) -> Optional[dict]:
    """
    [DEPRECATED - use get_youtube_recommendation directly]
    """
    return None


def _is_valid_youtube_url_strict(url: str) -> bool:
    """
    Strict validation untuk YouTube URL.
    Extract & validate video ID untuk detect hallucination.
    
    Args:
        url: YouTube URL untuk divalidasi
    
    Returns:
        True jika URL valid dan video ID terlihat legitimate
    """
    import re
    
    if not url or url == "#":
        return False
    
    url_lower = url.lower()
    
    # Must start with https:// atau http://
    if not url.startswith(('https://', 'http://')):
        print(f"  [VALIDATION] Invalid URL scheme: {url}")
        return False
    
    # Must contain youtube domain
    if 'youtube' not in url_lower and 'youtu.be' not in url_lower:
        print(f"  [VALIDATION] Not a YouTube URL: {url}")
        return False
    
    # Extract video ID
    video_id = None
    
    # Try pattern: youtube.com/watch?v=ID
    match = re.search(r'youtube\.com/watch\?v=([\w-]{11})', url)
    if match:
        video_id = match.group(1)
    
    # Try pattern: youtu.be/ID
    if not video_id:
        match = re.search(r'youtu\.be/([\w-]{11})', url)
        if match:
            video_id = match.group(1)
    
    # Try pattern: youtube.com/embed/ID
    if not video_id:
        match = re.search(r'youtube\.com/embed/([\w-]{11})', url)
        if match:
            video_id = match.group(1)
    
    # Video ID harus ditemukan dan harus 11 karakter
    if not video_id or len(video_id) != 11:
        print(f"  [VALIDATION] Invalid video ID length: {url}")
        return False
    
    # Video ID hanya boleh alphanumeric, underscore, dash
    if not re.match(r'^[\w-]{11}$', video_id):
        print(f"  [VALIDATION] Invalid video ID chars: {video_id}")
        return False
    
    # Check if it looks like a REAL YouTube video ID (character variety heuristic)
    # Real YouTube IDs biasanya punya mix of upper, lower, digit, special chars
    has_upper = any(c.isupper() for c in video_id)
    has_lower = any(c.islower() for c in video_id)
    has_digit = any(c.isdigit() for c in video_id)
    has_special = any(c in '-_' for c in video_id)
    
    # Character variety score (0-4)
    variety_score = sum([has_upper, has_lower, has_digit, has_special])
    
    # Real YouTube IDs biasanya punya minimal 2 kombinasi
    # Tapi relax sedikit - 1 kombinasi juga OK (misal hanya lowercase + digit)
    # Yang important adalah tidak SEMUA sama (all lowercase OR all uppercase)
    if variety_score < 1:
        print(f"  [VALIDATION] Suspicious ID (no variety): {video_id}")
        return False
    
    # Warn jika hanya 1 variety tapi still allow
    if variety_score == 1:
        print(f"  [VALIDATION] ⚠️ Low variety ID (still allowing): {video_id}")
    else:
        print(f"  [VALIDATION] ✓ Valid video ID: {video_id} (variety: {variety_score})") 
    
    return True