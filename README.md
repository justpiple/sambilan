# Sambilan

Project Akhir Mata Kuliah **Pengembangan Aplikasi Web**  
**Universitas Brawijaya**

---

## 📌 Tentang Project

Repository ini merupakan project akhir untuk mata kuliah **Pengembangan Aplikasi Web** di **Universitas Brawijaya**.

> **Catatan:** Deskripsi lengkap serta spesifikasi detail aplikasi akan diperbarui segera (*menyusul*).

---

## 🛠️ Tech Stack & Arsitektur

Aplikasi ini dibangun menggunakan arsitektur **Fullstack Monolith** berbasis Server-Side Rendering (SSR):

- **Backend Framework:** [Laravel](https://laravel.com/)
- **Frontend / Templating:** [Laravel Blade](https://laravel.com/docs/blade) (menggunakan Blade Components & Layouts)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Database:** [MySQL](https://www.mysql.com/)
- **Asset Bundler:** [Vite](https://vitejs.dev/)
- **Package Manager:** Composer (PHP) & NPM (Node.js)

---

## 📋 Prasyarat Sistem (Prerequisites)

Pastikan lingkungan lokal Anda telah terpasang:

- **PHP** >= 8.2 (disarankan PHP 8.3+) beserta ekstensi yang dibutuhkan (`pdo_mysql`, `mbstring`, `openssl`, `curl`, dll.)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **MySQL** >= 8.0 (atau MariaDB)

---

## 🚀 Panduan Instalasi (Local Development Setup)

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal:

### 1. Clone Repository
```bash
git clone https://github.com/justpiple/sambilan.git
cd sambilan
```

### 2. Install Dependensi
```bash
# Install dependensi PHP
composer install

# Install dependensi frontend
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Buka file `.env` dan sesuaikan kredensial database MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sambilan
DB_USERNAME=root
DB_PASSWORD=
```
> Pastikan database MySQL dengan nama yang Anda masukkan (misal: `sambilan`) sudah dibuat terlebih dahulu.

### 4. Migrasi Database
Jalankan migrasi database untuk membuat tabel-tabel yang diperlukan:
```bash
php artisan migrate
```

### 5. Menjalankan Server Pengembangan

Anda dapat menjalankan server backend dan Vite secara bersamaan dengan perintah:
```bash
composer run dev
```

Atau jalankan secara terpisah di dua terminal berbeda:
```bash
# Terminal 1 - Backend Server
php artisan serve

# Terminal 2 - Vite Assets Watcher
npm run dev
```

Aplikasi dapat diakses melalui browser pada: [`http://localhost:8000`](http://localhost:8000)

---

## 👥 Tim Pengembang

- Kusindra Aji Rabbany (255150701111012)
- Rouf Daniswara - (255150700111013)

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik pada mata kuliah Pengembangan Aplikasi Web, Universitas Brawijaya.
