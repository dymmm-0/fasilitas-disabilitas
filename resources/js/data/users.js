/** Pengguna dan peran (data dummy, tanpa autentikasi sungguhan). */
export const USERS = [
    { id: 'usr-01', name: 'Hendra Saputra', email: 'hendra@akeskita.example', role: 'admin', city: 'Jakarta', submissions: 12, status: 'aktif' },
    { id: 'usr-02', name: 'Maya Lestari', email: 'maya@akeskita.example', role: 'verifikator', city: 'Bandung', submissions: 48, status: 'aktif' },
    { id: 'usr-03', name: 'Rani Prameswari', email: 'rani@akeskita.example', role: 'kontributor', city: 'Jakarta', submissions: 26, status: 'aktif' },
    { id: 'usr-04', name: 'Bayu Nugroho', email: 'bayu@akeskita.example', role: 'kontributor', city: 'Yogyakarta', submissions: 17, status: 'aktif' },
    { id: 'usr-05', name: 'Komang Ariani', email: 'komang@akeskita.example', role: 'kontributor', city: 'Denpasar', submissions: 9, status: 'nonaktif' },
];

/** Kategori dapat dinonaktifkan tanpa menghapus fasilitasnya. */
export const CATEGORY_SETTINGS = [
    { id: 'transportasi', facilities: 4, active: true },
    { id: 'kesehatan', facilities: 2, active: true },
    { id: 'pendidikan', facilities: 2, active: true },
    { id: 'pemerintahan', facilities: 1, active: true },
    { id: 'perbelanjaan', facilities: 2, active: true },
    { id: 'rekreasi', facilities: 1, active: true },
    { id: 'ibadah', facilities: 1, active: true },
    { id: 'olahraga', facilities: 1, active: true },
];