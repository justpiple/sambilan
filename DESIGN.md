# Panduan Desain Sambilan (DESIGN.md)

Dokumen ini adalah acuan umum visual, prinsip desain, dan sistem token untuk platform **Sambilan: Katalog Lowongan Magang & Kerja Paruh Waktu Mahasiswa**. Setiap halaman dan komponen baru wajib mengacu pada prinsip di bawah ini.

---

## 1. Filosofi & Karakter Visual

* **Arketipe:** **Modern Utilitarian + Student-Friendly Craft**.
* **Karakter:** Bersih, terpercaya, cepat dipindai (*high scannability*), dan fungsional untuk mahasiswa aktif.
* **Prinsip Utama:**
  * **Kedalaman Halus:** Hindari bayangan tebal/buram (*fuzzy shadows*). Gunakan kombinasi batas tipis 1px semi-transparan (`border border-slate-200/80 dark:border-white/10`) dipadukan dengan elevasi mikro (`shadow-xs`).
  * **Fokus Informasi:** Mahasiswa mencari kejelasan: nama posisi, instansi, sistem kerja, komitmen jam/minggu, dan nominal kompensasi.

---

## 2. Sistem Warna & Token Semantik

Gunakan token warna yang konsisten di seluruh aplikasi:

| Peran Token | Nilai Light Mode | Nilai Dark Mode | Penggunaan |
| :--- | :--- | :--- | :--- |
| **Canvas (Latar)** | `#f8fafc` (`slate-50`) | `#09090b` (`zinc-950`) | Latar belakang halaman |
| **Surface (Kartu)** | `#ffffff` | `#18181b` (`zinc-900`) | Kontainer, kartu, modal |
| **Border Subtle** | `rgba(15, 23, 42, 0.08)` | `rgba(255, 255, 255, 0.10)` | Garis batas kartu & pembatas |
| **Primary (Brand)** | `#0f172a` (`slate-900`) | `#f8fafc` (`slate-50`) | Tombol aksi utama (CTA), header |
| **Primary Accent** | `#047857` (`emerald-700`)| `#10b981` (`emerald-500`)| Aksen tautan aktif, indikator pertumbuhan |
| **Accent Kompensasi**| `#059669` (`emerald-600`)| `#34d399` (`emerald-400`)| Nominal rupiah gaji & kompensasi |
| **Text Main** | `#0f172a` (`slate-900`) | `#f8fafc` (`slate-50`) | Judul, nama posisi (Kontras > 7:1) |
| **Text Muted** | `#64748b` (`slate-500`) | `#94a3b8` (`slate-400`) | Deskripsi, lokasi, jam kerja |

### Aturan Warna Status & Badges (Disiplin Monokromatik)
*Hindari palet pelangi klise AI (jangan gunakan ungu, pink, atau cyan acak).*
* **Tipe Lowongan:** 
  * *Magang:* Netral bergaris (`border border-slate-200 bg-slate-50 text-slate-700 dark:border-white/10 dark:bg-zinc-800 dark:text-slate-300`).
  * *Paruh Waktu:* Aksen pertumbuhan halus (`border border-emerald-200/80 bg-emerald-50 text-emerald-800 dark:border-emerald-800/40 dark:bg-emerald-950/40 dark:text-emerald-300`).
* **Sistem Kerja (Remote / Onsite / Hybrid):**
  * Seluruhnya menggunakan badge netral tipografis seragam (`border border-slate-200/90 bg-white text-slate-600 dark:border-white/10 dark:bg-zinc-900 dark:text-slate-400`). Pembedaan dilakukan lewat teks yang jelas, bukan warna pelangi.

---

## 3. Tipografi & Hirarki Teks

* **Font Utama:** `Plus Jakarta Sans` atau `Instrument Sans` (sans-serif modern).
* **Font Angka & Data:** `font-mono` untuk angka kompensasi, jam kerja, dan tanggal.

