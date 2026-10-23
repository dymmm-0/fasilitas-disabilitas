<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import ContributionForm from '../components/forms/ContributionForm.vue';
import CorrectionForm from '../components/forms/CorrectionForm.vue';
import StatusPill from '../components/common/StatusPill.vue';
import { useSubmissions } from '../composables/useSubmissions';
import { useAuth } from '../composables/useAuth';
import { SUBMISSION_TYPES } from '../data/meta';

const route = useRoute();
const { submit } = useSubmissions();
// Tautan ke antrean verifikasi hanya relevan (dan hanya tampil) untuk petugas.
const { user } = useAuth();

const tab = ref(route.query.mode === 'koreksi' ? 'koreksi' : 'usulan');
const result = ref(null);
const busy = ref(false);
const submitError = ref('');

/** Id pengajuan ditentukan server; klien hanya menampilkan hasil penyimpanan. */
const onSubmitted = async (payload) => {
    if (busy.value) return;

    busy.value = true;
    submitError.value = '';

    try {
        const submission = await submit(payload);

        result.value = {
            id: submission.id,
            label: SUBMISSION_TYPES[payload.type].label,
            name: submission.facilityName,
            submittedBy: submission.submittedBy,
        };
    } catch (error) {
        // Pesan validasi pertama dari server, misalnya berkas terlalu besar.
        submitError.value = Object.values(error.errors ?? {})[0]?.[0] ?? error.message;
    } finally {
        busy.value = false;
    }
};
</script>

<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Kontribusi masyarakat</p>
                <h1 class="display-lg">Tambahkan fasilitas, atau <em>perbaiki data yang keliru.</em></h1>
                <p class="body-copy">
                    Setiap pengajuan divalidasi dan disimpan server, lalu masuk antrean verifikasi yang dibaca
                    petugas dari basis data. Nomor pengajuan diberikan server setelah pengajuan tersimpan.
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
                    <ContributionForm
                        v-if="tab === 'usulan'"
                        :busy="busy"
                        :error="submitError"
                        @submit="onSubmitted"
                    />
                    <CorrectionForm
                        v-else
                        :initial-facility-id="String(route.query.facility ?? '')"
                        :busy="busy"
                        :error="submitError"
                        @submit="onSubmitted"
                    />
                </div>

                <div class="notice">
                    <b>Pengajuan tersimpan di basis data</b>
                    <span>
                        Server memeriksa isian sebelum menyimpan. Bukti foto (opsional) disimpan sebagai berkas
                        privat dan hanya dapat dibuka petugas verifikasi.
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
                    Pengajuan sudah tercatat dengan status menunggu. Verifikator akan meninjau bukti dan koordinat;
                    keputusan serta riwayatnya tersimpan di basis data.
                </p>
                <div class="inline-actions">
                    <RouterLink v-if="user" class="btn btn--primary" to="/verifikasi">Buka dashboard verifikasi</RouterLink>
                    <button type="button" class="btn" @click="result = null">Kirim pengajuan lain</button>
                </div>
            </div>
        </div>
    </section>
</template>