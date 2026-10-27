<div align="center">

# AksesKita

**Pemetaan dan verifikasi fasilitas publik ramah disabilitas**

AksesKita membantu masyarakat menemukan fasilitas publik dan melihat informasi aksesibilitasnya sebelum berkunjung.

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Vue](https://img.shields.io/badge/Vue-3-42B883?logo=vuedotjs&logoColor=white)](https://vuejs.org/)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)](https://vite.dev/)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9-199900?logo=leaflet&logoColor=white)](https://leafletjs.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)

</div>

## Fitur

- Peta dan daftar fasilitas publik Kota Medan.
- Pencarian berdasarkan nama, alamat, atau kota.
- Filter kategori, status verifikasi, dan atribut aksesibilitas.
- Detail fasilitas dengan delapan indikator aksesibilitas.
- Usulan fasilitas baru dan laporan koreksi dari masyarakat.
- Halaman publik untuk memeriksa status pengajuan.
- Dashboard admin dan antrean verifikasi.
- Bukti foto pengajuan disimpan di storage privat.
- Status aksesibilitas memakai tiga nilai: `yes`, `no`, dan `unknown`.

Data fasilitas berasal dari dataset OpenStreetMap Kota Medan. Sebanyak 1.117 baris telah diimpor ke tabel `facilities`. Data yang ditolak tidak ditampilkan di peta publik, tetapi tetap disimpan untuk audit.

## Rute utama

| Rute | Kegunaan |
| --- | --- |
| `/` | Peta, pencarian, filter, dan daftar fasilitas |
| `/fasilitas/:id` | Detail fasilitas |
| `/kontribusi` | Usulan fasilitas atau koreksi data |
| `/status` | Cek status pengajuan tanpa login |
| `/masuk` | Login admin |
| `/admin` | Dashboard administrasi |
| `/verifikasi` | Antrean dan keputusan verifikasi |
| `/tentang` | Informasi aplikasi |

## Teknologi

- PHP 8.3+, Laravel 13, PHPUnit 12, Laravel Pint
- Vue 3, Vue Router, Vue I18n
- Vite 8 dan Tailwind CSS 4
- Leaflet dan OpenStreetMap
- MySQL 8.x

State frontend bersama memakai composable singleton. UI menggunakan class CSS dan token desain di `resources/css/app.css`.

## Persyaratan

- PHP 8.3+
- Composer 2.x
- Node.js 20.19 atau 22.12+
- npm 10+
- MySQL 8.x
- Git 2.x

Koneksi internet diperlukan untuk font saat build, tile OpenStreetMap, dan tautan sumber data.

## Instalasi

```powershell
git clone https://github.com/dymmm-0/fasilitas-disabilitas.git
cd fasilitas-disabilitas
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

Buat database kosong, lalu sesuaikan `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fasilitas_disabilitas
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi, seeder, dan impor dataset:

```powershell
php artisan migrate
php artisan db:seed
php artisan facilities:import
npm run build
```

Seeder membuat akun admin menggunakan `SEED_PASSWORD`. Nilai bawaan adalah `password`; ganti sebelum dipakai di luar mesin lokal.

## Menjalankan aplikasi

Perintah yang disarankan:

```powershell
composer run dev
```

Perintah ini menjalankan server Laravel, queue worker, dan Vite. Aplikasi tersedia di `http://127.0.0.1:8000`.

Alternatif, jalankan di terminal terpisah:

```powershell
php artisan serve
npm run dev
```

Gunakan `npm run build` sebelum demo atau deployment. Jangan memakai `npm run dev` di produksi.

## Impor dataset

Sumber data bawaan:

```text
Dataset/akseskita_facilities_medan.csv
```

Perintah:

```powershell
# Impor dataset bawaan
php artisan facilities:import

# Gunakan file CSV lain dengan skema sama
php artisan facilities:import "path/file.csv"

# Validasi tanpa mengubah database
php artisan facilities:import --dry-run
```

Impor bersifat idempoten, tidak menghapus data, dan tidak menimpa fasilitas yang sudah memiliki status verifikasi selain `pending`. Laporan impor disimpan di `storage/app/private/import-reports/`.

Jangan mengubah file CSV bawaan. Perbaikan format dilakukan oleh pembaca dataset.

## Testing dan format kode

```powershell
# Semua tes backend
php artisan test

# Satu file atau tes tertentu
php artisan test tests/Feature/FacilityImportTest.php
php artisan test --filter=AksesKitaTest

# Cek format PHP
vendor/bin/pint --test

# Format PHP
vendor/bin/pint

# Build frontend
npm run build
```

Belum tersedia: test komponen Vue, test end-to-end, linter JavaScript, dan CI.

## Struktur direktori

```text
app/
├── Console/Commands/       # Import dataset
├── Http/Controllers/       # API, autentikasi, pengajuan, verifikasi
├── Jobs/                   # QueueSubmission
└── Models/                 # Facility, Submission, User, dan model terkait

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/app.css             # Token desain dan class UI
├── js/components/          # Komponen Vue
├── js/composables/         # State bersama
├── js/data/                # Label dan helper data
├── js/router/              # Rute SPA
└── js/views/               # Halaman aplikasi

routes/                     # Route API dan web
public/images/hero/         # Foto lokal slider hero
Dataset/                    # Dataset fasilitas
storage/app/private/        # Laporan impor dan bukti pengajuan
TemplateDesignWeb/          # Referensi desain, read-only
tests/                      # Test backend
```

## Catatan keamanan dan data

- `/admin` dan `/verifikasi` dilindungi middleware `auth`; proteksi frontend bukan satu-satunya akses gate.
- Bukti pengajuan berada di disk `local`, bukan di `public/`.
- Endpoint status pengajuan tidak mengirim `contact`, `payload`, ringkasan, atau bukti.
- Status `unknown` berarti belum diketahui, bukan berarti fasilitas tidak tersedia.
- Keputusan verifikasi fasilitas tidak mengubah indikator aksesibilitas tanpa bukti.
- Jangan commit `.env`, kredensial, atau data hasil impor.
- Migrasi baru diperlukan untuk perubahan skema; jangan mengubah migrasi yang sudah dijalankan.

## Lisensi

Lisensi proyek belum ditentukan. Repositori belum memiliki file `LICENSE`; nilai `MIT` di `composer.json` adalah bawaan skeleton Laravel dan bukan pernyataan lisensi proyek.

---

<div align="center">
<sub>AksesKita — informasi aksesibilitas yang jelas sebelum berangkat.</sub>
</div>
