# Rule 01: The Anti-Slop Manifesto (UI & Visual Filter)

Diadaptasi langsung dari framework **`antislop:antislop-ui`** dan dikombinasikan dengan prinsip **Paul Bakaus's Impeccable** serta **Anthropic Frontend Design**.

Setiap aturan disusun dalam format terstandarisasi:
* **Tell:** Pola visual klise yang terdeteksi.
* **Why:** Alasan mengapa pola tersebut terbaca sebagai "AI slop" murahan.
* **Fix:** Solusi konkret berstandar craft tinggi yang harus diterapkan.

---

## 1. Warna & Visual

### 1.1 The Blue-Purple / Indigo Default
* **Tell:** Penggunaan gradien biru-ke-ungu, warna primer indigo/violet elektrik (`#4f46e5`, `#6366f1`), skema ungu-hitam, atau pendaran radial ungu di belakang layar.
* **Why:** Kombinasi biru/ungu/indigo adalah warna yang paling over-representatif dalam data pelatihan LLM. Menggunakannya adalah sinyal instan bahwa antarmuka tidak memiliki identitas brand mandiri dan dibuat oleh AI secara default.
* **Fix:** **DILARANG KERAS menggunakan ungu/indigo sebagai default.** Ambil palet dari brand riil produk. Batasi palet aktif maksimal 2–3 warna inti + 1 aksen fungsional. Untuk produk profesional/mahasiswa seperti Sambilan, gunakan palet netral tegas (*Deep Ink Slate* `#0f172a`) dipadukan dengan 1 aksen bermakna (misal: *Forest Emerald* `#059669` untuk pertumbuhan & penghasilan).

### 1.2 The Background Dot-Grid / Blueprint Cliché
* **Tell:** Latar belakang diisi pola titik-titik radial (*dot grid*), kotak-kotak buku tulis (*graph paper*), garis cetak biru (*blueprint lines*), atau orbs buram.
* **Why:** Ini adalah trik malas model AI untuk membuat halaman yang kosong terasa "teknikal" tanpa melakukan pekerjaan desain nyata. Ini adalah tekstur tanpa maksud (*texture without intent*).
* **Fix:** **Hapus total grid dan orbs latar.** Gunakan latar belakang kanvas solid yang bersih (`bg-white` atau `bg-slate-50`). Kepercayaan diri sebuah desain lahir dari ritme *whitespace* dan tipografi, bukan hiasan tekstur stok.

### 1.3 Rainbow Status Badges (Terlalu Banyak Warna)
* **Tell:** Menggunakan warna pastel pelangi yang berbeda untuk setiap label (biru untuk magang, hijau untuk paruh waktu, ungu untuk remote, amber untuk onsite, cyan untuk hybrid).
* **Why:** Membuat halaman terlihat seperti toko permen warna-warni tanpa hierarki visual. Mata pengguna kewalahan dan tidak tahu elemen mana yang paling penting.
* **Fix:** **Disiplin Palet Monokromatik.** Gunakan badge netral bersahaja (`border border-slate-200 bg-slate-50 text-slate-700 dark:border-white/10 dark:bg-zinc-800 dark:text-slate-300`). Simpan aksen warna khusus hanya untuk informasi status yang kritis (misal: hijau untuk kompensasi atau merah untuk kedaluwarsa).

### 1.4 Excessive Glassmorphism
* **Tell:** Menempelkan `backdrop-blur` pada navbar, kartu, sidebar, dan dropdown sekaligus.
* **Why:** Efek kaca memudarkan tekstur dan menaruh seluruh elemen pada lapisan buram yang sama, meratakan hierarki. Jika semua benda adalah kaca, tidak ada yang menjadi latar depan.
* **Fix:** Perlakukan efek kaca sebagai aksen langka (maksimal 1 elemen, misal sticky navbar). Semua kontainer kartu lainnya harus berupa permukaan solid tegas (`bg-white` atau `bg-zinc-900`).

---

## 2. Komponen & Dekorasi

