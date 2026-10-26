// Rapikan hasil MT: lindungi nama merek, samakan istilah domain, isi kunci yang gagal.
import { readFileSync, writeFileSync } from 'node:fs';

const OUT = 'resources/js/i18n/en.json';
const en = JSON.parse(readFileSync(OUT, 'utf8'));

// Nama merek tidak boleh diterjemahkan MT.
const BRAND = [
    [/Our ?Access\b/g, 'AksesKita'],
    [/Access ?We\b/g, 'AksesKita'],
    [/AccessKita\b/g, 'AksesKita'],
    [/OurAcess\b/g, 'AksesKita'],
];

// Istilah domain yang harus konsisten di seluruh UI.
const GLOSSARY = [
    [/\bamenities\b/g, 'facilities'],
    [/\bamenity\b/g, 'facility'],
    [/\bRecords\b/g, 'Notes'],
    [/\brecords\b/g, 'notes'],
    [/useIn the form/g, 'use the form'],
];

const MANUAL = {
    'Temukan fasilitas yang bisa {a}': 'Find facilities you can {a}',
    'Status verifikasi fasilitas tetap menunggu sampai diperiksa lapangan.': 'Facility verification status stays pending until checked in the field.',
    'Sudah diputuskan': 'Decided',
    'Sumber data': 'Data source',
    'Surel': 'Email',
    'Surel pelapor': 'Reporter email',
    'Surel pengaju': 'Submitter email',
    'Surel pengusul': 'Proposer email',
    'Tabel akun admin': 'Admin accounts table',
    'Tabel fasilitas': 'Facilities table',
    'Tabel kategori fasilitas': 'Facility categories table',
    'Tahap 1': 'Step 1',
    'Tahap 2': 'Step 2',
    'Tahap 3': 'Step 3',
    'Tambah': 'Add',
    'Tambah kategori': 'Add category',
    'Tambahkan fasilitas, atau {a}': 'Add a facility, or {a}',
    'Tampil di filter': 'Shown in filters',
    'Tampilkan': 'Show',
    'Tanpa catatan.': 'No notes.',
    'Tanpa ID sumber': 'No source ID',
    'Tanpa pengaju': 'No submitter',
    'tanpa perlu akun.': 'without an account.',
    'Tentang AksesKita': 'About AksesKita',
    'Terapkan': 'Apply',
    'Tersedia': 'Available',
    'Terverifikasi': 'Verified',
    'Tidak ada baris': 'No rows',
    'Tidak ada baris yang cocok dengan penyaring saat ini.': 'No rows match the current filters.',
    'Tidak ada fasilitas pada halaman ini.': 'No facilities on this page.',
    'Tidak ada pengajuan': 'No submissions',
    'Tidak diketahui': 'Unknown',
    'Tidak ditemukan': 'Not found',
    'Tidak tersedia': 'Not available',
    'Tinjau di dashboard verifikasi.': 'Review in the verification dashboard.',
    'Toilet aksesibel': 'Accessible toilet',
    'Toilet dengan ruang manuver, pegangan, dan pintu lebar.': 'Toilet with turning space, grab bars, and a wide door.',
    'Total antrean: {a} baris = {b} fasilitas belum diperiksa + {c} perlu perbaikan + {d} pengajuan belum diputuskan. {e} fasilitas sudah ditolak dan tetap tersimpan.': 'Queue total: {a} rows = {b} facilities not yet checked + {c} needing repairs + {d} submissions not yet decided. {e} facilities were rejected and are still stored.',
    'Transportasi': 'Transport',
    'Tujuan platform': 'Platform goals',
    'Tutup menu': 'Close menu',
    'Tutup pemberitahuan': 'Dismiss notification',
    'Ubah penyaring untuk menampilkan baris antrean lain.': 'Change the filters to show other queue rows.',
    'Untuk admin': 'For admins',
    'Untuk pengguna': 'For users',
    'Untuk pengunjung': 'For visitors',
    'Usulan fasilitas baru': 'New facility proposal',
    'Usulkan fasilitas': 'Propose a facility',
    'Usulkan fasilitas atau laporkan koreksi.': 'Propose a facility or report a correction.',
    'Usulkan fasilitas baru': 'Propose a new facility',
    'Verifikasi': 'Verification',
    'Verifikator memeriksa.': 'A verifier checks.',
    'Warga mengusulkan.': 'Residents propose.',
    'Daftar fasilitas berasal dari impor dataset OpenStreetMap Kota Medan ke basis data AksesKita — 1.117 fasilitas, semuanya masih berstatus menunggu verifikasi. Karena itu status aksesibilitasnya belum diperiksa lapangan: sebagian besar masih "belum diketahui", hanya sedikit yang punya catatan pendukung. Akun admin sudah asli dan dilindungi login; antrean pengajuan, keputusan verifikasi, riwayat, dan bukti pendukung tersimpan di basis data AksesKita. Pengelolaan kategori masih berupa data di sisi klien karena kategori mengikuti kolom fasilitas hasil impor.': 'The facility list comes from importing the Medan City OpenStreetMap dataset into the AksesKita database — 1,117 facilities, all still pending verification. Their accessibility status has therefore not been checked in the field: most are still "unknown", and only a few have supporting notes. Admin accounts are real and protected by login; the submission queue, verification decisions, history, and supporting evidence are stored in the AksesKita database. Category management is still client-side data because categories follow the columns of the imported facilities.',
};

const fixed = {};
for (const [key, value] of Object.entries(en)) {
    if (/QUERY LENGTH LIMIT|MYMEMORY WARNING/i.test(value)) continue;
    let out = MANUAL[key] ?? value;
    for (const [pattern, replacement] of [...BRAND, ...GLOSSARY]) out = out.replace(pattern, replacement);
    fixed[key] = out;
}
Object.assign(fixed, MANUAL);

const sorted = Object.fromEntries(Object.keys(fixed).sort((a, b) => a.localeCompare(b, 'id')).map((k) => [k, fixed[k]]));
writeFileSync(OUT, `${JSON.stringify(sorted, null, 4)}\n`);
console.log(`${Object.keys(sorted).length} kunci`);