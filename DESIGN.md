# Panduan Desain Sambilan (DESIGN.md)

Dokumen ini adalah acuan resmi identitas visual, perancangan warna brand, tipografi, dan tata letak platform **Sambilan: Direktori Lowongan Magang & Kerja Paruh Waktu Mahasiswa**. Setiap halaman dan komponen antarmuka wajib mengacu pada panduan ini.

---

## 1. Perancangan Brand & Filosofi Visual

### Masalah Warna Klise AI (*Anti-AI Slop*)
* Sebagian besar alat AI secara otomatis menghasilkan warna biru korporat (`#2563eb` / `#1d4ed8`) atau ungu SaaS (`#6366f1` / `#7c3aed`). Warna-warna tersebut membuat antarmuka terasa dingin, kaku, generik, dan membosankan seperti formulir administrasi lama.
* *Sambilan* adalah platform mahasiswa aktif (kuliah sambil magang/kerja). Mahasiswa membutuhkan antarmuka yang memancarkan **energi nyata, kehangatan, kemandirian finansial, dan optimisme**, tanpa mengorbankan standar profesionalitas.

### Referensi Riil Industri Non-Slop (Benchmark Internet)
Mengadopsi pola warna dari platform rekrutmen & direktori karier modern peraih penghargaan (seperti *Otta / Welcome to the Jungle*, *Wellfound*, *Contra*, dan *Glints*):
1. **Warm Persimmon / Terracotta (`#c2410c` / `#ea580c`):** Memberi kehangatan, urgensi positif, dan semangat eksplorasi mahasiswa muda.
2. **Deep Obsidian Ink (`#0f172a` / `#18181b`):** Menjadi jangkar struktural yang tegas, memberikan wibawa dan kredibilitas legalitas instansi.
3. **Forest Emerald (`#047857` / `#059669`):** Khusus untuk transparansi finansial (nominal rupiah kompensasi & status terverifikasi).
4. **Single Light Mode:** Dirancang murni untuk mode terang dengan pencahayaan hangat alami tanpa komplikasi dual dark/light mode.

---

## 2. Sistem Warna & Token Semantik

Palet resmi: **Warm Persimmon & Obsidian Craft**.

| Peran Token | Kode Hex | Kelas Tailwind | Rasional & Penggunaan |
| :--- | :--- | :--- | :--- |
| **Canvas** | `#fafaf9` | `bg-stone-50` | Latar belakang lembut dengan nuansa kertas hangat alami |
| **Surface** | `#ffffff` | `bg-white` | Kartu lowongan, modal dialog, kontainer pencarian |
| **Border Subtle** | `#e7e5e4` | `border-stone-200` | Garis batas tipis 1px presisi tinggi |
| **Brand Primary** | `#c2410c` | `bg-orange-700` / `text-orange-700` | Identitas brand utama, tombol aksi pencarian, fokus aktif |
| **Brand Vibrant** | `#ea580c` | `bg-orange-600` / `hover:bg-orange-700`| Aksen cerah, tag baru, hover interaktif |
| **Brand Soft Tint** | `#fff7ed` | `bg-orange-50` / `border-orange-200` | Badge lowongan paruh waktu, sorotan filter aktif |
| **Anchor Dark (Ink)**| `#0f172a` | `bg-slate-900` / `text-slate-900` | Tombol sekunder ("Lihat Detail"), judul utama, teks tegas |
| **Earning Accent** | `#047857` | `text-emerald-700` | Nominal uang saku/gaji & tanda verifikasi terpercaya |
| **Earning Soft Tint**| `#ecfdf5` | `bg-emerald-50` / `border-emerald-200` | Badge jaminan upah transparan |
| **Text Main** | `#0f172a` | `text-slate-900` | Teks judul, nama posisi (Kontras > 14:1) |
| **Text Muted** | `#57534e` | `text-stone-600` | Deskripsi posisi, instansi, jam kerja (Kontras > 5.5:1) |

