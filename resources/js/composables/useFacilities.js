import { computed, reactive, ref, watch } from 'vue';
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
 * Filter fasilitas dipakai bersama beranda dan tabel Pengelolaan fasilitas di
 * dashboard admin: pencarian nama/alamat/kota plus kategori, status verifikasi,
 * dan atribut aksesibilitas. Penyaringan di klien dari `facilities` yang sudah dimuat.
 */
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

        if (filters.attributes.length && !filters.attributes.every((id) => f.accessibility[id] === 'yes')) {
            return false;
        }

        if (!q) return true;

        return [f.name, f.address, f.city, categoryLabel(f.category)]
            .join(' ')
            .toLowerCase()
            .includes(q);
    });
});

watch(results, (nextResults) => {
    if (selectedId.value && !nextResults.some((facility) => facility.id === selectedId.value)) {
        selectedId.value = null;
    }
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