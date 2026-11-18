# Sistem Presensi Sekolah

PHP Native + MySQL (XAMPP) + Composer.

## Setup XAMPP

1. Install XAMPP (PHP 8.2+), pastikan Apache & MySQL jalan.
2. Clone/copy proyek ke `C:\xampp\htdocs\presensi`.
3. `cd C:\xampp\htdocs\presensi && composer install`
4. Copy `.env.example` → `.env`, sesuaikan DB.
5. Buat database `presensi` di phpMyAdmin.
6. Import SQL berurutan:
   - `database/migrations/001_fondasi.sql`
   - `database/migrations/002_master.sql`
   - `database/migrations/003_presensi.sql`
   - `database/migrations/004_delegasi_remote_qr.sql`
   - `database/migrations/000_seed.sql`
7. Generate hash password valid → ganti di `users.password_hash` (lihat catatan di seed).
8. Pastikan `mod_rewrite` Apache aktif.
9. Buka `http://localhost/presensi/public`.

## Login default

- Admin: `admin` / `admin123`
- Guru:  `guru1` / `guru123`
- Siswa: `siswa1` / `01012010` (wajib ganti saat login pertama)

## Struktur

Lihat folder `app/core/` (Router, DB, Auth, dll), `app/controllers/`, `app/views/`.

## Catatan

- Document root = `public/`.
- Semua query wajib filter `school_id`.
- CSRF wajib di semua form POST.