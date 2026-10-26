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
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
![Dataset](https://img.shields.io/badge/dataset-1.117%20fasilitas%20Medan-A8542A)

</div>

> **Status proyek.**
> Aplikasi sudah terhubung ke basis data. Tabel `facilities` berisi
> **1.117 fasilitas asli** Kota Medan hasil impor dataset OpenStreetMap, dan
> frontend membacanya lewat `GET /api/facilities`. Login admin memakai autentikasi
> sesi Laravel sungguhan. Antrean verifikasi memuat **fasilitas yang belum
> diperiksa maupun pengajuan warga**, dan keputusan atas keduanya tersimpan di
> basis data (`facilities`, `submissions`, `verification_events`,
> `submission_proofs`).
>
> Audit 22 Oktober 2026: seluruh 1.117 fasilitas berstatus `pending` dan kolom
> `verified_at`, `verification_method`, serta `evidence_url` masih kosong — jadi
> memang **belum ada satu pun yang terverifikasi** (bukan asumsi dari kekosongan
> status). Keputusan "Tandai terverifikasi" menyimpan identitas petugas, waktu, dan
> catatan, tetapi **tidak** mengubah indikator aksesibilitas yang belum ada
> buktinya. Pengelolaan kategori masih berupa data di sisi klien.

## Daftar Isi

- [Tentang AksesKita](#tentang-akseskita)
- [Permasalahan dan Solusi](#permasalahan-dan-solusi)
- [Fitur Utama](#fitur-utama)
- [Indikator Aksesibilitas](#indikator-aksesibilitas)
- [Alur Penggunaan](#alur-penggunaan)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Tampilan Aplikasi](#tampilan-aplikasi)
- [Struktur Direktori](#struktur-direktori)
- [Skema Basis Data](#skema-basis-data)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi dan Menjalankan](#instalasi-dan-menjalankan)
- [Impor dan Sinkronisasi Data Fasilitas](#impor-dan-sinkronisasi-data-fasilitas)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Autentikasi dan Peran](#autentikasi-dan-peran)
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

AksesKita merapikan informasi tersebut ke dalam satu peta, satu skema data, dan satu
basis data:

- **Mencari** fasilitas berdasarkan nama, alamat, kota, kategori, atau status verifikasi.
- **Menyaring** hanya fasilitas yang benar-benar memiliki atribut tertentu — misalnya
  yang punya ramp *dan* toilet aksesibel sekaligus.
- **Membaca detail** kondisi aksesibilitas, catatan sumber, dan tautan ke data aslinya.
- **Mengusulkan** fasilitas baru atau melaporkan data yang sudah tidak sesuai.
- **Memeriksa** pengajuan sebelum informasi dianggap tepercaya (dashboard verifikasi).

Sasaran penggunanya: penyandang disabilitas dan keluarganya, pendamping atau relawan,
pengelola fasilitas publik, serta masyarakat umum yang ingin memastikan tempat yang
mereka tuju dapat diakses semua orang.

## Permasalahan dan Solusi

| Permasalahan | Pendekatan AksesKita | Status |
| --- | --- | --- |
| Informasi aksesibilitas tersebar dan tidak seragam | Satu skema data: 8 indikator aksesibilitas dengan tiga nilai baku (`yes`, `no`, `unknown`) | ✅ Skema, basis data, dan tampilan sudah ada |
| Sulit menilai kesiapan fasilitas sebelum berkunjung | Peta, daftar kartu dengan pemuatan bertahap, filter atribut, dan halaman detail per fasilitas | ✅ Berjalan dari data basis data |
| Data fasilitas belum terkumpul dalam satu tempat | Dataset OpenStreetMap Kota Medan diimpor ke tabel `facilities` lewat satu perintah idempoten | ✅ 1.117 fasilitas tersimpan |
| Data yang tidak lengkap tampak sama dengan data yang buruk | Indikator `belum diketahui` ditampilkan terpisah dari `tidak tersedia`, plus persentase kelengkapan data | ✅ Berjalan |
| Informasi dari masyarakat perlu diperiksa | Antrean verifikasi dari basis data dengan aksi setujui, tolak, dan minta perbaikan; keputusan dan riwayatnya tersimpan | ✅ Berjalan untuk akun petugas yang sudah masuk |
| 1.117 fasilitas dataset tidak terlihat di menu verifikasi | Antrean digabung di sisi server: fasilitas `pending`/`needs_repair` dan pengajuan warga dalam satu endpoint berhalaman, dengan penyaring jenis, pencarian, dan statistik yang dihitung per record | ✅ Berjalan; keputusan fasilitas menyimpan petugas, waktu, dan catatan tanpa menyentuh indikator |
| Data lama bisa menyesatkan | Penanda "Perlu diperbarui" di tabel fasilitas dashboard admin bila data tidak diperiksa lebih dari 180 hari | ✅ Berjalan (perhitungan di sisi klien) |
| Kontribusi warga belum terverifikasi | Setiap pengajuan membawa jenis, ringkasan, klaim aksesibilitas, dan bukti pendukung | ✅ Tersimpan di basis data; berkas bukti disimpan privat dan hanya dibuka petugas |

AksesKita **belum** menyelesaikan seluruh persoalan di atas. Lapisan data fasilitas,
autentikasi admin, pengajuan warga, dan keputusan verifikasi sudah nyata; yang masih
kurang adalah pemeriksaan lapangan atas indikator aksesibilitas dan pemisahan peran
petugas.

## Fitur Utama

| Fitur | Rute | Keterangan |
| --- | --- | --- |
| Peta fasilitas | `/` | Leaflet + OpenStreetMap, penanda kotak berisi kode kategori, diisi 1.117 fasilitas dari API |
| Pencarian dan filter | `/` | Kata kunci, kategori, status verifikasi, dan kombinasi atribut (client-side, atas data API) |
| Daftar kartu | `/` | 40 kartu per tahap dengan tombol "Tampilkan lagi"; jalur alternatif keyboard/pembaca layar |
| Detail fasilitas | `/fasilitas/:id` | 5 indikator, catatan sumber, koordinat, asal data, dan kelengkapan data |
| Kontribusi masyarakat | `/kontribusi` | Usulan fasilitas baru atau koreksi data, divalidasi server dan disimpan ke tabel `submissions` |
| Status pengajuan | `/status` | Guest memeriksa status usulan atau koreksi lewat surel pengaju, tanpa akun; field sensitif tidak ditampilkan |
| Masuk admin | `/masuk` | Login sesi Laravel dengan proteksi CSRF dan pembatasan percobaan |
| Dashboard verifikasi | `/verifikasi` | Antrean gabungan (fasilitas yang belum diperiksa + pengajuan warga) berhalaman dengan penyaring, detail + bukti, keputusan verifikasi, riwayat keputusan (perlu masuk) |
| Dashboard administrasi | `/admin` | Statistik, tabel fasilitas berhalaman (5 atau 10 baris per halaman), kelengkapan data per kategori, jumlah per kategori, daftar akun (perlu masuk) |
| Tentang dan panduan | `/tentang` | Latar belakang, panduan penggunaan, mekanisme kontribusi dan verifikasi |
| Halaman 404 | `*` | Rute tidak dikenal tetap memakai shell SPA |

**Sudah berjalan:**

- 🗺️ Peta interaktif dengan 1.117 fasilitas asli dalam 7 kategori: kesehatan,
  pemerintahan, pendidikan, penginapan, perbelanjaan, rekreasi, dan transportasi.
- 🔌 `GET /api/facilities` membaca tabel `facilities`; frontend tidak lagi memuat data
  fasilitas dari berkas JavaScript.
- 🔐 Login admin berbasis sesi (`POST /api/login`, `POST /api/logout`, `GET /api/me`),
  dengan `/admin` dan `/verifikasi` diproteksi middleware `auth`.
- 📥 Kontribusi warga tersimpan di basis data: `POST /api/submissions` memvalidasi isian,
  memberi nomor `SUB-####`, dan menyimpannya ke tabel `submissions`. Bukti foto opsional
  diunggah ke storage privat (`submission_proofs`) dan hanya dapat dibuka petugas.
- ⏳ Jeda antrean pengajuan: pengajuan baru disimpan berstatus `queued`, lalu job
  `QueueSubmission` memindahkannya ke `menunggu` setelah jeda 60 detik. Selama `queued`,
  pengajuan tidak muncul di meja verifikasi dan belum dapat diputuskan.
- 🔎 Status pengajuan guest: `GET /api/submissions/status` mencari pengajuan lewat surel
  pengaju (nama boleh sama antara dua orang), menampilkan status, waktu kirim, dan tautan
  fasilitas yang terkait. Surel, isian mentah, ringkasan, dan bukti tidak pernah dikirim.
- 🧾 Keputusan verifikasi nyata: `POST /api/submissions/{id}/decision` menyimpan status,
  catatan, identitas petugas, dan waktu; `POST /api/facilities/{id}/verification` menyimpan
  hasil pemeriksaan fasilitas. Keduanya mencatat riwayat di `verification_events`.
  Usulan yang disetujui membuat baris fasilitas baru; koreksi yang disetujui mengubah
  atribut fasilitas yang dilaporkan.
- 🗂️ Antrean verifikasi gabungan: `GET /api/verification-queue` menampilkan fasilitas dari
  dataset yang masih `pending`/`needs_repair` bersama usulan dan koreksi warga, lengkap
  dengan pagination, penyaring (perlu/dataset/usulan/koreksi/menunggu/perbaikan/selesai),
  pencarian, dan statistik yang dihitung di sisi server.
- 🔎 Filter bekerja pada peta dan daftar sekaligus; memilih atribut berarti "harus
  memiliki semua atribut ini", bukan "salah satu".
- 📊 Persentase kelengkapan data per fasilitas, dihitung dari jumlah indikator yang
  bukan `unknown`.
- 🖼️ Slider foto lokal di hero beranda (crossfade 6 detik, `object-fit: cover`, diam saat
  `prefers-reduced-motion`) — berkas di `public/images/hero/`, daftar di `heroSlides`.
- 🧭 Navigasi SPA dengan riwayat browser, judul halaman per rute, dan tautan aktif.
- ♿ Status selalu ditampilkan sebagai label + glyph, tidak mengandalkan warna saja.

**Belum tersedia:**

- Peran pengguna selain admin: tidak ada kolom `role`, `admin` dan `verifikator` belum
  dipisahkan.
- Pengajuan ulang yang terhubung ke pengajuan lama: pengaju tidak punya akun, jadi
  "minta perbaikan" tidak mengirim pemberitahuan dan pengajuan baru dibuat dari formulir.
  Pengaju bisa memeriksa keputusan itu sendiri lewat `/status` (dicari dengan surel), tetapi
  tidak ada notifikasi dan tidak ada keterkaitan ke pengajuan lama.
- Pemeriksaan lapangan: menyetujui pengajuan tidak mengubah `verification_status`
  fasilitas menjadi `verified`. Fasilitas baru hasil usulan tetap `pending` sampai ada
  pemeriksaan, dan indikator yang tidak punya bukti tetap `unknown`.
- Riwayat perubahan atribut fasilitas: jejak audit mencatat keputusan verifikasi
  (`verification_events`), tetapi belum mencatat nilai atribut sebelum/sesudah tiap koreksi.
- Bukti per indikator: keputusan fasilitas dan keputusan indikator belum dipisah per
  indikator, jadi "terverifikasi" berarti fasilitasnya sudah diperiksa, bukan bahwa
  semua indikatornya punya bukti.
- Tes komponen Vue, tes end-to-end, linter JavaScript, dan CI.

## Indikator Aksesibilitas

Delapan indikator dipakai di seluruh aplikasi (didefinisikan di
`resources/js/data/meta.js` → `ATTRIBUTES`, dipetakan dari kolom database di
`app/Models/Facility.php` → `ATTRIBUTE_COLUMNS`):

| Kunci | Indikator | Yang dicatat |
| --- | --- | --- |
| `accessible_ramp` | Ramp / Jalur Landai | Jalur landai di pintu masuk, kemiringan ramah kursi roda |
| `accessible_toilet` | Toilet Aksesibel | Toilet dengan ruang manuver, pegangan, dan pintu lebar |
| `accessible_elevator` | Lift Aksesibel | Lift dengan tombol braille dan penanda suara |
| `accessible_pedestrian_pathway` | Jalur Pejalan Kaki Aksesibel | Jalur di dalam area yang bebas tangga dan hambatan |
| `accessible_entrance` | Pintu Masuk Aksesibel | Pintu masuk bebas hambatan, lebar minimal 80 cm |
| `tactile_guidance_path` | Guiding Block / Jalur Pemandu Tunanetra | Ubin pemandu taktil yang mengarahkan tunanetra di area publik |
| `accessible_information_signage` | Informasi dan Petunjuk Aksesibel | Papan informasi dengan huruf jelas, kontras, dan braille |
| `accessible_parking` | Parkir Khusus Disabilitas | Ruang parkir khusus dengan akses datar ke pintu masuk |

Kunci di atas dipakai apa adanya di tiga tempat: nama kolom database
(`<kunci>_status` dan `<kunci>_details`), isi `payload` pengajuan, dan kunci atribut di
frontend. Tidak ada terjemahan nilai di sisi data — pergantian bahasa hanya mengubah
label yang ditampilkan.

Setiap indikator bernilai salah satu dari tiga status, dan **nilai inilah yang disimpan
di database** (`enum('yes','no','unknown')`):

| Nilai di data | Arti | Label tampilan |
| --- | --- | --- |
| `yes` | Tercatat tersedia berdasarkan informasi yang dikumpulkan | Tersedia |
| `no` | Dinyatakan tidak tersedia berdasarkan informasi yang dikumpulkan | Tidak tersedia |
| `unknown` | Belum diketahui atau informasinya belum memadai | Belum diketahui |

> `unknown` **bukan** `no`. Fasilitas yang toiletnya belum pernah diperiksa tidak
> boleh dianggap tidak punya toilet aksesibel. Karena itu `unknown` tidak pernah
> dihitung sebagai "tersedia" pada filter, dan justru menurunkan persentase
> kelengkapan data.

Status verifikasi tingkat fasilitas: `terverifikasi`, `menunggu`, `perlu-perbaikan`,
dan `ditolak` (kolom `facilities.verification_status`; nilai database `pending`,
`verified`, `needs_repair`, `rejected`). Status pengajuan: `queued`, `menunggu`, `disetujui`,
`ditolak`, `perbaikan`.
Data yang tidak diperiksa lebih dari **180 hari** ditandai **Perlu diperbarui** pada tabel
fasilitas di dashboard admin (`STALE_DAYS` di `meta.js`).

### Aturan fasilitas yang membutuhkan verifikasi

Antrean verifikasi hanya memuat record yang memang belum punya pemeriksaan sah
(`VerificationQueueController`, kunci yang sama dengan `Facility::NEEDS_VERIFICATION`
dan `NEEDS_VERIFICATION` di `meta.js`):

| Kelompok | Isi antrean | Penghitung |
| --- | --- | --- |
| Belum diverifikasi / menunggu | `verification_status = pending` | `stats.facilitiesPending` |
| Perlu perbaikan | `verification_status = needs_repair` | `stats.facilitiesNeedsRepair` |
| Pengajuan belum diputuskan | `submissions.status` = `menunggu` atau `perbaikan` | `stats.submissionsOpen` |
| Pengajuan dalam jeda antrean | `submissions.status = queued` — **tidak** tampil di antrean sampai job antrean memindahkannya | `stats.submissionsQueued` |
| Terverifikasi | `verification_status = verified` — **tidak** dihitung sebagai antrean | `stats.facilitiesVerified` |
| Ditolak | `verification_status = rejected` — baris fasilitas tetap tersimpan, hanya tampil pada penyaring "sudah diputuskan" | `stats.facilitiesRejected` |

- Nama, kategori, atau kelengkapan alamat **tidak** dipakai sebagai bukti verifikasi.
- Setiap record dihitung sekali: fasilitas dengan tiga laporan koreksi tetap terhitung
  satu di `facilitiesPending`; tiga laporan itu terhitung sebagai tiga `submissionsOpen`
  karena memang tiga keputusan berbeda. Tidak ada `JOIN` pada statistik, jadi tidak ada
  penggandaan angka.
- Keputusan "Tandai terverifikasi" menulis `verification_status`, `verified_at`,
  `verified_by`, `verification_method`, dan `verification_note`, tanpa menyentuh lima
  kolom indikator aksesibilitas. Indikator hanya berubah lewat keputusan koreksi.
- Hasil audit 22 Oktober 2026 (sebelum perubahan ini): 1.117 baris `pending`, 0 baris
  `verified`/`rejected`, dan `verified_at`/`verification_method`/`evidence_url` kosong
  seluruhnya. Karena itu tidak ada migrasi status massal — status lama sudah jujur
  menggambarkan keadaan.

## Alur Penggunaan

1. **Buka beranda** (`/`) — peta dan daftar fasilitas tampil berdampingan. Daftar kartu
   adalah jalur alternatif untuk pengguna keyboard dan pembaca layar.
2. **Cari atau saring** — ketik nama/alamat/kota, pilih kategori, status verifikasi,
   atau atribut yang wajib ada. Jumlah hasil ikut berubah di peta dan daftar.
3. **Buka detail** (`/fasilitas/AKS-0001`) — periksa delapan indikator, catatan sumber,
   koordinat, dan tautan ke data OpenStreetMap aslinya.
4. **Kirim kontribusi** (`/kontribusi`) — pilih "usulan fasilitas baru" atau "koreksi
   data", isi lokasi lewat peta, lampirkan foto bila ada, lalu kirim. Server menolak
   isian yang tidak lengkap dan mengembalikan pesan kesalahannya ke formulir.
5. **Masuk sebagai admin** (`/masuk`) — dashboard verifikasi dan administrasi hanya
   dapat dibuka setelah masuk.
6. **Verifikasi** (`/verifikasi`) — pilih penyaring (semua yang butuh verifikasi, fasilitas
   dataset, usulan, koreksi, menunggu, perlu perbaikan, sudah diputuskan), buka detail,
   lalu putuskan: fasilitas ditandai terverifikasi/perlu perbaikan/ditolak, pengajuan
   disetujui/diminta perbaikan/ditolak. Semuanya tersimpan di basis data bersama nomor,
   keputusan, petugas, dan waktunya.

## Teknologi yang Digunakan

| Teknologi | Versi | Peran dalam proyek |
| --- | --- | --- |
| PHP | `^8.3` (terpasang 8.5.9) | Bahasa server |
| Laravel | `^13.17` | Kerangka backend, routing, Blade shell, migrasi, autentikasi sesi |
| Vue 3 | `3.5.44` | Seluruh antarmuka SPA (`<script setup>`) |
| Vue Router | `5.4.0` | 8 rute + halaman 404, mode history, penjaga rute admin |
| Vite | `8.3.4` | Build aset, HMR, unduh font saat build |
| `laravel-vite-plugin` | `3.2.0` | Menghubungkan Vite dengan Blade (`@vite`, `Vite::fonts()`) |
| `@vitejs/plugin-vue` | `6.0.10` | Kompilasi komponen `.vue` |
| Leaflet | `1.9.4` | Peta interaktif, penanda `L.divIcon` tanpa aset gambar |
| OpenStreetMap | — | Sumber tile peta dan sumber dataset fasilitas (butuh internet) |
| Tailwind CSS | `4.x` | Terpasang dan aktif di Vite; antarmuka sendiri memakai token CSS di `resources/css/app.css` |
| MySQL | 8.x | Basis data: `facilities`, `users`, `sessions`, `cache`, `jobs` |
| PHPUnit | `12.5` | Tes backend |
| Laravel Pint | `1.27` | Perapian gaya kode PHP |
| Composer / npm | 2.x / 11.x | Manajemen dependensi PHP dan frontend |

Tidak ada library state management, HTTP client, atau komponen UI eksternal. State
bersama memakai composable singleton (`useFacilities`, `useVerification`,
`useSubmissions`, `useAuth`, `useToast`), dan seluruh gaya ditulis sebagai kelas CSS biasa.

## Tampilan Aplikasi

| Halaman | Isi utama |
| --- | --- |
| Beranda | Hero ringkas dengan slider foto, peta fasilitas, panel filter, daftar kartu bertahap, legenda peta |
| Detail fasilitas | Nama, kategori, alamat, delapan indikator, catatan sumber, koordinat, status verifikasi |
| Kontribusi | Dua jenis formulir, pemilih lokasi di peta, pratinjau foto, daftar kesalahan |
| Masuk | Formulir surel + kata sandi dengan pesan kesalahan yang terbaca pembaca layar |
| Verifikasi | Antrean gabungan berhalaman (fasilitas + pengajuan), penyaring, detail fasilitas atau pengajuan, aksi keputusan, riwayat keputusan |
| Administrasi | Kartu statistik, tabel fasilitas dengan pagination, diagram kelengkapan, kelola kategori, daftar akun |
| Tentang | Latar belakang, panduan langkah demi langkah, mekanisme kontribusi dan verifikasi |

Repositori ini **belum** menyimpan tangkapan layar. Daripada menampilkan gambar yang
tidak dapat diverifikasi, dokumentasi visual sengaja dikosongkan. Untuk menambahkannya:

1. Jalankan aplikasi, ambil tangkapan layar beranda, detail, dan verifikasi.
2. Simpan di `docs/screenshots/` dengan nama deskriptif, misalnya `beranda.png`.
3. Sisipkan dengan path relatif, contoh:
   `![Beranda AksesKita: peta dan daftar fasilitas](docs/screenshots/beranda.png)`.

### Slider foto di hero beranda

Kotak visual di sisi kanan hero beranda menampilkan beberapa foto secara bergantian
(crossfade, jeda 6 detik). Foto adalah berkas lokal di `public/images/hero/`; tidak ada
gambar yang dihotlink dari situs lain.

- Daftar foto: `heroSlides` di `resources/js/views/HomeView.vue`.
- Gaya dan animasi: `.hero__frame` dan `.hero__frame img` di `resources/css/app.css`.
- Setiap `<img>` memakai `object-fit: cover`, jadi foto memenuhi kotak tanpa distorsi
  (`aspect-ratio` kotak 3/4 di desktop, 4/5 di layar kecil).
- Foto bersifat dekoratif (`alt=""`), sedangkan angka fasilitas pada label hitam
  (`.stamp`) tetap berasal dari data `/api/facilities`.
- Saat pengguna memilih `prefers-reduced-motion: reduce`, rotasi otomatis tidak
  dijalankan dan transisi menjadi hampir instan.

Menambah foto:

1. Simpan berkas ke `public/images/hero/` (mis. `3-sekolah.jpg`), idealnya maksimal
   1600 px pada sisi terpanjang dan kualitas JPEG ~78.
2. Tambahkan satu entri ke `heroSlides` di `HomeView.vue`.

> **Lisensi foto.** Dua foto yang saat ini dipakai berasal dari situs pihak ketiga dan
> disalin ke repositori ini sebagai berkas lokal. Pemilik repositori perlu memastikan
> lisensi serta atribusinya, atau menggantinya dengan foto milik sendiri, sebelum
> aplikasi dipublikasikan.

## Struktur Direktori

```text
fasilitas-disabilitas/
├── app/
│   ├── Console/Commands/       # facilities:import (pembaca dataset + laporan)
│   ├── Http/Controllers/       # AuthController (login/logout), FacilityController (API),
│   │                           # SubmissionController (antrean + keputusan verifikasi),
│   │                           # VerificationQueueController (antrean gabungan)
│   ├── Jobs/                   # QueueSubmission (jeda antrean pengajuan)
│   └── Models/                 # Facility (peta status + toPayload), Submission,
│                               # SubmissionProof, VerificationEvent, User
├── bootstrap/app.php           # Registrasi route web/api + redirect guest
├── config/                     # Konfigurasi Laravel
├── database/
│   ├── factories/              # UserFactory
│   ├── migrations/             # users/sessions, cache, jobs, facilities, submissions, verifikasi fasilitas
│   └── seeders/                # DatabaseSeeder (akun admin)
├── Dataset/
│   └── akseskita_facilities_medan.csv   # Sumber 1.117 fasilitas (read-only)
├── public/                     # Document root; build aset Vite diabaikan Git
│   └── images/hero/            # Foto slider hero beranda (berkas lokal, bukan hotlink)
├── resources/
│   ├── css/app.css             # Token desain + seluruh kelas komponen
│   ├── js/
│   │   ├── components/         # layout (PublicLayout/AdminLayout + header publik/admin),
│   │   │                       # common, map, facilities, forms, verification, dashboard
│   │   ├── composables/        # useFacilities, useVerification, useSubmissions, useAuth, useToast
│   │   ├── data/               # meta (label/glyph), facilities (helper kelengkapan)
│   │   ├── router/index.js     # Rute SPA, judul halaman, penjaga rute admin
│   │   ├── views/              # 9 halaman: beranda, detail, kontribusi, status, masuk,
│   │   │                       # verifikasi, admin, tentang, 404
│   │   ├── App.vue             # Pemilih layout (meta.layout admin/publik) + host toast
│   │   └── app.js              # Titik masuk Vue (memuat sesi lebih dulu)
│   └── views/
│       ├── app.blade.php       # Satu-satunya shell aplikasi (#app + @vite)
│       └── welcome.blade.php   # Halaman bawaan Laravel (tidak dipakai rute)
├── routes/
│   ├── api.php                 # GET /api/facilities
│   └── web.php                 # Endpoint sesi /api/*, submissions, halaman yang butuh auth, SPA fallback
├── storage/app/private/import-reports/  # Laporan impor (dibuat otomatis)
├── storage/app/private/submission-proofs/  # Bukti pengajuan (privat, dibuat saat ada unggahan)
├── tests/                      # AksesKitaTest, AuthTest, FacilityImportTest, SubmissionTest + fixture CSV
├── TemplateDesignWeb/          # Referensi desain asli (read-only, bukan bagian aplikasi)
├── vite.config.js              # Plugin Vue, Tailwind, dan unduh font Bunny
├── composer.json               # Dependensi dan skrip PHP
└── package.json                # Dependensi dan skrip frontend
```

## Skema Basis Data

Tujuh tabel dibuat oleh migrasi: `users` (+ `password_reset_tokens` dan `sessions`),
`cache`, `jobs`, `facilities`, `submissions`, `submission_proofs`, dan
`verification_events`.

Tabel **`facilities`** (`database/migrations/2026_10_22_000000_create_facilities_table.php`)
mengikuti skema berkas dataset:

| Kelompok | Kolom |
| --- | --- |
| Identitas | `id`, `external_id` (unik, mis. `AKS-0001`), `name`, `category`, `source_category`, `source_category_detail` |
| Lokasi | `address`, `village`, `subdistrict`, `city`, `province`, `postal_code`, `latitude`, `longitude` |
| Aksesibilitas | delapan pasang `<kunci>_status`/`<kunci>_details` (lihat [Indikator Aksesibilitas](#indikator-aksesibilitas)) plus `entrance_width_cm`, `entrance_width_measured` |
| Verifikasi | `verification_status`, `verified_at`, `verified_by`, `verification_method`, `verification_note`, `evidence_url` |
| Sumber | `source_name`, `source_url`, `source_ref_type`, `source_ref_id`, `collected_at` |
| Ringkasan | `data_completeness_percent`, `notes`, `timestamps` |

Catatan penting:

- `external_id` adalah id publik yang dipakai frontend (`/fasilitas/AKS-0001`).
- Kolom status aksesibilitas memakai `enum('yes','no','unknown')` dengan nilai awal
  `unknown`, supaya ketiadaan bukti tidak pernah terbaca sebagai "tidak tersedia".
- `verification_status` memakai `enum('pending','verified','needs_repair','rejected')`.
  `needs_repair`, `verified_by`, dan `verification_note` ditambahkan migrasi
  `2026_10_22_110000_add_facility_verification.php`; migrasi itu **tidak mengubah satu
  baris pun** — audit menunjukkan semua baris tetap `pending` tanpa bukti pemeriksaan.
- Indeks tambahan ada pada `(latitude, longitude)`, `category`, dan `verification_status`.
- Tabel ini **additif**: migrasinya tidak menyentuh tabel lain dan `down()` hanya
  menghapus tabel `facilities` bila dijalankan sendiri.

Tiga tabel kontribusi dan verifikasi
(`database/migrations/2026_10_22_100000_create_submissions_tables.php`, lalu
`2026_10_22_110000_add_facility_verification.php`) juga additif: tidak ada baris tabel
lama yang diubah, hanya penambahan kolom dan penggantian nama tabel riwayat.

| Tabel | Isi | Kolom penting |
| --- | --- | --- |
| `submissions` | Satu baris per pengajuan warga | `public_id` (`SUB-0001`, unik), `type` (`usulan`/`koreksi`), `facility_id` (untuk koreksi), `facility_name`, `category`, `city`, `submitted_by`, `contact`, `summary`, `payload` (JSON isian mentah), `status` (`queued`/`menunggu`/`disetujui`/`perbaikan`/`ditolak`), `decided_by`, `decided_at`, `created_facility_id` |
| `submission_proofs` | Bukti foto pendukung, satu baris per berkas | `disk`, `path`, `original_name`, `mime`, `size` |
| `verification_events` | Riwayat keputusan (jejak audit) untuk **pengajuan maupun fasilitas** | `submission_id` (nullable), `facility_id` (nullable), `status_from`, `status_to`, `note`, `actor` |

Catatan penting:

- `public_id` diisi dari primary key di dalam transaksi yang sama, jadi nomor pengajuan
  tidak pernah bentrok.
- `payload` menyimpan isian pengaju apa adanya: kunci atribut memakai delapan kunci
  netral bahasa, dan status memakai nilai `yes`/`no`/`unknown` — sama dengan kolom
  fasilitas, jadi tidak ada pemetaan kosakata saat usulan disetujui. Migrasi
  `2026_10_26_000000_refactor_accessibility_attributes.php` memetakan isian lama
  (`ramp`…`entrance`, `tersedia`/`tidak`/`belum`) ke bentuk baru tanpa mengubah baris
  fasilitas.
- `verification_events` mencatat satu baris per keputusan, baik untuk pengajuan
  (`submission_id`) maupun fasilitas (`facility_id`). Riwayat inilah yang ditampilkan di
  halaman `/verifikasi` dan dihitung ulang pada setiap pemuatan halaman.
- Keputusan verifikasi fasilitas menulis kolom verifikasi di `facilities` dan **tidak**
  menyentuh kolom indikator aksesibilitas; keputusan koreksi yang disetujui tetap tidak
  menandai fasilitasnya `verified`.
- Baris pengajuan tidak dihapus ketika fasilitasnya dihapus: foreign key memakai
  `nullOnDelete`, dan bukti dihapus hanya bersama pengajuan induknya (`cascadeOnDelete`).
- Bukti disimpan di disk privat `local` (`storage/app/private/submission-proofs`), bukan
  di `public/`, dan dibuka lewat rute yang butuh sesi petugas.
- Tidak ada perintah yang menghapus fasilitas atau pengajuan secara massal.

## Persyaratan Sistem

| Kebutuhan | Versi minimum | Catatan |
| --- | --- | --- |
| PHP | 8.3 | Mengikuti `composer.json` |
| Composer | 2.x | Manajemen dependensi PHP |
| Node.js | 20.19 atau 22.12+ | Batas dari Vite 8 |
| npm | 10+ | Manajemen dependensi frontend |
| MySQL | 8.x | Alternatif: SQLite untuk pengembangan lokal |
| Git | 2.x | Kontrol versi |

Butuh koneksi internet untuk tiga hal: mengunduh font (Syne, Inter, DM Mono) saat
`npm run build`/`npm run dev`, memuat tile OpenStreetMap saat peta dibuka, dan
memuat halaman OpenStreetMap dari tautan sumber data.

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

Jalankan migrasi dan seeder akun admin:

```powershell
php artisan migrate
php artisan db:seed
```

Migrasi membuat tabel `users`, `sessions`, `cache`, `jobs`, dan `facilities`.
Seeder membuat satu akun admin (`admin@akseskita.test`) dengan kata sandi dari
`SEED_PASSWORD` (bawaan `password`). **Ganti kata sandi itu sebelum dipakai di luar
mesin lokal.**

### 4. Impor dataset fasilitas

```powershell
php artisan facilities:import
```

Perintah ini mengisi tabel `facilities` dengan 1.117 fasilitas dari
`Dataset/akseskita_facilities_medan.csv`. Rinciannya ada di
[Impor dan Sinkronisasi Data Fasilitas](#impor-dan-sinkronisasi-data-fasilitas).

### 5. Bangun aset frontend

```powershell
npm run build
```

### 6. Jalankan aplikasi

**Opsi A — satu perintah (disarankan).** `composer run dev` menjalankan
`php artisan dev`, yang menyalakan tiga proses sekaligus:

```powershell
composer run dev
```

- server Laravel di `http://127.0.0.1:8000`
- queue listener
- Vite (HMR untuk `resources/js` dan `resources/css`)

Opsi A memakai HTTP pada server artisan. Untuk URL aplikasi kanonik lokal, gunakan Opsi C.

**Opsi B — terpisah di dua terminal.**

```powershell
php artisan serve
npm run dev
```

**Opsi C — virtual host Laragon.** Arahkan document root ke folder `public/`
(misalnya `https://fasilitas-disabilitas.test`), tambahkan host di `hosts`, lalu
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
Database harus sudah dibuat sebelum perintah dijalankan. Pintasan ini **tidak**
menjalankan seeder dan **tidak** mengimpor dataset — jalankan `php artisan db:seed`
dan `php artisan facilities:import` setelahnya.

## Impor dan Sinkronisasi Data Fasilitas

Sumber tunggal data fasilitas adalah `Dataset/akseskita_facilities_medan.csv`
(1.117 baris data, 36 kolom). Berkas ini **tidak boleh diubah** — perbaikan format
dilakukan di sisi pembaca.

| Perintah | Kegunaan |
| --- | --- |
| `php artisan facilities:import` | Impor dataset bawaan dari `Dataset/` |
| `php artisan facilities:import "path/file.csv"` | Impor berkas lain dengan skema kolom yang sama |
| `php artisan facilities:import --dry-run` | Validasi dan ringkasan tanpa menyentuh basis data |

Sifat perintah ini:

- **Idempoten.** Baris dengan `external_id` yang sudah ada tidak dibuat ulang.
- **Tidak menimpa hasil verifikasi.** Baris yang `verification_status`-nya bukan
  `pending` dilewati, bukan ditimpa.
- **Bertransaksi per 200 baris** dan melaporkan ringkasan
  (dibaca / dibuat / diperbarui / dilewati / gagal).
- **Tidak pernah menghapus** data. Tidak ada operasi massal yang merusak.
- Menulis laporan per baris ke `storage/app/private/import-reports/facilities-<tanggal>.csv`,
  termasuk alasan baris yang gagal.
- Menolak berkas tanpa kolom wajib, dan menolak baris dengan kategori di luar
  pemetaan, koordinat tidak numerik, atau nama kosong.

Pemetaan kategori dataset → kategori aplikasi ada di `Facility::CATEGORY_MAP`; kategori
yang tidak dikenal membuat baris tersebut gagal dan tercatat di laporan, bukan
ditebak. Dataset yang berisi bacaan lengkap hasil impor terakhir ada di
[Lampiran: hasil validasi](#lampiran-hasil-validasi-dataset).

### Kalau dataset bertambah

Jalankan `php artisan facilities:import` lagi. Baris baru ditambahkan, baris lama
dilewati, dan laporan baru dibuat. Tidak perlu menghapus tabel lebih dulu.

## Konfigurasi Environment

| Variabel | Kegunaan | Contoh aman |
| --- | --- | --- |
| `APP_NAME` | Nama aplikasi | `AksesKita` |
| `APP_ENV` | Lingkungan | `local` |
| `APP_KEY` | Kunci enkripsi Laravel | Dibuat oleh `php artisan key:generate` |
| `APP_URL` | URL dasar aplikasi | `https://fasilitas-disabilitas.test` (Laragon) atau `http://localhost:8000` (artisan serve) |
| `APP_DEBUG` | Detail error | `true` hanya di lokal |
| `DB_CONNECTION` | Driver database | `mysql` |
| `DB_DATABASE` | Nama database | `fasilitas_disabilitas` |
| `DB_USERNAME` / `DB_PASSWORD` | Kredensial database | Isi sesuai mesin masing-masing |
| `SESSION_DRIVER` | Penyimpanan sesi | `database` (bawaan; butuh tabel `sessions`) |
| `SESSION_SECURE_COOKIE` | Cookie hanya lewat HTTPS | `true` pada URL HTTPS; `false` atau kosong saat memakai artisan serve HTTP |
| `QUEUE_CONNECTION` | Antrean | `database` (bawaan) |
| `SEED_PASSWORD` | Kata sandi awal akun admin hasil seeder | Ganti dari bawaan `password` |
| `VITE_APP_NAME` | Nama aplikasi untuk sisi Vite | `${APP_NAME}` |

- File `.env` **tidak** diunggah ke GitHub; `.gitignore` sudah mengecualikannya.
- `.env.example` hanya berisi placeholder tanpa kredensial asli.
- Tes memaksa SQLite `:memory:` lewat `phpunit.xml`, sehingga tidak pernah menyentuh
  database lokal.
- Jangan menaruh token, kunci API, atau kata sandi di dalam kode maupun di README.

## Autentikasi dan Peran

Autentikasi memakai sesi Laravel berbasis cookie (bukan token API), sehingga token
CSRF dikirim lewat cookie `XSRF-TOKEN`.

| Endpoint | Middleware | Kegunaan |
| --- | --- | --- |
| `POST /api/login` | `throttle:6,1` | Masuk dengan surel + kata sandi |
| `POST /api/logout` | `auth` | Mengakhiri sesi |
| `GET /api/me` | — | Profil pengguna yang sedang masuk, atau `null` untuk pengunjung |
| `GET /api/users` | `auth` | Daftar akun (hanya untuk admin) |
| `GET /api/facilities` | — | Seluruh fasilitas (1.117 baris) untuk peta dan agregat dashboard |
| `GET /api/facilities?page=1&per_page=10` | — | Satu halaman untuk tabel Pengelolaan fasilitas di `/admin` (maks. 100 baris per permintaan) |
| `POST /api/submissions` | `throttle:10,1` | Pengajuan warga (usulan atau koreksi) beserta bukti foto opsional; terbuka untuk pengunjung tanpa akun |
| `GET /api/submissions/status` | `throttle:10,1` | Status pengajuan warga dicari lewat surel pengaju (`?email=`, `?name=` opsional); terbuka tanpa akun, maks. 50 pengajuan terbaru |
| `GET /api/verification-queue` | `auth` | Antrean verifikasi gabungan: `?bucket=perlu\|dataset\|usulan\|koreksi\|menunggu\|perbaikan\|selesai`, `?q=`, `?page=`, `?per_page=` (maks. 50). Mengembalikan `items`, `meta`, `stats`, dan `history` |
| `POST /api/submissions/{id}/decision` | `auth` | Keputusan atas pengajuan (`disetujui`, `perbaikan`, `ditolak`); catatan wajib untuk minta perbaikan dan tolak |
| `POST /api/facilities/{id}/verification` | `auth` | Keputusan atas fasilitas (`verified`, `needs_repair`, `rejected`); catatan wajib untuk minta perbaikan dan tolak |
| `GET /api/submissions/{id}/proofs/{proof}` | `auth` | Menyajikan berkas bukti pendukung yang bersifat privat |

Aturan keputusan yang dijalankan server (bukan hanya tampilan):

- Pengajuan yang sudah `disetujui` atau `ditolak` tidak dapat diputuskan lagi.
- Pengajuan yang masih `queued` belum masuk antrean verifikasi dan tidak dapat diputuskan;
  setelah jeda antrean lewat, statusnya `menunggu` dan baru muncul di meja petugas.
- Fasilitas yang sudah `verified` tidak dapat ditandai terverifikasi dua kali (422).
- Keputusan atas fasilitas menulis `verification_status`, `verified_at`, `verified_by`,
  `verification_method`, dan `verification_note`; kolom indikator aksesibilitas tidak
  disentuh sama sekali.
- Keputusan `ditolak` pada fasilitas tidak menghapus baris fasilitasnya, juga tidak
  dianggap menolak pengajuan warga yang tertaut.
- Keputusan menyimpan nama petugas dari sesi yang sedang masuk dan waktunya; tidak ada
  identitas yang dikarang.
- Usulan yang disetujui membuat fasilitas baru dengan `external_id` berikutnya
  (`AKS-####`) dan `verification_status` tetap `pending`; klaim `unknown` tetap `unknown`.
- Koreksi yang disetujui mengubah atribut fasilitas yang dilaporkan; koreksi yang ditolak
  tidak mengubah apa pun.
- Pengajuan ditolak tidak pernah membuat fasilitas baru.
- Kiriman identik dari pengaju yang sama dalam 60 detik ditolak sebagai kiriman ganda.

Tamu yang membuka `/admin` atau `/verifikasi` diarahkan ke `/masuk`, sedangkan admin
yang sudah masuk dan membuka `/masuk` diarahkan ke `/admin`. Penjaga rute Vue hanya
untuk pengalaman pengguna; penegakan sesungguhnya ada di middleware `auth`.

> `shortcut:` saat ini hanya ada satu peran, yaitu admin, jadi "peran admin" berarti
> "sedang masuk". Tambahkan kolom `role` dan `Gate` saat peran staf kedua
> (verifikator) benar-benar dibutuhkan. Lihat komentar di `app/Models/User.php`.

## Pengujian dan Pengembangan

```powershell
# Tes backend (PHPUnit 12)
php artisan test

# Satu berkas tes
php artisan test tests/Feature/FacilityImportTest.php

# Periksa format PHP tanpa mengubah berkas
vendor/bin/pint --test

# Rapikan format PHP
vendor/bin/pint

# Build aset frontend
npm run build
```

Saat README ini ditulis, `php artisan test` menjalankan **53 tes dengan 359 asersi dan
lulus**: tiga tes shell/rute SPA (`AksesKitaTest`), lima tes impor dan bentuk payload
API (`FacilityImportTest`), tujuh tes autentikasi (`AuthTest`), dua belas tes kontribusi
dan keputusan pengajuan (`SubmissionTest`), sepuluh tes antrean verifikasi gabungan dan
keputusan fasilitas (`VerificationQueueTest`), lima tes jeda antrean pengajuan
(`QueueSubmissionTest`), sembilan tes status pengajuan guest (`SubmissionStatusTest`),
dan dua tes contoh bawaan Laravel.
Perintah `vendor/bin/pint --test` dan `npm run build` juga lulus.

Yang belum ada: tes untuk komponen Vue, tes end-to-end, linter JavaScript, dan CI.
Jangan menganggap perubahan frontend "sudah teruji" hanya karena tes PHP lulus.

## Roadmap

**Sudah selesai**

- [x] Impor dataset 1.117 fasilitas ke tabel `facilities` dengan perintah idempoten
- [x] API `GET /api/facilities` + integrasi peta, daftar, dan dashboard ke basis data
- [x] Pemuatan bertahap pada daftar kartu dan pagination tabel Pengelolaan fasilitas (5/10 baris per halaman, di sisi server)
- [x] Autentikasi sesi untuk admin dan proteksi `/admin` serta `/verifikasi`
- [x] Peta Leaflet + OpenStreetMap dengan penanda berkode kategori
- [x] Pencarian, filter kategori, status verifikasi, dan kombinasi atribut
- [x] Halaman detail dengan delapan indikator, catatan sumber, dan status verifikasi
- [x] Formulir usulan fasilitas dan koreksi data dengan validasi di klien dan server
- [x] Tabel `submissions`, `submission_proofs`, dan `verification_events` + API tulis pengajuan
- [x] Keputusan verifikasi tersimpan di basis data beserta identitas petugas, waktu, dan riwayatnya
- [x] Unggah bukti foto ke storage privat dan penyajiannya lewat rute berproteksi
- [x] Antrean verifikasi gabungan berhalaman: fasilitas dataset yang belum diperiksa + pengajuan warga, dengan penyaring, pencarian, dan statistik server
- [x] Keputusan verifikasi tingkat fasilitas (`pending`/`verified`/`needs_repair`/`rejected`) tanpa mengubah indikator aksesibilitas
- [x] Dashboard verifikasi dan administrasi yang membaca antrean, statistik, dan riwayat dari basis data
- [x] Halaman status pengajuan guest (`/status`) dengan kunci surel pengaju, tanpa akun
- [x] Jeda antrean pengajuan lewat job `QueueSubmission` (status `queued` → `menunggu`)
- [x] Tata letak responsif dan status yang selalu disertai teks + glyph
- [x] Shell SPA Laravel dengan rute history dan halaman 404

**Berikutnya**

- [ ] Peran `admin` dan `verifikator` dengan kolom `role` dan `Gate`
- [ ] Pengajuan ulang yang terhubung ke pengajuan "minta perbaikan", bila pengaju diberi akun
- [ ] Riwayat perubahan atribut fasilitas (bukan hanya riwayat keputusan)
- [ ] Pemeriksaan lapangan atas 1.117 fasilitas sehingga indikator `unknown`
      berubah menjadi `yes` atau `no`
- [ ] Agregat dashboard dihitung di server supaya `/admin` tidak lagi mengunduh seluruh baris
- [ ] Pagination dan pencarian antrean verifikasi bila volume pengajuan bertambah
- [ ] Tes Vue, tes end-to-end, dan CI
- [ ] Audit aksesibilitas WCAG 2.2 AA dan perbaikan temuan
- [ ] Tangkapan layar di `docs/screenshots/`

## Kontribusi

1. Fork repositori ini.
2. Tarik perubahan terbaru, lalu buat branch baru dari `master`:
   ```powershell
   git pull --rebase
   git checkout -b feat/nama-perubahan
   ```
3. Lakukan perubahan, lalu jalankan `php artisan test`, `vendor/bin/pint --test`,
   dan `npm run build`.
4. Commit dengan pesan yang jelas, misalnya `feat: tambah filter jarak terdekat`.
5. Push branch dan ajukan pull request ke `master`.

Jangan hapus atau ubah `Dataset/akseskita_facilities_medan.csv` dan jangan commit
ulang data hasil impor. Perubahan skema memerlukan migrasi baru, bukan perubahan
pada migrasi yang sudah dijalankan. Jangan menyertakan `.env`, kredensial, atau
berkas hasil build.

## Lampiran: Hasil Validasi Dataset

Angka berikut berasal dari dataset dan basis data pengembangan pada saat README ini
ditulis. Semuanya dapat diperiksa ulang dengan `php artisan facilities:import --dry-run`.

| Pemeriksaan | Hasil |
| --- | --- |
| Berkas sumber | `Dataset/akseskita_facilities_medan.csv`, 1 header + 1.117 baris data, 36 kolom |
| Baris lolos validasi | 1.117 (0 gagal) |
| `external_id` unik | 1.117 dari 1.117 (`AKS-0001`–`AKS-1117`, tanpa lubang) |
| Baris dibuat saat impor pertama | 1.117 |
| Baris dibuat saat impor ulang | 0 (1.117 dilewati — impor idempoten) |
| Koordinat tidak valid / tertukar | 0 (lat 3,48–3,79; lng 98,60–98,74) |
| Nama atau kategori kosong | 0 |
| Alamat jalan kosong di sumber | 926 dari 1.117 |
| Kode pos kosong di sumber | 1.024 dari 1.117 |
| Cakupan wilayah | Kota Medan, Sumatera Utara — 21 kecamatan, 138 kelurahan |
| Nama + kelurahan kembar | 5 pasang (node dan way untuk tempat sama) — diimpor apa adanya, tidak dihapus |
| Kategori terpakai | kesehatan, pemerintahan, pendidikan, penginapan, perbelanjaan, rekreasi, transportasi |
| Sumber | OpenStreetMap (`way`/`node`/`relation`), dikumpulkan 21 Oktober 2026 |
| Status verifikasi | 100% `pending` (0 terverifikasi) |
| Kelengkapan data | 1.105 fasilitas 0%; 12 fasilitas 40% (punya catatan minimal satu indikator) |
| Jalur kursi roda | 8 `yes`, 4 `no`, 1.105 `unknown` |
| Akses masuk | 8 `yes`, 4 `no`, 1.105 `unknown` |
| Ramp, toilet aksesibel, lift | 1.117 `unknown` — belum ada bukti sama sekali |
| Lebar pintu terukur | 0 baris (sumber belum mengisi) |

> **Jangan membaca tabel ini sebagai klaim kualitas aksesibilitas.** 1.117 adalah
> jumlah fasilitas yang datanya tersedia di sumber, bukan jumlah fasilitas yang sudah
> diperiksa. Baru 8 fasilitas yang punya catatan jalur kursi roda dan akses masuk.

## Tujuan Pembangunan Berkelanjutan

AksesKita berkaitan langsung dengan dua tujuan pembangunan berkelanjutan:

- **SDG 10 — Berkurangnya kesenjangan.** Informasi aksesibilitas yang terbuka membantu
  penyandang disabilitas merencanakan perjalanan dengan lebih setara, tanpa harus
  bergantung pada kabar dari mulut ke mulut.
- **SDG 11 — Kota dan permukiman yang inklusif.** Data kondisi fasilitas publik dapat
  menjadi masukan bagi pengelola dan pemerintah daerah untuk memperbaiki akses.

Keterkaitan ini bersifat konseptual. Belum ada pengukuran dampak sosial, jumlah
pengguna, atau verifikasi lapangan yang dapat diklaim.

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
