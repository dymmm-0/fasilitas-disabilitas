import { computed, reactive, ref } from 'vue';
import { FACILITIES, completeness } from '../data/facilities';
import { ATTRIBUTES, categoryLabel } from '../data/meta';

/**
 * State fasilitas bersama untuk peta, daftar kartu, dan dashboard.
 * Singleton modul: semua komponen membaca sumber yang sama.
 */
const facilities = ref([...FACILITIES]);

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

/** Dipakai saat pengajuan usulan disetujui pada dashboard verifikasi. */
const addFacility = (payload) => {
    const highest = facilities.value.reduce(
        (max, f) => Math.max(max, Number(String(f.id).replace(/\D/g, '')) || 0),
        0,
    );
    const id = `fas-${String(highest + 1).padStart(3, '0')}`;

    facilities.value = [
        {
            id,
            name: payload.name,
            category: payload.category,
            address: payload.address,
            city: payload.city,
            lat: payload.lat,
            lng: payload.lng,
            accessibility: { ...payload.accessibility },
            notes: payload.notes ?? '',
            proofs: payload.proofs ?? [],
            verification: 'terverifikasi',
            lastChecked: new Date().toISOString().slice(0, 10),
            updatedAt: new Date().toISOString().slice(0, 10),
            contributor: payload.submittedBy ?? 'Kontributor masyarakat',
        },
        ...facilities.value,
    ];

    return id;
};

/** Dipakai saat laporan koreksi disetujui. */
const applyCorrection = (facilityId, field, value) => {
    const facility = facilities.value.find((f) => f.id === facilityId);
    if (!facility) return false;

    facility.accessibility[field] = value;
    facility.updatedAt = new Date().toISOString().slice(0, 10);

    return true;
};

export function useFacilities() {
    return {
        facilities,
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
        addFacility,
        applyCorrection,
        byId: (id) => facilities.value.find((f) => f.id === id) ?? null,
    };
}