### 2.1 Emojis Masquerading as UI Icons
* **Tell:** Emoji diselipkan ke dalam badge, tombol, dan judul (`⏱️`, `💰`, `⚡`, `📍`, `⏳`, `🟢`, `🚀`, `✨`).
* **Why:** Emoji adalah bukti paling mencolok (*loudest tell*) bahwa sebuah antarmuka digenerate oleh AI, bukan dirancang oleh desainer produk. Emoji merusak baseline teks, tampil beda di setiap OS, dan terlihat amatir.
* **Fix:** **HAPUS TOTAL EMOJI.** Gunakan tipografi teks bersih murni (misal: `15 jam/minggu`, `Sisa 4 hari`, `Malang (Remote)`). Jika dibutuhkan indikator titik, gunakan dot CSS seragam (`<span class="size-1.5 rounded-full bg-emerald-500"></span>`).

### 2.2 Pretentious Hero Pill Badge
* **Tell:** Badge kapsul melayang tepat di atas H1 (misal: `🟢 Direktori Aktif • Khusus Mahasiswa Aktif`, `✨ Platform No. 1`).
* **Why:** Meniru bahasa status sistem untuk melabeli hal yang bukan status. Badge ini hanya mengulang apa yang sudah dikatakan oleh judul di bawahnya.
* **Fix:** **Hapus badge tersebut.** Biarkan H1 memimpin halaman secara lugas dan percaya diri dengan bobot tipografi yang tegas.

### 2.3 Decorative Status Dot yang Berkedip
* **Tell:** Titik hijau kecil yang berkedip/pulsing tanpa henti (`animate-ping`) di samping label yang bukan siaran langsung/rekaman nyata.
* **Why:** Meminjam kosakata indikator *live streaming* atau *recording* untuk halaman statis. Ini adalah upaya murah menarik perhatian mata tanpa fakta di baliknya.
* **Fix:** Jika tidak ada sesi streaming/koneksi websocket langsung, **hapus animasi pulse.** Titik penanda cukup berupa lingkaran statis solid jika memang merepresentasikan status aktif.

### 2.4 Decorative Icon Clutter & Arrows on Every Button
* **Tell:** Ikon Lucide/heroicon ditempelkan di setiap tombol, link nav, dan heading; panah `→` atau `↗` di setiap tombol aksi.
* **Why:** Polusi ikon mengurangi daya pikat visual. Panah di setiap tombol membuat tanda arah kehilangan makna penunjuknya.
* **Fix:** Hanya gunakan ikon jika memiliki fungsi navigasi langsung (search, close, external link). Tombol aksi cukup berupa teks padat yang jelas (misal: `"Lihat Detail"`, `"Cari Lowongan"`).

### 2.5 Fabricated Stat Strips
* **Tell:** Kotak 4 metrik statis dengan angka palsu bulat (`120+ Posisi`, `45+ Mitra`, `+12% this week`) yang ditaruh hanya agar halaman terlihat "ramai".
* **Why:** Angka karangan adalah kebohongan antarmuka. Jika produk adalah platform baru atau prototipe, kartu angka palsu terasa hampa.
* **Fix:** Tampilkan hanya angka riil dari database. Jika belum ada agregat data masif, ganti dengan pilar keunggulan nyata produk (misal: *"Tanpa Perantara Lamaran"*, *"Informasi Kontak Terverifikasi"*).

---

## 3. Checklist Audit Sebelum Finalisasi

Gunakan checklist deterministik ini setiap kali selesai menyusun markup:

- [ ] **Bebas Warna Ungu/Indigo:** Tidak ada `#4f46e5`, `#6366f1`, atau turunan violet/indigo tanpa alasan brand.
- [ ] **Bebas Dot-Grid / Texture Cliché:** Latar belakang bersih solid, tidak memakai background dot/blueprint.
- [ ] **Bebas Emoji:** Nol emoji di seluruh tombol, badge, chip, dan judul.
- [ ] **Bebas Badge Hero Palsu:** Tidak ada pill badge mengambang di atas H1.
- [ ] **Bebas Animasi Pulse Liar:** Tidak ada `animate-ping` pada status dot yang statis.
- [ ] **Restriksi Ikon:** Nol ikon dekoratif yang tidak memiliki fungsi utilitas.
- [ ] **Disiplin Palet:** Maksimal 2–3 warna inti + 1 aksen fungsional; badge berkarakter netral monokromatik.
- [ ] **Aksesibilitas & Taktil:** Kontras $\ge 4.5:1$, tap target mobile $\ge 44\text{px}$, respon tekan `active:scale-[0.98]`.
