# Rule 07: UI/UX Pro Max 10-Priority Framework & Quality Gate

Diadaptasi langsung dari repositori **`nextlevelbuilder/ui-ux-pro-max-skill`** (`references/quick-reference.md` dan `references/pro-rules.md`).

Framework ini menyusun prioritas desain dalam urutan eksekusi $1 \to 10$ yang mutlak. Setiap keputusan visual, tata letak, dan interaksi wajib melewati gerbang kualitas ini untuk memastikan antarmuka yang dihasilkan memiliki standar desainer manusia profesional kelas dunia, bukan cetakan AI murahan.

---

## Tabel 10 Prioritas Utama Desain (UI/UX Pro Max)

| Prioritas | Kategori | Tingkat Urgensi | Key Checks (Wajib Ada) | Anti-Patterns (Wajib Hindari) |
| :---: | :--- | :---: | :--- | :--- |
| **1** | **Accessibility (a11y)** | **CRITICAL** | Rasio kontras teks $\ge 4.5:1$ (teks besar $\ge 3:1$), label formulir persisten, navigasi keyboard penuh, cincin fokus $2\text{px}$, `aria-label` pada tombol ikon. | Menghilangkan outline/ring fokus, tombol ikon tanpa label, kontras abu-abu redup (*gray-on-gray*). |
| **2** | **Touch & Interaction** | **CRITICAL** | Ukuran target sentuh minimal $44 \times 44\text{px}$, jarak antar-tombol $\ge 8\text{px}$, umpan balik aktif instan tanpa jeda. | Ketergantungan hanya pada state hover (perangkat sentuh tidak punya hover), target sentuh terlalu rapat. |
| **3** | **Performance & Layout Stability** | **HIGH** | Mencegah Cumulative Layout Shift (CLS < 0.1) dengan alokasi ruang awal (*pre-allocated aspect-ratio*), pemuatan gambar modern. | Transformasi yang menggeser tata letak elemen sekitar saat tombol ditekan/di-hover. |
| **4** | **Style Selection & Consistency** | **HIGH** | Menentukan satu gaya visual kohesif yang sesuai tipe produk, ikonografi vektor SVG tunggal yang seragam. | Mencampur paradigma desain secara acak (misal: bento grid campur skeuomorphic), memakai emoji sebagai ikon sistem. |
| **5** | **Layout & Responsiveness** | **HIGH** | Desain fluid *mobile-first*, adaptasi hingga lebar sempit $360\text{px}$, pembatas kontainer (`max-w-*`), penanganan *safe-area-inset*. | Fixed-width yang menyebabkan scrollbar horizontal, header tetap yang menutupi konten tanpa padding kompensasi. |
| **6** | **Typography & Color Hierarchy** | **MEDIUM** | Ukuran teks dasar $\ge 14\text{px}$–$16\text{px}$, tinggi baris proporsional ($\sim 1.5$), panjang baris ideal $45$–$75$ karakter, token semantik terpusat. | Teks tubuh di bawah $12\text{px}$, pemakaian hardcoded hex acak di tiap komponen, tipografi monokultur tanpa kontras bobot. |
| **7** | **Motion & Animation** | **MEDIUM** | Durasi proporsional ($\le 150\text{ms}$ untuk klik, $\le 200\text{ms}$ untuk dialog), hanya menganimasikan *compositor* (`transform`, `opacity`), dukungan `prefers-reduced-motion`. | Menggunakan durasi animasi sama untuk semua komponen, menganimasikan `width`/`height` (layout thrashing), animasi loop abadi yang bikin pusing. |
| **8** | **Forms & User Feedback** | **MEDIUM** | Label input selalu terlihat (bukan hanya placeholder yang hilang saat diketik), pesan error ditaruh langsung di bawah input terkait. | Menghapus tombol submit, mematikan fungsi paste (`onpaste="return false"`), pesan error global yang tidak menunjuk input mana yang salah. |
| **9** | **Navigation Ergonomics** | **MEDIUM** | Alur navigasi yang dapat ditebak, navigasi sticky terkompensasi rapi, batas maksimal 5 item pada navigasi bawah/mobile. | Tombol back yang merusak history browser, menu bersarang terlalu dalam tanpa penanda breadcrumb yang jelas. |
| **10** | **Data & Status Indicators** | **LOW** | *Redundant encoding* (tidak mengandalkan warna saja untuk menyampaikan status; selalu sertakan teks eksplisit), angka tabular monospaced (`tabular-nums`). | Status hanya berupa lingkaran warna tanpa teks penjelasan, angka metrik yang bergoyang saat nilainya terupdate. |

---

## 4 Prinsip Anti-Slop Profesional (UI/UX Pro Max)

1. **Dual-Input Search Ergonomics:**
   * Pada platform pencarian lowongan/direktori, pencarian profesional membagi masukan menjadi dua bidang logis:
     `[ 🔍 Posisi / Kata Kunci / Perusahaan ]` dan `[ 📍 Lokasi / Wilayah / Remote ]` dengan tombol pemicu jelas.
   * *Anti-Pattern AI:* Satu search bar raksasa yang mencoba menebak segalanya dengan chip-chip hiasan acak di bawahnya.
2. **Autentisitas Data (Tanpa Mockup Palsu):**
   * Tampilkan metadata lowongan yang nyata dan relevan bagi mahasiswa: waktu terbit riil (*"2 hari lalu"*), sistem kerja (*Remote, Onsite, Hybrid*), komitmen jam pasti (*"15 jam/minggu"*), dan rentang kompensasi.
   * *Anti-Pattern AI:* Menaruh "Spotlight Card" buatan dengan teks heboh atau deretan 4 kartu statistik buatan (*"45+ Mitra, 120+ Lowongan"*).
3. **Ikon Vektor Vektor Fungsional (Zero Emoji & Zero Icon Clutter):**
   * Gunakan set ikon vektor SVG yang seragam dengan stroke 1.5px atau 2px.
   * Ikon hanya digunakan jika membantu pembacaan sekilas (misal: penanda lokasi, jam kerja, atau pencarian).
4. **Stabilitas Tata Letak Visual:**
   * Jangan gunakan efek scaling besar yang menggetarkan elemen di sekitarnya. Gunakan transisi warna permukaan (`bg-slate-50`), border lembut, dan elevasi terkontrol.