### Aturan Tipografi
1. **`text-balance`:** Gunakan pada semua judul (H1, H2, H3) agar pemenggalan baris rapi dan tidak ada kata sendirian.
2. **`text-pretty`:** Gunakan pada paragraf deskripsi dan kualifikasi persyaratan.
3. **`tabular-nums`:** Wajib untuk semua angka metrik, nominal rupiah, dan beban jam kerja agar lebar angka seragam dan tidak melompat saat nilainya berubah.

---

## 4. Hirarki Spasial & Layout

* **Skala Spasial (8pt Grid):**
  * Jarak elemen dalam komponen (atom): $6\text{px}$–$8\text{px}$ (`gap-2`).
  * Jarak judul ke deskripsi (molekul): $8\text{px}$–$12\text{px}$ (`gap-3`).
  * Padding internal kartu: $16\text{px}$–$24\text{px}$ (`p-4` s/d `p-6`).
  * Jarak antar kartu grid: $16\text{px}$–$24\text{px}$ (`gap-4` s/d `gap-6`).
  * Jarak antar seksi halaman: $48\text{px}$–$80\text{px}$ (`py-12` s/d `py-20`).
* **Penyelarasan Kartu (`mt-auto`):** Pada grid kartu dengan panjang teks berbeda, tombol aksi di bagian bawah wajib diratakan menggunakan `flex flex-col` dan `mt-auto`.
* **Batas Kontainer:** Batasi lebar konten dengan `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` agar tidak melar di monitor lebar.

---

## 5. Mikro-Interaksi & Respon Taktil (Emil Kowalski Physics)

* **Sensasi Tekan Tombol:** Setiap tombol dan kartu interaktif wajib memiliki respon taktil:
  `active:scale-[0.98] transition-transform duration-100 ease-out`
* **Hover Halus:** Gunakan pergeseran warna/border lembut: `hover:border-slate-300 transition-all duration-150`.
* **Budget Durasi:** Maksimal $150\text{ms}$ untuk interaksi mikro tombol, maksimal $200\text{ms}$ untuk dialog/dropdown.
* **Focus Ring:** Wajib terlihat jelas untuk navigasi keyboard:
  `focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 dark:focus-visible:ring-white`
* **Reduced Motion:** Neutralisasi semua transisi jika pengguna meminta mode hemat gerak:
  `motion-reduce:transition-none`

---

## 6. Aksesibilitas & Batasan Ergonomi (NFR-04)

1. **Responsif Layar Sempit:** Tata letak harus fleksibel dan tidak boleh patah (*no horizontal overflow*) hingga resolusi **$360\text{px}$**.
2. **Area Sentuh Mobile:** Target sentuh tombol dan elemen interaktif minimal **$44 \times 44\text{px}$** (`min-h-[44px] min-w-[44px]`).
3. **Kontras Teks:** Rasio kontras wajib memenuhi WCAG AA ($\ge 4.5:1$ untuk teks biasa).
4. **Bahasa:** Seluruh antarmuka menggunakan Bahasa Indonesia baku yang ramah mahasiswa.

---

## 7. Aturan Anti-Slop (Wajib Ditaati)

* **DILARANG EMOJI SEBAGAI IKON:** Jangan gunakan emoji (`⏱️`, `💰`, `⚡`, `📍`, `⏳`, `🟢`) sebagai ikon antarmuka atau awalan badge. Gunakan teks tipografi bersih murni atau dot geometris kecil.
* **RESTRIKSI IKON DEKORATIF:** Hindari menaruh ikon di setiap tombol dan judul. Ikon hanya digunakan jika memiliki fungsi utilitas esensial (misal: pencarian, tombol tutup).
* **DILARANG BADGE HERO KLISE:** Jangan memasang badge melayang klise di atas H1 (seperti *"Direktori Aktif • Khusus Mahasiswa"* atau *"Platform #1"*). Biarkan H1 tampil lugas dan percaya diri.
* **DILARANG GRADIEN UNGU/INDIGO KLISE:** Jangan gunakan latar belakang gradien warna-warni generik.
