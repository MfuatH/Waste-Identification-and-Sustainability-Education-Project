# Waste Identification and Sustainability Education (WISE)

Deskripsi singkat:

- Proyek ini menggabungkan model Machine Learning (FastAPI) dan antarmuka web (Laravel) untuk mengidentifikasi jenis sampah dan memberikan edukasi keberlanjutan.

Persyaratan singkat:

- Python 3.8+ untuk backend
- PHP 8+ dan Composer untuk frontend
- Wajib install tensorflow 2.15

Cara menjalankan (lokal):

1) Backend (FastAPI)

- Buka terminal, masuk ke folder `FastAPI`.
- Buat virtual environment dan install dependensi:

  python -m venv .venv
  .venv\Scripts\activate
  pip install -r requirements.txt

- Jalankan server:

  uvicorn main:app --reload --port 8001

2) Frontend (Laravel)

- Buka terminal, masuk ke folder `FrontEndLaravel`.
- Salin environment dan install dependensi:

  copy .env.example .env
  composer install

- Generate app key dan jalankan:

  php artisan key:generate
  php artisan migrate   # opsional bila menggunakan database
  php artisan serve

Integrasi:

- Pastikan frontend mengakses API backend (default: `http://localhost:8000`). Sesuaikan variabel environment di `FrontEndLaravel/.env` jika perlu.

Itu saja: ringkas deskripsi proyek dan langkah menjalankan backend serta frontend.
