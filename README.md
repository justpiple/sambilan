# Sambilan — Direktori Lowongan Magang & Kerja Paruh Waktu Mahasiswa

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![PHPUnit 100% Passed](https://img.shields.io/badge/Tests-100%25_Passing-10B981?style=flat-square&logo=php&logoColor=white)](tests/)
[![Laravel Pint](https://img.shields.io/badge/Code_Style-Laravel_Pint-blue?style=flat-square)](https://laravel.com/docs/pint)

> **Sambilan** adalah platform direktori satu pintu (*one-stop curated directory*) berbasis web yang menyajikan katalog lowongan magang (*internship*) dan kerja paruh waktu (*part-time*) yang terkurasi khusus untuk mahasiswa aktif. Dirancang dengan transparansi jam kerja per minggu, kejelasan kompensasi, dan kemudahan akses langsung ke mitra kampus tanpa perantara birokrasi yang rumit.

---

## 📌 Konteks Akademik & Tim Pengembang

Proyek ini dikembangkan sebagai karya tugas akhir praktikum mata kuliah **Pengembangan Aplikasi Web (PAW) — Kelas B**, Program Studi Teknologi Informasi, Fakultas Ilmu Komputer, **Universitas Brawijaya** (Semester Ganjil 2026/2027).

- **Dosen Pengampu**: Novia Ratnasari
- **Asisten Praktikum**:
  - Rosyida Dimitri (NIM: 245150201111014)
  - Samuel Alfito Deanova (NIM: 235150207111022)

### Pembagian Peran & Kontributor Kelompok 8

| Foto / Inisial | Nama Lengkap | NIM | Peran Utama & Tanggung Jawab |
| :---: | :--- | :---: | :--- |
| **Beni** | **Kusindra Aji Rabbany** | `255150701111012` | **Backend Engineer & Architect**: Perancangan skema basis data, model domain Eloquent, aturan bisnis & integritas data, autentikasi administrator, CRUD admin (Perusahaan, Kategori, Lowongan), seeder master data, dan automated test suite. |
| **Wara** | **Rouf Daniswara** | `255150700111013` | **Frontend Engineer & UI/UX Craft**: Perancangan antarmuka publik, design token Tailwind CSS v4 (*Warm Persimmon*, *Obsidian Ink*, *Forest Emerald*), filter multi-kriteria, detail lowongan, tombol kontak WhatsApp/Email, dan optimasi mobile responsif 360px. |

---

## 💡 Latar Belakang & Masalah yang Diselesaikan

Mahasiswa aktif sering mengalami kendala saat mencari pekerjaan sampingan atau magang:
1. **Ketidaksesuaian Beban Waktu**: Informasi lowongan umum di portal karier jarang mencantumkan estimasi beban jam per minggu, sehingga berisiko mengganggu performa akademik kuliah.
2. **Informasi Kedaluwarsa & Tersebar**: Informasi lowongan di media sosial sering kali sudah lewat tenggat pendaftaran (*expired*) tanpa penandaan yang jelas.
3. **Kompensasi Tidak Transparan**: Ketidakjelasan uang saku atau sistem magang non-kompensasi membuat ekspektasi mahasiswa pelamar tidak selaras.
4. **Beban Administrasi bagi Mitra**: UMKM dan instansi mitra lokal membutuhkan saluran publikasi langsung yang cepat tanpa keharusan mengelola portal pelamar (*applicant tracking*) yang berat.

**Solusi Sambilan**:
- Menghadirkan katalog publik terbuka yang tidak mewajibkan pendaftaran akun bagi mahasiswa (model *direct redirect* ke sistem pendaftaran resmi atau narahubung WhatsApp/Email).
- Menampilkan estimasi beban jam kerja per minggu (`jam_per_minggu`) dan label format kompensasi yang transparan (*Rupiah* atau *Non-kompensasi*).
- Menjalankan isolasi otomatis (*soft-filtering*) untuk lowongan kedaluwarsa via query scope `scopeAktif`.

---

## 🛠️ Tech Stack & Spesifikasi Arsitektur

Aplikasi dibangun menggunakan arsitektur **Fullstack Monolith** berbasis *Server-Side Rendering* (SSR) yang cepat, kokoh, dan mudah di-maintain:

- **Backend Framework**: [Laravel 12](https://laravel.com/) (PHP 8.4)
- **Frontend & Templating**: [Blade Template Engine](https://laravel.com/docs/blade) dengan arsitektur komponen (`<x-job-card>`, `<x-badge>`, `<x-alert>`)
- **Styling & Design System**: [Tailwind CSS v4](https://tailwindcss.com/) dengan palet kustom:
  - *Warm Persimmon* (`#c2410c` / `orange-700`) — Aksen identitas dinamis mahasiswa
  - *Obsidian Ink* (`#0f172a` / `slate-900`) — Tipografi struktural dan dashboard pengelola
  - *Forest Emerald* (`#047857` / `emerald-700`) — Penanda kompensasi finansial transparan
  - Standar aksesibilitas WCAG AA (kontras teks tinggi & target sentuh minimum 44px)
- **Basis Data**:
  - **MySQL 8.0+** untuk lingkungan pengembangan lokal (`PRAK_sambilan`)
  - **SQLite In-Memory** (`:memory:`) untuk eksekusi automated test suite super cepat
- **Code Formatter & Linter**: [Laravel Pint](https://laravel.com/docs/pint)
- **Asset Bundler**: Vite 6

---

## ⚙️ Aturan Bisnis & Integritas Data

Platform Sambilan menerapkan tiga prinsip utama untuk menjaga kualitas data direktori:

### 1. Otomatisasi Isolasi Lowongan Kedaluwarsa (`scopeAktif`)
- Lowongan yang ditampilkan pada katalog publik mahasiswa secara ketat difilter menggunakan query scope `Lowongan::scopeAktif()`.
- Syarat lowongan aktif: `status = 'buka'` **DAN** `tenggat >= TODAY()`.
- Lowongan yang melewati tanggal tenggat atau diubah statusnya menjadi `ditutup` oleh admin otomatis dieliminasi dari halaman katalog publik mahasiswa.

### 2. Validasi Batas Waktu Pendaftaran Masa Depan (`LowonganRequest`)
- Pengelola dilarang menginputkan lowongan dengan tanggal tenggat masa lampau.
- Form Request memvalidasi input `tenggat` dengan aturan `required|date|after_or_equal:today`.

### 3. Perlindungan Integritas Relasional Hapus Data (`Relational Deletion Protection`)
- Perusahaan atau Kategori yang masih memiliki relasi data lowongan dilindungi dari penghapusan tidak disengaja.
- `PerusahaanController::destroy` dan `KategoriController::destroy` memverifikasi keberadaan data lowongan terkait (`count > 0`). Jika masih ada, sistem membatalkan penghapusan dan mengirimkan pesan kesalahan informatif ke admin.

---

## 📋 Prasyarat Sistem (Prerequisites)

Sebelum menjalankan aplikasi, pastikan perangkat lokal Anda telah memenuhi spesifikasi berikut:

- **PHP**: `>= 8.2` (sangat direkomendasikan PHP `8.4+`) beserta ekstensi `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`.
- **Composer**: `>= 2.2`
- **Node.js**: `>= 18.x` & **NPM**: `>= 9.x`
- **MySQL**: `>= 8.0` (atau MariaDB `>= 10.4`)

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

Ikuti panduan langkah demi langkah berikut untuk memasang Sambilan di komputer lokal Anda:

### 1. Clone Repository
```bash
git clone https://github.com/justpiple/sambilan.git
cd sambilan
```

### 2. Pasang Dependensi PHP & Node.js
```bash
# Install paket dependensi PHP via Composer
composer install

# Install paket dependensi frontend via NPM
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi contoh dan buat kunci enkripsi aplikasi:
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan pengaturan koneksi basis data MySQL lokal Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=PRAK_sambilan
DB_USERNAME=root
DB_PASSWORD=
```
> *Catatan:* Pastikan database MySQL dengan nama `PRAK_sambilan` sudah dibuat sebelumnya di MySQL lokal Anda (`CREATE DATABASE PRAK_sambilan;`).

### 4. Eksekusi Migrasi & Master Data Seeder
Jalankan migrasi skema tabel beserta seeder data mitra, kategori, akun administrator, dan contoh lowongan realistis:
```bash
php artisan migrate --seed
```

### 5. Kompilasi Aset Frontend & Jalankan Server
Jalankan backend server dan Vite development watcher secara bersamaan:
```bash
composer run dev
```
Atau jalankan pada dua terminal terpisah:
```bash
# Terminal 1: Laravel Web Server
php artisan serve

# Terminal 2: Vite Assets Bundler
npm run dev
```

Buka peramban (*browser*) Anda pada tautan: [`http://localhost:8000`](http://localhost:8000)

---

## 🔐 Kredensial Akun Demonstrasi (Demo Admin)

Untuk mengakses modul administrasi, gunakan kredensial berikut:

| Parameter | Kredensial |
| :--- | :--- |
| **URL Login** | [`http://localhost:8000/login`](http://localhost:8000/login) |
| **Email** | `admin@sambilan.id` |
| **Kata Sandi** | `password` |
| **Role Akun** | `admin` |

---

## 🧭 Panduan Pengguna

### A. Panduan Mahasiswa (Katalog Publik)
1. **Menjelajahi Katalog Lowongan**:
   - Buka halaman utama `http://localhost:8000/` atau `http://localhost:8000/katalog`.
   - Lowongan ditampilkan dalam grid kartu responsif dengan paginasi **9 lowongan per halaman**.
   - Setiap kartu menampilkan nama mitra, judul posisi, badge tipe (Magang / Paruh Waktu), badge sistem kerja (Onsite / Remote / Hybrid), nominal kompensasi, jam per minggu, serta sisa hari batas pendaftaran.
2. **Pencarian Kata Kunci**:
   - Ketik kata kunci pada bilah pencarian (mencakup judul pekerjaan dan nama perusahaan mitra).
   - Tekan enter atau tombol cari. Hasil yang muncul otomatis diisolasi hanya untuk lowongan yang masih aktif.
3. **Penyaringan Multi-Kriteria**:
   - Gunakan filter dropdown Kategori, Tipe Pekerjaan, dan Sistem Kerja.
   - Pilihan filter tetap dipertahankan saat berpindah halaman paginasi (*query string persistence*).
4. **Halaman Detail Lowongan**:
   - Klik kartu lowongan untuk membuka rincian lengkap tugas, kualifikasi persyaratan, profil perusahaan, dan narahubung.
   - Klik tombol **Lamar Sekarang** untuk diarahkan ke situs pendaftaran resmi mitra.
   - Klik tombol **WhatsApp Narahubung** atau **Email Narahubung** untuk berkonsultasi langsung dengan bagian rekrutmen mitra.

### B. Panduan Pengelola (Administrator)
1. **Dasbor Statistik**:
   - Setelah login, pengelola disuguhkan ringkasan kartu metrik: Total Perusahaan Mitra, Total Kategori Bidang, dan Total Lowongan Aktif saat ini.
2. **Manajemen Master Data Perusahaan**:
   - Tambah, tinjau, ubah profil, dan hapus instansi mitra kampus.
   - Dilindungi proteksi relasional agar perusahaan yang masih memiliki data lowongan tidak terhapus secara sengaja.
3. **Manajemen Kategori Bidang**:
   - Tambah dan kelola taksonomi bidang profesi (Software Engineering, UI/UX, Data, dll.). Dilindungi proteksi relasional jika masih digunakan lowongan.
4. **Manajemen Publikasi Lowongan**:
   - Buat lowongan baru dengan validasi tenggat waktu masa depan.
   - Ubah status lowongan menjadi `ditutup` secara instan bila kuota rekrutmen terpenuhi, yang otomatis menyembunyikannya dari katalog mahasiswa.

---

## 🧪 Eksekusi Pengujian Otomatis & Standar Kode

### 1. Menjalankan Automated Test Suite (PHPUnit)
Seluruh fungsionalitas diuji secara otomatis menggunakan basis data SQLite in-memory untuk memastikan keandalan 100% tanpa risiko manipulasi data lokal:
```bash
# Menjalankan seluruh test suite secara ringkas
php artisan test --compact

# Menjalankan spesifik End-to-End (E2E) Acceptance Tests
php artisan test --compact tests/Feature/E2E/

# Menjalankan pengujian aturan bisnis dan validasi
php artisan test --compact tests/Feature/BusinessRules/
```

### 2. Memeriksa Standar Gaya Kode (Laravel Pint)
Proyek ini mematuhi standar PSR-12 dan panduan resmi Laravel menggunakan Laravel Pint:
```bash
# Memformat seluruh file PHP pada proyek
vendor/bin/pint --format agent

# Menguji kepatuhan formatting tanpa mengubah file
vendor/bin/pint --test
```

---

## 📄 Lisensi & Hak Cipta

Proyek ini dikembangkan untuk tujuan edukasi dan akademik dalam rangka pemenuhan Tugas Akhir Mata Kuliah **Pengembangan Aplikasi Web** di **Universitas Brawijaya**, 2026. Seluruh materi hak cipta dilindungi oleh tim pengembang (Kelompok 8 PAW-B).
