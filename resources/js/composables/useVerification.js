import { computed, reactive, ref } from 'vue';
import { request } from './useAuth';
import { useFacilities } from './useFacilities';

/**
 * Antrean verifikasi gabungan dari `GET /api/verification-queue`: fasilitas dari
 * basis data (termasuk 1.117 baris dataset) dan pengajuan masyarakat. Penyaringan
 * dan pagination dikerjakan server supaya seluruh dataset tidak pernah dikirim
 * sekaligus hanya untuk menampilkan satu halaman.
 *
 * Statistik (`stats`) juga datang dari server dan dihitung dari basis data, bukan
 * dari baris yang sedang tampil.
 */
const items = ref([]);
const stats = ref({});
const history = ref([]);
const selectedId = ref(null);
const status = ref('memuat');
const loadError = ref(null);

const filters = reactive({ bucket: 'perlu', query: '' });

const meta = reactive({ currentPage: 1, lastPage: 1, perPage: 10, total: 0, from: null, to: null });

/** Halaman verifikasi: satu permintaan untuk antrean, statistik, dan riwayat. */
const load = async (number = 1) => {
    status.value = 'memuat';
    loadError.value = null;

    const params = new URLSearchParams({ bucket: filters.bucket, page: String(number) });
    const query = filters.query.trim();

    if (query) params.set('q', query);

    try {
        const data = await request(`/api/verification-queue?${params}`);

        items.value = data.items ?? [];
        stats.value = data.stats ?? {};
        history.value = data.history ?? [];

        Object.assign(meta, data.meta ?? {}, { total: data.total ?? 0 });

        // Baris yang tadi dibuka bisa saja pindah halaman atau keluar dari penyaring.
        if (!items.value.some((item) => item.id === selectedId.value)) {
            selectedId.value = items.value[0]?.id ?? null;
        }

        status.value = 'siap';
    } catch (error) {
        loadError.value = error.status === 401
            ? 'Sesi petugas sudah berakhir. Masuk kembali untuk membuka antrean.'
            : error.message;
        status.value = 'gagal';
    }
};

const selected = computed(() => items.value.find((item) => item.id === selectedId.value) ?? null);

/** Keputusan atas pengajuan. Server yang menyimpan keputusan, riwayat, dan fasilitasnya. */
const decide = async (id, action, note = '') => {
    const data = await request(`/api/submissions/${encodeURIComponent(id)}/decision`, {
        method: 'POST',
        body: JSON.stringify({ action, note }),
    });

    selectedId.value = id;
    useFacilities().reload();
    await load(meta.currentPage);

    return data.submission;
};

/** Keputusan atas fasilitas: identitas petugas dan waktunya dicatat server. */
const verify = async (id, action, note = '') => {
    const data = await request(`/api/facilities/${encodeURIComponent(id)}/verification`, {
        method: 'POST',
        body: JSON.stringify({ action, note }),
    });

    selectedId.value = id;
    useFacilities().reload();
    await load(meta.currentPage);

    return data.facility;
};

export function useVerification() {
    return {
        items,
        stats,
        history,
        filters,
        meta,
        status,
        loadError,
        selectedId,
        selected,
        load,
        decide,
        verify,
        select: (id) => (selectedId.value = id),
    };
}
