<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { useRoute } from 'vue-router';
import StatusPill from '../components/common/StatusPill.vue';
import { request } from '../composables/useAuth';
import { SUBMISSION_TYPES, formatDate } from '../data/meta';

// Guest tanpa akun: pengajuan dicari lewat surel yang diisi di formulir kontribusi.
// Nama boleh sama antara dua orang, jadi nama hanya penyaring tambahan.
const route = useRoute();

const email = ref(String(route.query.email ?? ''));
const name = ref('');
const state = ref('idle');
const error = ref('');
const submissions = ref([]);
const searched = ref(false);

let timer = null;

const queued = computed(() => submissions.value.some((item) => item.status === 'queued'));

const time = (iso) => (iso ? new Date(iso).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '');

const lookup = async () => {
    if (state.value === 'memuat' || !email.value.trim()) return;

    clearTimeout(timer);
    state.value = 'memuat';
    error.value = '';

    const params = new URLSearchParams({ email: email.value.trim() });
    if (name.value.trim()) params.set('name', name.value.trim());

    try {
        const data = await request(`/api/submissions/status?${params}`);

        submissions.value = data.submissions ?? [];
        searched.value = true;
        state.value = 'siap';

        // Selama masih ada pengajuan dalam jeda antrean, hasilnya diperiksa ulang.
        if (queued.value) timer = setTimeout(lookup, 10000);
    } catch (requestError) {
        error.value = Object.values(requestError.errors ?? {})[0]?.[0] ?? requestError.message;
        state.value = 'gagal';
        searched.value = true;
    }
};

onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Status pengajuan</p>
                <h1 class="display-lg">Periksa pengajuan <em>tanpa perlu akun.</em></h1>
                <p class="body-copy">
                    Masukkan surel yang dipakai saat mengirim usulan atau laporan koreksi. Nama boleh sama antara
                    dua orang, karena itu surel yang menjadi penanda pengajuan Anda.
                </p>
            </div>

            <form class="panel stack" @submit.prevent="lookup">
                <div class="grid grid--form">
                    <div class="field">
                        <label for="status-email">Surel pengaju</label>
                        <input
                            id="status-email"
                            v-model="email"
                            class="input"
                            type="email"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="field">
                        <label for="status-nama">Nama pengaju (opsional)</label>
                        <input id="status-nama" v-model="name" class="input" autocomplete="name">
                    </div>
                </div>

                <div class="inline-actions">
                    <button type="submit" class="btn btn--primary" :disabled="state === 'memuat'">
                        {{ state === 'memuat' ? 'Memeriksa...' : 'Cek status' }}
                    </button>
                    <span class="meta">Cek ulang otomatis selama pengajuan masih dalam antrean.</span>
                </div>

                <p v-if="error" class="error-text" role="alert">{{ error }}</p>
            </form>

            <div v-if="searched" class="stack" role="status" aria-live="polite">
                <p v-if="!submissions.length" class="notice">
                    <b>Tidak ada pengajuan</b>
                    <span>Periksa kembali penulisan surel, atau kirim pengajuan baru dari halaman kontribusi.</span>
                </p>

                <div v-else>
                    <p class="label">{{ submissions.length }} pengajuan ditemukan</p>
                    <div class="table-wrap" tabindex="0" role="region" aria-label="Daftar pengajuan">
                        <table class="table">
                            <caption class="help" style="text-align: left; padding-bottom: 12px">
                                Status pengajuan atas surel Anda. Bukti foto, surel, dan catatan internal petugas tidak
                                ditampilkan di halaman publik ini.
                            </caption>
                            <thead>
                                <tr>
                                    <th scope="col">Nomor</th>
                                    <th scope="col">Jenis</th>
                                    <th scope="col">Fasilitas</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Dikirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in submissions" :key="item.id">
                                    <td class="mono">{{ item.id }}</td>
                                    <td>{{ SUBMISSION_TYPES[item.type]?.label ?? item.type }}</td>
                                    <td>
                                        <RouterLink v-if="item.facilityId" :to="`/fasilitas/${item.facilityId}`">
                                            {{ item.facilityName }}
                                        </RouterLink>
                                        <RouterLink v-else-if="item.createdFacilityId" :to="`/fasilitas/${item.createdFacilityId}`">
                                            {{ item.facilityName }}
                                        </RouterLink>
                                        <template v-else>{{ item.facilityName }}</template>
                                    </td>
                                    <td>
                                        <StatusPill kind="submission" :value="item.status" />
                                        <span v-if="item.status === 'queued'" class="help">
                                            diproses otomatis sekitar {{ time(item.readyAt) }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(item.submittedAt) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
