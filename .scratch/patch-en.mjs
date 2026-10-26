// Isi terjemahan yang belum ada + perbaiki hasil MT yang janggal. Idempoten.
import { readFileSync, writeFileSync } from 'node:fs';

const OUT = 'resources/js/i18n/en.json';
const en = JSON.parse(readFileSync(OUT, 'utf8'));

const ADD = {
    '{0}. Kategori {1}. Status {2}': '{0}. Category {1}. Status {2}',
    '△ Minta perbaikan': '△ Request correction',
    '✓ Setujui': '✓ Approve',
    '✓ Tandai terverifikasi': '✓ Mark as verified',
    '✕ Tandai ditolak': '✕ Mark as rejected',
    '✕ Tolak': '✕ Reject',
    'Alamat surel tidak valid.': 'Email address is not valid.',
    'Alamat wajib diisi.': 'Address is required.',
    Beranda: 'Home',
    'Browser ini tidak mendukung pengambilan lokasi. Pilih titik secara manual di peta.': 'This browser does not support location capture. Pick a point manually on the map.',
    'Daftar akun hanya dapat dibaca setelah masuk sebagai admin.': 'The account list can only be read after signing in as admin.',
    Dashboard: 'Dashboard',
    'Detail fasilitas': 'Facility detail',
    'Diperiksa oleh {0}.': 'Checked by {0}.',
    'Diputuskan oleh {0}.': 'Decided by {0}.',
    'Fasilitas sudah ditandai terverifikasi': 'Facility marked as verified',
    'Halaman tidak ditemukan': 'Page not found',
    'Izin lokasi ditolak. Aktifkan izin lokasi untuk situs ini melalui pengaturan browser jika ingin mencoba lagi.': 'Location permission denied. Enable location permission for this site in your browser settings if you want to try again.',
    'Kelengkapan data dihitung dari atribut aksesibilitas yang sudah diketahui.': 'Data completeness is calculated from the accessibility attributes already known.',
    'Keputusan sudah dibuat: {0}. Riwayat tersimpan di basis data.': 'Decision already made: {0}. The history is stored in the database.',
    'Keputusan tersimpan.': 'Decision saved.',
    Kontribusi: 'Contribution',
    'Koordinat lokasi tidak valid. Pilih titik secara manual di peta.': 'Location coordinates are not valid. Pick a point manually on the map.',
    'Koreksi atribut {0}: {1} menjadi {2}.': 'Correct attribute {0}: {1} to {2}.',
    'Kota wajib diisi.': 'City is required.',
    'Latitude harus -90 sampai 90 dan longitude -180 sampai 180.': 'Latitude must be -90 to 90 and longitude -180 to 180.',
    'Lokasi ditemukan. Akurasi sekitar {0} meter; pastikan titik fasilitas sudah benar.': 'Location found. Accuracy about {0} meters; make sure the facility point is correct.',
    'Lokasi ditemukan. Pastikan titik ini benar-benar lokasi fasilitas yang dilaporkan.': 'Location found. Make sure this point is really the location of the reported facility.',
    'Lokasi tidak dapat diambil. Pilih titik secara manual di peta atau coba lagi.': 'Location could not be captured. Pick a point manually on the map or try again.',
    'Lokasi tidak dapat ditemukan. Periksa GPS, sinyal, atau izin lokasi perangkat.': 'Location could not be found. Check GPS, signal, or the device location permission.',
    'Masuk admin': 'Admin sign-in',
    'Nama fasilitas wajib diisi.': 'Facility name is required.',
    'Nama pelapor wajib diisi.': 'Reporter name is required.',
    'Nama pengusul wajib diisi.': 'Proposer name is required.',
    oleh: 'by',
    pada: 'on',
    'Pengambilan lokasi membutuhkan koneksi aman (HTTPS atau localhost). Pilih titik secara manual di peta.': 'Location capture requires a secure connection (HTTPS or localhost). Pick a point manually on the map.',
    'Permintaan gagal.': 'Request failed.',
    'Peta fasilitas publik ramah disabilitas': 'Disability-friendly public facility map',
    'Pilih fasilitas yang ingin dikoreksi.': 'Select the facility you want to correct.',
    'Pilih nilai yang berbeda dari data saat ini.': 'Choose a value different from the current data.',
    'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.': 'Session expired. Reload the page and try again.',
    'Sesi petugas sudah berakhir. Masuk kembali untuk membuka antrean.': 'The officer session has ended. Sign in again to open the queue.',
    Tentang: 'About',
    'Tentang dan panduan': 'About and guide',
    'Tidak ada baris pada halaman ini': 'No rows on this page',
    'Tidak ada fasilitas pada halaman ini': 'No facilities on this page',
    'Tidak ada fasilitas yang cocok': 'No matching facilities',
    'Tidak dapat masuk. Periksa koneksi lalu coba lagi.': 'Cannot sign in. Check your connection and try again.',
    'Ubah kata kunci atau kosongkan filter untuk melihat semua fasilitas.': 'Change the keyword or clear the filters to see all facilities.',
    'Usulan fasilitas {0} di {1}.': 'Proposed facility {0} in {1}.',
    'Waktu pencarian lokasi habis. Coba lagi atau pilih titik secara manual di peta.': 'Location search timed out. Try again or pick a point manually on the map.',
    // Dihitung di template literal, jadi tidak terdeteksi sebagai literal t().
    'Menampilkan {0}–{1} dari {2} fasilitas': 'Showing {0}–{1} of {2} facilities',
    'Menampilkan {0}–{1} dari {2} baris antrean': 'Showing {0}–{1} of {2} queue rows',
    '{0} hari': '{0} days',
    '{0} fasilitas': '{0} facilities',
    'Pilih bahasa': 'Select language',
};

// Perbaikan hasil mesin terjemah: kata benda jamak yang salah, istilah domain,
// dan glyph yang berubah saat diterjemahkan.
const QUALITY = {
    Fasilitas: 'Facilities',
    'Fasilitas dari dataset': 'Facilities from the dataset',
    'Fasilitas dibuat': 'Facilities created',
    Alamat: 'Address',
    'Kata sandi': 'Password',
    Lift: 'Lift',
    Pengaju: 'Submitter',
    'Ramp / jalur landai': 'Ramp / gentle path',
    Sebelumnya: 'Previous',
    Perbelanjaan: 'Shopping',
    Penginapan: 'Accommodation',
    'Jasa Pengiriman': 'Delivery services',
    Nonaktif: 'Inactive',
    Dikirim: 'Sent',
    '≠ Laporan koreksi data': '≠ Data correction report',
    'menurut data sumber.': 'according to source data.',
    'dipetakan warga': 'mapped by citizens',
};

// Kalimat yang dipecah di template (i18n-t + slot) harus menyambung jadi satu
// kalimat utuh, dan artefak em dash dari mesin terjemah harus dibetulkan.
const SENTENCES = {
    'Temukan fasilitas yang bisa {a}': 'Find facilities {a}',
    'diakses semua orang.': 'everyone can access.',
    'dapat digunakan semua orang.': 'be usable by everyone.',
    'Masuk untuk {a}': 'Sign in to {a}',
    'mengelola data.': 'manage the data.',
    'Periksa pengajuan {a}': 'Check submission {a}',
    'Masuk petugas': 'Officer sign-in',
    'Nama pengaju (opsional)': 'Submitter name (optional)',
    'Cek status pengajuan': 'Check submission status',
    'Cek ulang otomatis selama pengajuan masih dalam antrean.': 'Automatic recheck while the submission is still in the queue.',
};

// Perbaikan berbasis nilai untuk kunci yang mengandung em dash / tanda baca.
const VALUE_FIXES = [
    [/facilities-then verify/, 'facilities — then verify'],
    [/card on the side-both feature/, 'card on the side — both feature'],
    [/assigned to the server/, 'assigned by the server'],
    [/Pledges don't require/, "Contributions don't require"],
    [/decision on an application or facility/, 'decision on a submission or facility'],
    [/internal attendant notes/, 'internal officer notes'],
    [/\{a\} facilities as per search/, '{a} facilities matching search'],
    [/· \{c\} city$/, '· {c} cities'],
    [/\{0\} of dataset · \{1\} of citizen proposal/, '{0} from the dataset · {1} from citizen proposals'],
];

const out = { ...en, ...QUALITY, ...SENTENCES, ...ADD };
for (const [k, v] of Object.entries(out)) {
    out[k] = VALUE_FIXES.reduce((acc, [pattern, replacement]) => acc.replace(pattern, replacement), v);
}
const sorted = Object.fromEntries(Object.keys(out).sort((a, b) => a.localeCompare(b, 'id')).map((k) => [k, out[k]]));
writeFileSync(OUT, `${JSON.stringify(sorted, null, 4)}\n`);
console.log(`${Object.keys(en).length} -> ${Object.keys(sorted).length} kunci`);