### Aturan Warna Status & Badges
* **Lowongan Magang:** Netral bergaris dengan karakter tegas (`border border-stone-200 bg-stone-100/70 text-stone-800 font-semibold`).
* **Lowongan Paruh Waktu:** Aksen hangat energik (`border border-orange-200 bg-orange-50 text-orange-800 font-semibold`).
* **Sistem Kerja (Remote / Onsite / Hybrid):** Netral bersih (`border border-stone-200 bg-white text-stone-700`). Tidak menggunakan warna pelangi.

---

## 3. Tipografi & Hierarki Teks

* **Font Utama:** `Plus Jakarta Sans` (sans-serif modern terukur dengan geometri humanis).
* **Font Numerik & Data:** `font-mono` (`JetBrains Mono`) untuk nominal rupiah gaji, beban jam per minggu, dan tenggat waktu.

### Aturan Tipografi
1. **`text-balance`:** Wajib pada semua judul utama (H1, H2, H3).
2. **`text-pretty`:** Digunakan pada paragraf deskripsi tugas dan persyaratan.
3. **`tabular-nums`:** Wajib untuk semua angka kompensasi dan jam kerja.

---

## 4. Skala Spasial & Tata Letak (8pt Grid)

* **Jarak Atomik (Elemen dalam Komponen):** $6\text{px}$–$8\text{px}$ (`gap-1.5` s/d `gap-2`).
* **Jarak Molekul (Judul ke Deskripsi):** $8\text{px}$–$12\text{px}$ (`gap-2` s/d `gap-3`).
* **Padding Kartu Lowongan:** $20\text{px}$–$24\text{px}$ (`p-5` s/d `p-6`).
* **Jarak Antar Kartu Grid:** $16\text{px}$–$24\text{px}$ (`gap-4` s/d `gap-6`).
* **Penyelarasan Kartu (`mt-auto`):** Footer kartu (kompensasi dan tombol aksi) wajib diratakan di bagian bawah menggunakan `flex flex-col` dan `mt-auto`.
* **Batas Kontainer:** Maksimal `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`.

---

## 5. Mikro-Interaksi & Respon Taktil

* **Sensasi Tekan Tombol:** Seluruh elemen interaktif memiliki feedback taktil nyata:
  `active:scale-[0.98] transition-all duration-150 ease-out`
* **Hover Halus:** Peningkatan kontras border secara halus: `hover:border-stone-300 hover:shadow-sm`.
* **Focus Ring Aksesibel:**
  `focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2`
* **Reduced Motion:** Nonaktifkan transisi jika pengguna mengaktifkan pengaturan hemat gerak:
  `motion-reduce:transition-none`

---

## 6. Aksesibilitas & Standar Ergonomi

1. **Responsif Layar Sempit:** Layout wajib fleksibel tanpa luapan horizontal (*no horizontal overflow*) hingga resolusi smartphone minimal **$360\text{px}$**.
2. **Target Sentuh Mobile:** Seluruh tombol dan target sentuh interaktif berukuran minimal **$44 \times 44\text{px}$** (`min-h-[44px] min-w-[44px]`).
3. **Standar Kontras WCAG AA:** Rasio kontras teks reguler terhadap latar belakang minimal $\ge 4.5:1$ (Brand Persimmon `#c2410c` terhadap putih mencapai $\ge 6.2:1$).

---

## 7. Aturan Anti-Slop (Wajib Ditaati)

* **DILARANG BIRU KORPORAT / UNGU GENUS AI:** Jangan gunakan warna biru standar Tailwind (`blue-600` / `#2563eb`) atau gradien ungu SaaS.
* **DILARANG EMOJI SEBAGAI IKON:** Gunakan teks tipografis atau dot geometris bersih.
* **DILARANG BADGE HERO KLISE:** Jangan memasang badge mengambang seperti *"Direktori Aktif • Khusus Mahasiswa"*.
* **RESTRIKSI IKON DEKORATIF:** Ikon hanya digunakan jika memiliki fungsi navigasi/pencarian esensial.
