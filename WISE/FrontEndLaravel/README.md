**Waste Identification and Sustainability Education — Frontend (Laravel)**

Ringkasan singkat:

- Proyek ini merupakan bagian frontend berbasis Laravel untuk sistem identifikasi sampah dan edukasi keberlanjutan.
- Backend model ML dan API berada di folder `FastAPI`.

Struktur penting:

- `app/` — kode aplikasi Laravel (controllers, models, dll.)
- `routes/` — definisi routing (web.php, console.php)
- `resources/` — aset frontend (views, css, js)

Menjalankan (lokal):

1. Salin file `.env.example` menjadi `.env` dan atur konfigurasi database serta `APP_URL`.
2. Install dependensi PHP:

	- Pastikan Composer terpasang, kemudian jalankan:

	  composer install

3. Generate aplikasi key:

	php artisan key:generate

4. Jalankan migrasi (jika perlu):

	php artisan migrate

5. Jalankan server lokal:

	php artisan serve

Catatan:

- Jika Anda ingin menghubungkan frontend dengan backend FastAPI, atur URL API di konfigurasi atau environment sesuai alamat server FastAPI.
- File frontend ini terintegrasi dengan model dan router FastAPI yang ada di folder `FastAPI`.

Kontak dan kontribusi:

- Untuk perubahan besar, buka issue atau pull request ke repositori utama.
- Penanggung jawab: pemilik repositori.

Lisensi: sesuaikan dengan lisensi proyek (jika tidak ada, gunakan MIT atau sesuaikan kebijakan tim).
