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
    { id: 'penginapan', label: 'Penginapan', glyph: 'PN' },
    { id: 'rekreasi', label: 'Rekreasi', glyph: 'RK' },
    { id: 'ibadah', label: 'Ibadah', glyph: 'IB' },
    { id: 'olahraga', label: 'Olahraga', glyph: 'OL' },
    { id: 'perbankan', label: 'Perbankan & Keuangan', glyph: 'BK' },
    { id: 'pengiriman', label: 'Jasa Pengiriman', glyph: 'JP' },
];

/**
 * Kunci atribut netral bahasa dan sama persis dengan `Facility::ATTRIBUTE_COLUMNS`
 * di backend. Label Indonesia adalah kunci katalog terjemahan (`$t(label)`).
 */
export const ATTRIBUTES = [
    { id: 'accessible_ramp', label: 'Ramp / Jalur Landai', hint: 'Jalur landai di pintu masuk, kemiringan ramah kursi roda.' },
    { id: 'accessible_toilet', label: 'Toilet Aksesibel', hint: 'Toilet dengan ruang manuver, pegangan, dan pintu lebar.' },
    { id: 'accessible_elevator', label: 'Lift Aksesibel', hint: 'Lift dengan tombol braille dan penanda suara.' },
    { id: 'accessible_pedestrian_pathway', label: 'Jalur Pejalan Kaki Aksesibel', hint: 'Jalur dalam area bebas tangga dan hambatan.' },
    { id: 'accessible_entrance', label: 'Pintu Masuk Aksesibel', hint: 'Pintu masuk bebas hambatan, lebar minimal 80 cm.' },
    { id: 'tactile_guidance_path', label: 'Guiding Block / Jalur Pemandu Tunanetra', hint: 'Ubin pemandu taktil yang mengarahkan tunanetra di area publik.' },
    { id: 'accessible_information_signage', label: 'Informasi dan Petunjuk Aksesibel', hint: 'Papan informasi dengan huruf jelas, kontras, dan braille.' },
    { id: 'accessible_parking', label: 'Parkir Khusus Disabilitas', hint: 'Ruang parkir khusus dengan akses datar ke pintu masuk.' },
];

/** Status tiga nilai; label adalah kunci terjemahan, bukan nilai yang disimpan. */
export const AVAILABILITY = {
    yes: { label: 'Tersedia', glyph: '✓', pill: 'pill--ok' },
    no: { label: 'Tidak tersedia', glyph: '✕', pill: 'pill--no' },
    unknown: { label: 'Belum diketahui', glyph: '?', pill: 'pill--unknown' },
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
    queued: { label: 'Dalam antrean', glyph: '◷', pill: 'pill--pending' },
    menunggu: { label: 'Menunggu', glyph: '◐', pill: 'pill--pending' },
    disetujui: { label: 'Disetujui', glyph: '✓', pill: 'pill--ok' },
    ditolak: { label: 'Ditolak', glyph: '✕', pill: 'pill--no' },
    perbaikan: { label: 'Minta perbaikan', glyph: '△', pill: 'pill--pending' },
};

/** Jenis baris di antrean verifikasi (fasilitas basis data atau pengajuan warga). */
export const QUEUE_TYPES = {
    fasilitas: { label: 'Fasilitas', glyph: '▣' },
    usulan: SUBMISSION_TYPES.usulan,
    koreksi: SUBMISSION_TYPES.koreksi,
};

/** Penyaring antrean; kuncinya harus sama dengan `VerificationQueueController::BUCKETS`. */
export const QUEUE_BUCKETS = [
    { id: 'perlu', label: 'Semua yang membutuhkan verifikasi' },
    { id: 'dataset', label: 'Fasilitas dari dataset' },
    { id: 'usulan', label: 'Usulan fasilitas baru' },
    { id: 'koreksi', label: 'Laporan koreksi' },
    { id: 'menunggu', label: 'Menunggu verifikasi' },
    { id: 'perbaikan', label: 'Perlu perbaikan' },
    { id: 'selesai', label: 'Sudah diputuskan' },
];

/** Sama dengan `Facility::NEEDS_VERIFICATION`: masih menunggu atau perlu perbaikan. */
export const NEEDS_VERIFICATION = ['menunggu', 'perlu-perbaikan'];

export const needsVerification = (facility) => NEEDS_VERIFICATION.includes(facility.verification);

/** Label status baris antrean; status fasilitas dan status pengajuan kosakatanya beda. */
export const statusMeta = (kind, status) =>
    (kind === 'fasilitas' ? VERIFICATION : SUBMISSION_STATUSES)[status] ?? {
        label: status,
        glyph: '—',
        pill: 'pill--unknown',
    };

export const categoryLabel = (id) => CATEGORIES.find((c) => c.id === id)?.label ?? id;

export const categoryGlyph = (id) => CATEGORIES.find((c) => c.id === id)?.glyph ?? '—';

export const attributeLabel = (id) => ATTRIBUTES.find((a) => a.id === id)?.label ?? id;

/** Fasilitas dianggap "perlu diperbarui" setelah 180 hari tanpa pemeriksaan. */
export const STALE_DAYS = 180;

export const daysSince = (iso, today = new Date()) =>
    Math.round((today - new Date(iso)) / 86400000);

export const formatDate = (iso) =>
    new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });