/**
 * Label domain AksesKita. Semua status punya glyph + teks supaya informasi
 * tidak pernah bergantung pada warna saja.
 */

export const CATEGORIES = [
    { id: 'transportasi', label: 'Transportasi', glyph: 'TR' },
    { id: 'kesehatan', label: 'Kesehatan', glyph: 'KS' },
    { id: 'pendidikan', label: 'Pendidikan', glyph: 'PD' },
    { id: 'pemerintahan', label: 'Pemerintahan', glyph: 'PK' },
    { id: 'perbelanjaan', label: 'Perbelanjaan', glyph: 'PB' },
    { id: 'rekreasi', label: 'Rekreasi', glyph: 'RK' },
    { id: 'ibadah', label: 'Ibadah', glyph: 'IB' },
    { id: 'olahraga', label: 'Olahraga', glyph: 'OL' },
];

export const ATTRIBUTES = [
    { id: 'ramp', label: 'Ramp / jalur landai', hint: 'Jalur landai di pintu masuk, kemiringan ramah kursi roda.' },
    { id: 'toilet', label: 'Toilet aksesibel', hint: 'Toilet dengan ruang manuver, pegangan, dan pintu lebar.' },
    { id: 'lift', label: 'Lift', hint: 'Lift dengan tombol braille dan penanda suara.' },
    { id: 'path', label: 'Jalur kursi roda', hint: 'Jalur dalam area bebas tangga dan hambatan.' },
    { id: 'entrance', label: 'Akses masuk', hint: 'Pintu masuk bebas hambatan, lebar minimal 80 cm.' },
];

export const AVAILABILITY = {
    tersedia: { label: 'Tersedia', glyph: '✓', pill: 'pill--ok' },
    tidak: { label: 'Tidak tersedia', glyph: '✕', pill: 'pill--no' },
    belum: { label: 'Belum diketahui', glyph: '?', pill: 'pill--unknown' },
};

export const VERIFICATION = {
    terverifikasi: { label: 'Terverifikasi', glyph: '✓', pill: 'pill--accent' },
    menunggu: { label: 'Menunggu verifikasi', glyph: '◐', pill: 'pill--pending' },
    'perlu-perbaikan': { label: 'Perlu perbaikan data', glyph: '△', pill: 'pill--pending' },
    ditolak: { label: 'Ditolak', glyph: '✕', pill: 'pill--no' },
};

export const SUBMISSION_TYPES = {
    usulan: { label: 'Usulan fasilitas baru', glyph: '+' },
    koreksi: { label: 'Laporan koreksi data', glyph: '≠' },
};

export const SUBMISSION_STATUSES = {
    menunggu: { label: 'Menunggu', glyph: '◐', pill: 'pill--pending' },
    disetujui: { label: 'Disetujui', glyph: '✓', pill: 'pill--ok' },
    ditolak: { label: 'Ditolak', glyph: '✕', pill: 'pill--no' },
    perbaikan: { label: 'Minta perbaikan', glyph: '△', pill: 'pill--pending' },
};

export const ROLES = {
    admin: { label: 'Administrator', desc: 'Kelola fasilitas, kategori, dan pengguna.' },
    verifikator: { label: 'Verifikator', desc: 'Periksa pengajuan dan laporan koreksi.' },
    kontributor: { label: 'Kontributor', desc: 'Usulkan fasilitas dan laporkan koreksi.' },
};

export const categoryLabel = (id) => CATEGORIES.find((c) => c.id === id)?.label ?? id;

export const categoryGlyph = (id) => CATEGORIES.find((c) => c.id === id)?.glyph ?? '—';

export const attributeLabel = (id) => ATTRIBUTES.find((a) => a.id === id)?.label ?? id;

/** Fasilitas dianggap "perlu diperbarui" setelah 180 hari tanpa pemeriksaan. */
export const STALE_DAYS = 180;

export const daysSince = (iso, today = new Date('2026-10-21')) =>
    Math.round((today - new Date(iso)) / 86400000);

export const formatDate = (iso) =>
    new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });