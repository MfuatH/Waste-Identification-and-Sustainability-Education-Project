# Waste Identification and Sustainability Education Project (WISE)

Proyek WISE menggabungkan sebuah backend ML berbasis FastAPI dan frontend berbasis Laravel untuk mengidentifikasi jenis sampah dan memberikan edukasi keberlanjutan.

Folder utama:

- `FastAPI/` — backend Python, model ML, endpoint prediksi.
- `FrontEndLaravel/` — antarmuka web Laravel untuk interaksi pengguna.

Mulai cepat (lokal):

1. Backend (FastAPI):

   - Masuk ke folder `FastAPI`.
   - Buat environment Python dan install dependensi:

     python -m venv .venv
     .venv\Scripts\activate
     pip install -r requirements.txt

   - Jalankan server:

     uvicorn main:app --reload --port 8000

2. Frontend (Laravel):

   - Masuk ke folder `FrontEndLaravel`.
   - Salin `.env.example` ke `.env`, lalu jalankan:

     composer install
     php artisan key:generate
     php artisan migrate
     php artisan serve

Pengaturan integrasi:

- Pastikan URL API FastAPI (`http://localhost:8000` secara default) tersedia untuk frontend.
- Model ML dan file terkait berada di `FastAPI/models/`.

Push ke GitHub:

- Branch target: `WiseProject` (branch akan dibuat dan dipush ke remote yang Anda berikan).

Jika Anda ingin saya melakukan push sekarang, beri konfirmasi (saya akan membuat branch `WiseProject`, commit perubahan README, dan push ke `https://github.com/MfuatH/Waste-Identification-and-Sustainability-Education-Project.git`).
