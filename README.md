<div align="center">

# AksesKita

**Pemetaan dan Verifikasi Fasilitas Publik Ramah Disabilitas**

AksesKita membantu masyarakat menemukan fasilitas publik dan mengetahui kondisi
aksesibilitasnya sebelum berkunjung — dari ketersediaan ramp hingga lebar akses masuk.

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Vue](https://img.shields.io/badge/Vue-3-42B883?logo=vuedotjs&logoColor=white)](https://vuejs.org/)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)](https://vite.dev/)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9-199900?logo=leaflet&logoColor=white)](https://leafletjs.com/)
![Status](https://img.shields.io/badge/status-prototipe%20frontend-A8542A)

</div>

> ⚠️ **Status proyek: prototipe frontend.**
> Seluruh data — 13 fasilitas dan 11 pengajuan contoh — berada di `resources/js/data/`
> dan hanya hidup di memori browser. Belum ada backend domain, autentikasi, unggah
> berkas, atau penyimpanan permanen. Menyetujui pengajuan pada dashboard verifikasi
> memang menambah fasilitas ke peta, tetapi **hilang saat halaman dimuat ulang**.

## Daftar Isi

- [Tentang AksesKita](#tentang-akseskita)
- [Permasalahan dan Solusi](#permasalahan-dan-solusi)
- [Fitur Utama](#fitur-utama)
- [Indikator Aksesibilitas](#indikator-aksesibilitas)
- [Alur Penggunaan](#alur-penggunaan)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Tampilan Aplikasi](#tampilan-aplikasi)
- [Struktur Direktori](#struktur-direktori)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi dan Menjalankan](#instalasi-dan-menjalankan)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Pengujian dan Pengembangan](#pengujian-dan-pengembangan)
- [Roadmap](#roadmap)
- [Kontribusi](#kontribusi)
- [Tujuan Pembangunan Berkelanjutan](#tujuan-pembangunan-berkelanjutan)
- [Lisensi dan Kontak](#lisensi-dan-kontak)

## Tentang AksesKita

Bagi penyandang disabilitas, keluarga, dan pendamping, pertanyaan paling mendasar
sebelum mengunjungi fasilitas publik bukan "di mana lokasinya", melainkan
"apakah saya bisa masuk dan menggunakannya". Informasi seperti itu jarang tersedia
dalam bentuk terstruktur: sebagian tersebar di ulasan, sebagian hanya diketahui
warga sekitar, dan sebagian lagi tidak pernah dicatat.

AksesKita mencoba merapikan informasi tersebut ke dalam satu peta dan satu format data
yang konsisten:

- **Mencari** fasilitas berdasarkan nama, alamat, kota, kategori, atau status verifikasi.
- **Menyaring** hanya fasilitas yang benar-benar memiliki atribut tertentu — misalnya
  yang punya ramp *dan* toilet aksesibel sekaligus.
- **Membaca detail** kondisi aksesibilitas, catatan lapangan, dan bukti pendukung.
- **Mengusulkan** fasilitas baru atau melaporkan data yang sudah tidak sesuai.
- **Memeriksa** pengajuan sebelum informasi dianggap tepercaya (dashboard verifikasi).

Sasaran penggunanya: penyandang disabilitas dan keluarganya, pendamping atau relawan,
pengelola fasilitas publik, serta masyarakat umum yang ingin memastikan tempat yang
mereka tuju dapat diakses semua orang.

## Permasalahan dan Solusi

| Permasalahan | Pendekatan AksesKita | Status |
| --- | --- | --- |
| Informasi aksesibilitas tersebar dan tidak seragam | Satu skema data: 5 indikator aksesibilitas dengan tiga nilai baku (`tersedia`, `tidak`, `belum`) | ✅ Skema dan tampilan sudah ada |
| Sulit menilai kesiapan fasilitas sebelum berkunjung | Peta, daftar kartu, filter atribut, dan halaman detail per fasilitas | ✅ Berjalan di frontend |
| Data yang tidak lengkap tampak sama dengan data yang buruk | Indikator `belum diketahui` ditampilkan terpisah dari `tidak tersedia`, plus persentase kelengkapan data | ✅ Berjalan |
| Informasi dari masyarakat perlu diperiksa | Antrean verifikasi dengan aksi setujui, tolak, dan minta perbaikan | ⚠️ Alur tersimulasi di browser, belum ada backend |
| Data lama bisa menyesatkan | Penanda "Perlu diperbarui" di tabel fasilitas dashboard admin bila data tidak diperiksa lebih dari 180 hari | ✅ Berjalan (perhitungan di sisi klien) |
| Kontribusi warga belum terverifikasi | Setiap pengajuan membawa jenis, ringkasan, dan bukti pendukung | ⚠️ Formulir dan validasi ada, berkas tidak diunggah |

AksesKita **belum** menyelesaikan seluruh persoalan di atas. Yang sudah tersedia adalah
alur dan antarmuka yang utuh untuk diperagakan; lapisan data dan otorisasi masih harus
dibangun.

## Fitur Utama

| Fitur | Rute | Keterangan |
| --- | --- | --- |
| Peta fasilitas | `/` | Leaflet + OpenStreetMap, penanda kotak berisi kode kategori |
| Pencarian dan filter | `/` | Kata kunci, kategori, status verifikasi, dan kombinasi atribut |
| Daftar kartu | `/` | Menampilkan status verifikasi dan kelengkapan data tiap fasilitas |
| Detail fasilitas | `/fasilitas/:id` | 5 indikator aksesibilitas, catatan, bukti, riwayat pemeriksaan |
| Kontribusi masyarakat | `/kontribusi` | Usulan fasilitas baru atau koreksi data, lengkap dengan validasi |
| Dashboard verifikasi | `/verifikasi` | Antrean pengajuan, detail, setujui/tolak/minta perbaikan, riwayat |
| Dashboard administrasi | `/admin` | Statistik, tabel fasilitas, kelengkapan data, kategori, dan peran pengguna |
| Tentang dan panduan | `/tentang` | Latar belakang, panduan penggunaan, mekanisme kontribusi dan verifikasi |
| Halaman 404 | `*` | Rute tidak dikenal tetap memakai shell SPA |

**Sudah berjalan (frontend):**

- 🗺️ Peta interaktif dengan 13 fasilitas contoh di 8 kategori: transportasi, kesehatan,
  pendidikan, pemerintahan, perbelanjaan, rekreasi, ibadah, dan olahraga.
- 🔎 Filter bekerja pada peta dan daftar sekaligus; memilih atribut berarti "harus
  memiliki semua atribut ini", bukan "salah satu".
- 📊 Persentase kelengkapan data per fasilitas, dihitung dari jumlah indikator yang
  bukan `belum`.
- 📝 Formulir kontribusi dengan validasi wajib isi, pemilihan lokasi lewat peta,
  pratinjau foto di sisi klien, dan nomor pengajuan otomatis (`SUB-0143`, dst.).
- ✅ Aksi verifikasi benar-benar mengubah state bersama: pengajuan yang disetujui
  menambah atau memperbarui fasilitas yang tampil di peta dan daftar.
- 🧭 Navigasi SPA dengan riwayat browser, judul halaman per rute, dan tautan aktif.

**Belum tersedia:**

- Autentikasi, akun, dan pembatasan akses per peran (`admin`, `verifikator`,
  `kontributor` baru berupa data contoh di panel admin).
- Penyimpanan permanen: tidak ada tabel `facilities`, `submissions`, atau `proofs`.
- API, unggah foto ke server, dan validasi di sisi server.
- Jejak audit dan moderasi data dunia nyata.

## Indikator Aksesibilitas

Lima indikator yang dipakai di seluruh aplikasi (didefinisikan di
`resources/js/data/meta.js`):

| ID | Indikator | Yang dicatat |
| --- | --- | --- |
| `ramp` | Ramp / jalur landai | Jalur landai di pintu masuk dengan kemiringan ramah kursi roda |
| `toilet` | Toilet aksesibel | Toilet dengan ruang manuver, pegangan, dan pintu lebar |
| `lift` | Lift | Lift dengan tombol braille dan penanda suara |
| `path` | Jalur kursi roda | Jalur di dalam area yang bebas tangga dan hambatan |
| `entrance` | Akses masuk | Pintu masuk bebas hambatan, lebar minimal 80 cm |

Setiap indikator bernilai salah satu dari tiga status. **Nilai yang dipakai di kode
berbahasa Indonesia**, padanan `yes`/`no`/`unknown` hanya untuk memudahkan pembacaan:

| Nilai di data | Arti | Padanan |
| --- | --- | --- |
| `tersedia` | Tercatat tersedia berdasarkan informasi yang dikumpulkan | `yes` |
| `tidak` | Dinyatakan tidak tersedia berdasarkan informasi yang dikumpulkan | `no` |
| `belum` | Belum diketahui atau informasinya belum memadai | `unknown` |

> `belum` **bukan** `tidak`. Fasilitas yang toiletnya belum pernah diperiksa tidak
> boleh dianggap tidak punya toilet aksesibel. Karena itu `belum` tidak pernah
> dihitung sebagai "tersedia" pada filter, dan justru menurunkan persentase
> kelengkapan data.

Status verifikasi tingkat fasilitas: `terverifikasi`, `menunggu`, `perlu-perbaikan`,
dan `ditolak`. Status pengajuan: `menunggu`, `disetujui`, `ditolak`, `perbaikan`.
Data yang tidak diperiksa lebih dari **180 hari** ditandai **Perlu diperbarui** pada tabel
fasilitas di dashboard admin (`STALE_DAYS` di `meta.js`).

## Alur Penggunaan

1. **Buka beranda** (`/`) — peta dan daftar fasilitas tampil berdampingan. Daftar kartu
   adalah jalur alternatif untuk pengguna keyboard dan pembaca layar.
2. **Cari atau saring** — ketik nama/alamat/kota, pilih kategori, status verifikasi,
   atau atribut yang wajib ada. Jumlah hasil ikut berubah di peta dan daftar.
3. **Buka detail** (`/fasilitas/fas-001`) — periksa lima indikator, catatan lapangan,
   bukti pendukung, dan kapan terakhir diperiksa.
4. **Kirim kontribusi** (`/kontribusi`) — pilih "usulan fasilitas baru" atau "koreksi
   data", isi lokasi lewat peta, lalu kirim. Formulir menolak isian yang tidak lengkap.
5. **Verifikasi** (`/verifikasi`) — buka pengajuan, tinjau ringkasan dan bukti, lalu
   setujui, tolak, atau minta perbaikan. Persetujuan usulan langsung menambah fasilitas
   ke peta pada sesi yang sama.

Langkah 4 dan 5 sudah dapat dicoba, tetapi hasilnya belum tersimpan ke database dan
belum ada pemeriksaan hak akses.

## Teknologi yang Digunakan

| Teknologi | Versi | Peran dalam proyek |
| --- | --- | --- |
| PHP | `^8.3` (diuji pada 8.5.9) | Bahasa server |
| Laravel | `^13.17` (terpasang 13.38.0) | Kerangka backend, routing, Blade shell, migrasi |
| Vue 3 | `3.5.44` | Seluruh antarmuka SPA (`<script setup>`) |
| Vue Router | `5.4.0` | 6 rute + halaman 404, mode history |
| Vite | `8.3.4` | Build aset, HMR, unduh font saat build |
| `laravel-vite-plugin` | `3.1` | Menghubungkan Vite dengan Blade (`@vite`, `Vite::fonts()`) |
| `@vitejs/plugin-vue` | `6.0.10` | Kompilasi komponen `.vue` |
| Leaflet | `1.9.4` | Peta interaktif, penanda `L.divIcon` tanpa aset gambar |
| OpenStreetMap | — | Sumber tile peta (butuh koneksi internet) |
| Tailwind CSS | `4.x` | Terpasang dan aktif di Vite; antarmuka sendiri memakai token CSS di `resources/css/app.css` |
| MySQL | 8.x | Database pada `.env.example` (belum menyimpan data fasilitas) |
| PHPUnit | `12.5` | Tes backend |
| Laravel Pint | `1.27` | Perapian gaya kode PHP |
| Composer | 2.x | Manajemen dependensi PHP |
| npm | 11.x | Manajemen dependensi frontend |

Tidak ada library state management, HTTP client, atau komponen UI eksternal. State
bersama memakai composable singleton (`useFacilities`, `useSubmissions`, `useToast`),
dan seluruh gaya ditulis sebagai kelas CSS biasa.

## Tampilan Aplikasi

| Halaman | Isi utama |
| --- | --- |
| Beranda | Hero ringkas, peta fasilitas, panel filter, daftar kartu, legenda peta |
| Detail fasilitas | Nama, kategori, alamat, lima indikator, catatan, bukti, status verifikasi |
| Kontribusi | Dua jenis formulir, pemilih lokasi di peta, pratinjau foto, daftar kesalahan |
| Verifikasi | Antrean kiri, detail pengajuan, aksi keputusan, riwayat keputusan |
| Administrasi | Kartu statistik, tabel fasilitas, diagram kelengkapan, kelola kategori, peran pengguna |
| Tentang | Latar belakang, panduan langkah demi langkah, mekanisme kontribusi dan verifikasi |

Repositori ini **belum** menyimpan tangkapan layar. Daripada menampilkan gambar yang
tidak dapat diverifikasi, dokumentasi visual sengaja dikosongkan. Untuk menambahkannya:

1. Jalankan aplikasi, ambil tangkapan layar beranda, detail, dan verifikasi.
2. Simpan di `docs/screenshots/` dengan nama deskriptif, misalnya `beranda.png`.
3. Sisipkan dengan path relatif, contoh:
   `![Beranda AksesKita: peta dan daftar fasilitas](docs/screenshots/beranda.png)`.

## Struktur Direktori

```text
fasilitas-disabilitas/
├── app/                        # Controller dasar, model User, service provider
│                               # (belum ada logika domain fasilitas)
├── bootstrap/                  # Bootstrap aplikasi dan cache
├── config/                     # Konfigurasi Laravel
├── database/
│   ├── factories/              # UserFactory (bawaan)
│   ├── migrations/             # users, cache, jobs — belum ada tabel fasilitas
│   └── seeders/                # DatabaseSeeder (bawaan)
├── public/                     # Document root; build aset Vite diabaikan Git
├── resources/
│   ├── css/app.css             # Token desain + seluruh kelas komponen
│   ├── js/
│   │   ├── components/         # layout, common, map, facilities, forms,
│   │   │                       # verification, dashboard
│   │   ├── composables/        # useFacilities, useSubmissions, useToast
│   │   ├── data/               # facilities, submissions, users, meta (data contoh)
│   │   ├── router/index.js     # Rute SPA dan judul halaman
│   │   ├── views/              # 7 halaman: beranda, detail, kontribusi,
│   │   │                       # verifikasi, admin, tentang, 404
│   │   ├── App.vue             # Header, footer, host toast
│   │   └── app.js              # Titik masuk Vue
│   └── views/
│       ├── app.blade.php       # Satu-satunya shell aplikasi (#app + @vite)
│       └── welcome.blade.php   # Halaman bawaan Laravel (tidak dipakai rute)
├── routes/web.php              # SPA fallback, /up dikecualikan
├── storage/                    # Log, cache, sesi (isi diabaikan Git)
├── tests/                      # AksesKitaTest + dua contoh bawaan Laravel
├── TemplateDesignWeb/          # Referensi desain asli (read-only, bukan bagian aplikasi)
├── vite.config.js              # Plugin Vue, Tailwind, dan unduh font Bunny
├── composer.json               # Dependensi dan skrip PHP
└── package.json                # Dependensi dan skrip frontend
```

## Persyaratan Sistem

| Kebutuhan | Versi minimum | Catatan |
| --- | --- | --- |
| PHP | 8.3 | Mengikuti `composer.json`; dikembangkan pada PHP 8.5.9 |
| Composer | 2.x | Diuji pada 2.10.2 |
| Node.js | 20.19 atau 22.12+ | Batas dari Vite 8; diuji pada Node 24.15 |
| npm | 10+ | Diuji pada npm 11.12 |
| MySQL | 8.x | Alternatif: SQLite untuk pengembangan lokal |
| Git | 2.x | Diuji pada 2.55 |

Butuh koneksi internet untuk dua hal: mengunduh font (Syne, Inter, DM Mono) saat
`npm run build`/`npm run dev`, dan memuat tile OpenStreetMap saat peta dibuka.

## Instalasi dan Menjalankan

### 1. Clone dan pasang dependensi

```powershell
git clone https://github.com/dymmm-0/fasilitas-disabilitas.git
cd fasilitas-disabilitas
composer install
npm install
```

> `.npmrc` pada proyek ini memuat `ignore-scripts=true`, sehingga skrip `postinstall`
> paket npm tidak dijalankan. Untuk proyek ini tidak ada yang dibutuhkan.

### 2. Siapkan environment dan kunci aplikasi

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 3. Siapkan database

Buat database kosong, misalnya `fasilitas_disabilitas`, lalu sesuaikan `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fasilitas_disabilitas
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi bawaan:

```powershell
php artisan migrate
```

Migrasi hanya membuat tabel `users`, `cache`, dan `jobs`. Aplikasi tetap berjalan
tanpa data fasilitas di database karena seluruh isinya berasal dari data contoh
di `resources/js/data/`.

### 4. Bangun aset frontend

```powershell
npm run build
```

### 5. Jalankan aplikasi

**Opsi A — satu perintah (disarankan).** `composer run dev` menjalankan
`php artisan dev`, yang menyalakan tiga proses sekaligus:

```powershell
composer run dev
```

- server Laravel di `http://127.0.0.1:8000`
- queue listener
- Vite (HMR untuk `resources/js` dan `resources/css`)

**Opsi B — terpisah di dua terminal.**

```powershell
php artisan serve
npm run dev
```

**Opsi C — virtual host Laragon.** Arahkan document root ke folder `public/`
(misalnya `http://fasilitas-disabilitas.test`), tambahkan host di `hosts`, lalu
restart Apache. Dengan opsi ini `npm run dev` tidak wajib selama aset sudah
dibangun dengan `npm run build`.

### Perbedaan `dev` dan `build`

| Perintah | Kapan dipakai | Hasil |
| --- | --- | --- |
| `npm run dev` | Saat mengubah Vue/CSS | Aset disajikan dari server Vite (HMR), butuh proses berjalan |
| `npm run build` | Sebelum demo atau deploy | Aset terkompilasi ke `public/build/`, tidak butuh Vite |

`public/build/` diabaikan Git. Di server produksi jalankan `npm run build` sekali,
lalu sajikan aplikasi lewat web server — `npm run dev` tidak boleh dipakai di produksi.

### Pintasan setup

```powershell
composer run setup
```

Perintah ini menjalankan `composer install`, menyalin `.env`, membuat application key,
`php artisan migrate --force`, `npm install --ignore-scripts`, dan `npm run build`.
Database harus sudah dibuat sebelum perintah dijalankan.

## Konfigurasi Environment

| Variabel | Kegunaan | Contoh aman |
| --- | --- | --- |
| `APP_NAME` | Nama aplikasi | `AksesKita` |
| `APP_ENV` | Lingkungan | `local` |
| `APP_KEY` | Kunci enkripsi Laravel | Dibuat oleh `php artisan key:generate` |
| `APP_URL` | URL dasar aplikasi | `http://localhost:8000` |
| `APP_DEBUG` | Detail error | `true` hanya di lokal |
| `DB_CONNECTION` | Driver database | `mysql` |
| `DB_DATABASE` | Nama database | `fasilitas_disabilitas` |
| `DB_USERNAME` / `DB_PASSWORD` | Kredensial database | Isi sesuai mesin masing-masing |
| `SESSION_DRIVER` | Penyimpanan sesi | `database` (bawaan) |
| `QUEUE_CONNECTION` | Antrean | `database` (bawaan) |
| `VITE_APP_NAME` | Nama aplikasi untuk sisi Vite | `${APP_NAME}` |

- File `.env` **tidak** diunggah ke GitHub; `.gitignore` sudah mengecualikannya.
- `.env.example` hanya berisi placeholder tanpa kredensial asli.
- Tes memaksa SQLite `:memory:` lewat `phpunit.xml`, sehingga tidak pernah menyentuh
  database lokal.
- Jangan menaruh token, kunci API, atau kata sandi di dalam kode maupun di README.

## Pengujian dan Pengembangan

```powershell
# Tes backend (PHPUnit 12)
php artisan test

# Satu berkas tes
php artisan test tests/Feature/AksesKitaTest.php

# Periksa format PHP tanpa mengubah berkas
vendor/bin/pint --test

# Rapikan format PHP
vendor/bin/pint

# Build aset frontend
npm run build
```

Saat README ini ditulis, `php artisan test` menjalankan 5 tes dengan 12 asersi dan
lulus: tiga tes fitur AksesKita (shell Vue pada beranda, SPA fallback untuk
`/fasilitas/:id`, `/kontribusi`, `/admin`, serta `/up` yang tidak tertutup rute SPA)
dan dua tes contoh bawaan Laravel. Perintah `vendor/bin/pint --test` juga lulus.

Yang belum ada: tes untuk komponen Vue, tes end-to-end, linter JavaScript, dan CI.
Jangan menganggap perubahan frontend "sudah teruji" hanya karena tes PHP lulus.

## Roadmap

**Sudah selesai (prototipe frontend)**

- [x] Peta Leaflet + OpenStreetMap dengan penanda berkode kategori
- [x] Pencarian, filter kategori, status verifikasi, dan kombinasi atribut
- [x] Halaman detail dengan lima indikator, bukti, dan status verifikasi
- [x] Formulir usulan fasilitas dan koreksi data dengan validasi
- [x] Dashboard verifikasi: setujui, tolak, minta perbaikan, riwayat
- [x] Dashboard administrasi: statistik, tabel fasilitas, kelengkapan data, kategori, peran
- [x] Tata letak responsif dan status yang selalu disertai teks + glyph
- [x] Shell SPA Laravel dengan rute history dan halaman 404

**Berikutnya**

- [ ] Skema database: `facilities`, `facility_attributes`, `submissions`, `proofs`, `reviews`
- [ ] API Laravel + validasi di sisi server untuk usulan dan koreksi
- [ ] Autentikasi dan otorisasi berbasis peran (admin, verifikator, kontributor)
- [ ] Unggah foto ke storage dan penyajian bukti pendukung
- [ ] Riwayat perubahan dan jejak audit keputusan verifikasi
- [ ] Data fasilitas nyata dengan alur moderasi
- [ ] Tes komponen Vue, tes end-to-end, dan CI
- [ ] Audit aksesibilitas WCAG 2.2 AA dan perbaikan temuan
- [ ] Tangkapan layar di `docs/screenshots/`

## Kontribusi

1. Fork repositori ini.
2. Buat branch baru dari `master`:
   ```powershell
   git checkout -b feat/nama-perubahan
   ```
3. Lakukan perubahan, lalu jalankan `php artisan test`, `vendor/bin/pint --test`,
   dan `npm run build`.
4. Commit dengan pesan yang jelas, misalnya `feat: tambah filter jarak terdekat`.
5. Push branch dan ajukan pull request ke `master`.

Karena kode aplikasi masih berupa prototipe, jelaskan di pull request apakah perubahan
menyentuh data contoh, komponen bersama di `composables/`, atau hanya tampilan.
Jangan menyertakan `.env`, kredensial, atau berkas hasil build.

## Tujuan Pembangunan Berkelanjutan

AksesKita berkaitan langsung dengan dua tujuan pembangunan berkelanjutan:

- **SDG 10 — Berkurangnya kesenjangan.** Informasi aksesibilitas yang terbuka membantu
  penyandang disabilitas merencanakan perjalanan dengan lebih setara, tanpa harus
  bergantung pada kabar dari mulut ke mulut.
- **SDG 11 — Kota dan permukiman yang inklusif.** Data kondisi fasilitas publik dapat
  menjadi masukan bagi pengelola dan pemerintah daerah untuk memperbaiki akses.

Keterkaitan ini bersifat konseptual dan baru pada tahap prototipe. Belum ada
pengukuran dampak sosial, jumlah pengguna, atau data lapangan yang dapat diklaim.

## Lisensi dan Kontak

**Lisensi belum ditentukan.** Repositori ini belum memiliki berkas `LICENSE`.
Nilai `"license": "MIT"` pada `composer.json` adalah bawaan skeleton Laravel dan
bukan pernyataan lisensi atas karya ini. Pemilik repositori perlu memutuskan
lisensinya sebelum kode dipakai ulang di luar konteks kompetisi.

Pertanyaan, laporan bug, atau usulan pengembangan dapat disampaikan melalui
[Issues repositori](https://github.com/dymmm-0/fasilitas-disabilitas/issues).

---

<div align="center">
<sub>AksesKita — informasi aksesibilitas yang jelas sebelum berangkat, bukan setelah tiba.</sub>
</div>
