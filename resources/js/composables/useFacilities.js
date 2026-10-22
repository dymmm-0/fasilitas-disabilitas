import { computed, reactive, ref } from 'vue';
import { ATTRIBUTES, categoryLabel } from '../data/meta';

/**
 * State fasilitas bersama untuk peta, daftar kartu, dan dashboard.
 * Singleton modul: semua komponen membaca sumber yang sama, diisi dari `/api/facilities`.
 */
const facilities = ref([]);
const status = ref('memuat');
const loadError = ref(null);

const load = async () => {
    status.value = 'memuat';
    loadError.value = null;

    try {
        const response = await fetch('/api/facilities', { headers: { Accept: 'application/json' } });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        facilities.value = (await response.json()).facilities ?? [];
        status.value = 'siap';
    } catch (error) {
        loadError.value = error.message;
        status.value = 'gagal';
    }
};

load();

/**
 * Satu halaman tabel "Pengelolaan fasilitas" (dashboard admin). Diambil per halaman
 * lewat `?page=&per_page=` supaya tidak ada ribuan baris yang dipotong di klien;
 * `facilities` di atas tetap dipakai peta dan agregat dashboard.
 */
const tablePage = reactive({ rows: [], total: 0, meta: null, status: 'memuat', error: null });

const loadTablePage = async (number = 1, perPage = 10) => {
    tablePage.status = 'memuat';
    tablePage.error = null;

    try {
        const response = await fetch(`/api/facilities?page=${number}&per_page=${perPage}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json();
        const meta = data.meta ?? null;

        tablePage.rows = data.facilities ?? [];
        tablePage.total = data.total ?? 0;
        tablePage.meta = meta;
        tablePage.status = 'siap';

        // Data bisa menyusut (mis. baris terhapus); mundur supaya halaman tidak kosong.
        if (!tablePage.rows.length && meta && meta.lastPage < meta.currentPage) {
            await loadTablePage(meta.lastPage, meta.perPage);
        }
    } catch (error) {
        tablePage.error = error.message;
        tablePage.status = 'gagal';
    }
};

const filters = reactive({
    query: '',
    category: 'all',
    verification: 'all',
    attributes: [],
});

/** Fasilitas yang sedang disorot (klik kartu atau penanda peta). */
const selectedId = ref(null);

const results = computed(() => {
    const q = filters.query.trim().toLowerCase();

    return facilities.value.filter((f) => {
        if (filters.category !== 'all' && f.category !== filters.category) return false;
        if (filters.verification !== 'all' && f.verification !== filters.verification) return false;

        if (filters.attributes.length && !filters.attributes.every((id) => f.accessibility[id] === 'tersedia')) {
            return false;
        }

        if (!q) return true;

        return [f.name, f.address, f.city, categoryLabel(f.category)]
            .join(' ')
            .toLowerCase()
            .includes(q);
    });
});

const hasFilters = computed(
    () => filters.query.trim() !== '' || filters.category !== 'all' || filters.verification !== 'all' || filters.attributes.length > 0,
);

const toggleAttribute = (id) => {
    const at = filters.attributes.indexOf(id);
    if (at === -1) filters.attributes.push(id);
    else filters.attributes.splice(at, 1);
};

const resetFilters = () => {
    filters.query = '';
    filters.category = 'all';
    filters.verification = 'all';
    filters.attributes = [];
};

export function useFacilities() {
    return {
        facilities,
        status,
        loadError,
        reload: load,
        tablePage,
        loadTablePage,
        filters,
        results,
        hasFilters,
        attributes: ATTRIBUTES,
        selectedId,
        select: (id) => {
            selectedId.value = id;
        },
        toggleAttribute,
        resetFilters,
        byId: (id) => facilities.value.find((f) => f.id === id) ?? null,
    };
}