<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import ContributionForm from '../components/forms/ContributionForm.vue';
import CorrectionForm from '../components/forms/CorrectionForm.vue';
import StatusPill from '../components/common/StatusPill.vue';
import { useSubmissions } from '../composables/useSubmissions';
import { SUBMISSION_TYPES } from '../data/meta';

const route = useRoute();
const { submit } = useSubmissions();

const tab = ref(route.query.mode === 'koreksi' ? 'koreksi' : 'usulan');
const result = ref(null);

const onSubmitted = (payload) => {
    result.value = {
        id: submit(payload),
        label: SUBMISSION_TYPES[payload.type].label,
        name: payload.facilityName,
        submittedBy: payload.submittedBy,
    };
};
</script>

<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Kontribusi masyarakat</p>
                <h1 class="display-lg">Tambahkan fasilitas, atau <em>perbaiki data yang keliru.</em></h1>
                <p class="body-copy">
                    Setiap pengajuan masuk ke antrean verifikasi. Data dummy pada demonstrasi ini tidak dikirim ke
                    server dan tidak tersimpan permanen.
                </p>
            </div>

            <div v-if="!result" class="stack">
                <div class="cluster" role="group" aria-label="Pilih jenis pengajuan">
                    <button type="button" class="tab" :aria-pressed="tab === 'usulan'" @click="tab = 'usulan'">
                        + Usulan fasilitas baru
                    </button>
                    <button type="button" class="tab" :aria-pressed="tab === 'koreksi'" @click="tab = 'koreksi'">
                        ≠ Laporan koreksi data
                    </button>
                </div>

                <div class="panel">
                    <ContributionForm v-if="tab === 'usulan'" @submit="onSubmitted" />
                    <CorrectionForm
                        v-else
                        :initial-facility-id="String(route.query.facility ?? '')"
                        @submit="onSubmitted"
                    />
                </div>

                <div class="notice">
                    <b>Status pengajuan adalah simulasi</b>
                    <span>
                        Formulir ini berjalan sepenuhnya di sisi klien. Pada implementasi produksi, pengajuan akan
                        dikirim ke API Laravel dan disimpan di basis data.
                    </span>
                </div>
            </div>

            <div v-else class="panel stack" role="status" aria-live="polite">
                <p class="label">Pengajuan terkirim</p>
                <h2 class="display-md">{{ result.id }} masuk antrean verifikasi.</h2>
                <div class="inline-actions">
                    <StatusPill kind="submission" value="menunggu" />
                    <span class="meta">{{ result.label }} · {{ result.name }} · {{ result.submittedBy }}</span>
                </div>
                <p class="body-copy">
                    Verifikator akan meninjau bukti foto dan koordinat. Buka dashboard verifikasi untuk melihat antrean
                    dan mencoba aksi setujui, tolak, atau minta perbaikan.
                </p>
                <div class="inline-actions">
                    <RouterLink class="btn btn--primary" to="/verifikasi">Buka dashboard verifikasi</RouterLink>
                    <button type="button" class="btn" @click="result = null">Kirim pengajuan lain</button>
                </div>
            </div>
        </div>
    </section>
</template>