# Sambilan: Direktori Lowongan Magang dan Kerja Paruh Waktu Mahasiswa

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Tests](https://img.shields.io/badge/Tests-Passing-10B981?style=flat-square&logo=php&logoColor=white)](tests/)

Sambilan adalah aplikasi web direktori lowongan magang dan kerja paruh waktu untuk mahasiswa. Aplikasi ini menampilkan informasi beban jam kerja per minggu, kompensasi, serta kontak langsung ke pihak penyedia lowongan.

## Konteks Akademik

Proyek ini dikembangkan untuk tugas akhir praktikum mata kuliah Pengembangan Aplikasi Web (PAW) Kelas B, Program Studi Teknologi Informasi, Fakultas Ilmu Komputer, Universitas Brawijaya (Semester Ganjil 2026/2027).

- Dosen Pengampu: Novia Ratnasari
- Asisten Praktikum: Rosyida Dimitri, Samuel Alfito Deanova

### Tim Pengembang (Kelompok 8)

| Nama | NIM | Tanggung Jawab |
| :--- | :---: | :--- |
| Kusindra Aji Rabbany | 255150701111012 | Backend (database, autentikasi, CRUD admin, automated tests) |
| Rouf Daniswara | 255150700111013 | Frontend (antarmuka publik, styling Tailwind CSS, filter pencarian) |

## Tech Stack

- Backend: Laravel 12 (PHP 8.4)
- Frontend: Blade, Tailwind CSS v4
- Database: MySQL (pengembangan), SQLite (pengujian)
- Tooling: Vite, Laravel Pint, PHPUnit

## Prasyarat Sistem

- PHP >= 8.2 (ekstensi pdo_mysql, pdo_sqlite, mbstring, openssl)
- Composer >= 2.2
- Node.js >= 18.x dan NPM
- MySQL >= 8.0

## Panduan Instalasi

1. Clone repositori:
```bash
git clone https://github.com/justpiple/sambilan.git
cd sambilan
```

2. Pasang dependensi PHP dan frontend:
```bash
composer install
npm install
```

3. Salin file konfigurasi environment dan buat application key:
```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasikan koneksi basis data pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=PRAK_sambilan
DB_USERNAME=root
DB_PASSWORD=
```
Pastikan database `PRAK_sambilan` sudah dibuat di MySQL lokal.

5. Jalankan migrasi dan seeder:
```bash
php artisan migrate --seed
```

6. Jalankan server lokal:
```bash
composer run dev
```
Atau jalankan terpisah di dua terminal:
```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Aplikasi dapat diakses melalui `http://localhost:8000`.

## Akun Demo

Untuk masuk ke panel admin:
- URL: `http://localhost:8000/login`
- Email: `admin@sambilan.id`
- Password: `password`

## Pengujian dan Format Kode

Jalankan test suite menggunakan PHPUnit:
```bash
php artisan test --compact
```

Format kode menggunakan Laravel Pint:
```bash
vendor/bin/pint
```

## Lisensi

Proyek ini dibuat untuk keperluan akademik praktikum Pengembangan Aplikasi Web di Universitas Brawijaya.
