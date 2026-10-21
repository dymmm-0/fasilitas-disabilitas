<script setup>
import { computed, ref } from 'vue';
import SubmissionQueue from '../components/verification/SubmissionQueue.vue';
import SubmissionDetail from '../components/verification/SubmissionDetail.vue';
import StatCard from '../components/dashboard/StatCard.vue';
import StatusPill from '../components/common/StatusPill.vue';
import { useSubmissions } from '../composables/useSubmissions';
import { SUBMISSION_STATUSES, SUBMISSION_TYPES } from '../data/meta';

const { queue, counts, queueFilters, history, decide } = useSubmissions();

const selectedId = ref(null);
const selected = computed(() => queue.value.find((s) => s.id === selectedId.value) ?? queue.value[0] ?? null);

const onDecide = ({ id, action, note }) => {
    decide(id, action, note);
    selectedId.value = id;
};
</script>

<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Dashboard verifikasi</p>
                <h1 class="display-lg">Periksa pengajuan warga, <em>catat keputusannya.</em></h1>
                <p class="body-copy">
                    Antrean berisi usulan fasilitas baru dan laporan koreksi. Keputusan di sini mengubah state
                    demonstrasi: usulan yang disetujui langsung muncul di peta.
                </p>
            </div>

            <div class="grid grid--4">
                <StatCard :value="counts.usulan" label="Usulan fasilitas" hint="Antrean masuk" />
                <StatCard :value="counts.koreksi" label="Laporan koreksi" hint="Perbaikan data" />
                <StatCard :value="counts.menunggu" label="Menunggu tinjauan" hint="Belum diputuskan" />
                <StatCard :value="counts.selesai" label="Sudah diputuskan" hint="Disetujui atau ditolak" />
            </div>

            <form class="filter-bar" role="search" @submit.prevent>
                <div class="grid grid--form">
                    <div class="field">
                        <label for="antrean-cari">Cari pengajuan</label>
                        <input
                            id="antrean-cari"
                            v-model="queueFilters.query"
                            class="input"
                            type="search"
                            placeholder="ID, nama fasilitas, pengaju, atau kota"
                            autocomplete="off"
                        >
                    </div>

                    <div class="field">
                        <label for="antrean-jenis">Jenis pengajuan</label>
                        <select id="antrean-jenis" v-model="queueFilters.type" class="select">
                            <option value="all">Semua jenis</option>
                            <option v-for="(meta, key) in SUBMISSION_TYPES" :key="key" :value="key">{{ meta.label }}</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="antrean-status">Status</label>
                        <select id="antrean-status" v-model="queueFilters.status" class="select">
                            <option value="all">Semua status</option>
                            <option v-for="(meta, key) in SUBMISSION_STATUSES" :key="key" :value="key">{{ meta.label }}</option>
                        </select>
                    </div>
                </div>
                <p class="meta" role="status" aria-live="polite">{{ queue.length }} pengajuan ditampilkan</p>
            </form>

            <div class="grid grid--sidebar">
                <div class="stack--sm">
                    <h2 class="display-sm">Antrean pengajuan</h2>
                    <SubmissionQueue
                        :submissions="queue"
                        :selected-id="selected?.id ?? null"
                        @select="(id) => (selectedId = id)"
                    />
                </div>

                <div class="stack--sm">
                    <h2 class="display-sm">Detail dan keputusan</h2>
                    <SubmissionDetail v-if="selected" :submission="selected" @decide="onDecide" />
                    <div v-else class="empty-state">
                        <span class="label">Tidak ada pengajuan</span>
                        <p class="card__text">Ubah filter untuk menampilkan pengajuan lain.</p>
                    </div>
                </div>
            </div>

            <div class="stack--sm">
                <h2 class="display-sm">Riwayat verifikasi</h2>
                <ol class="steps">
                    <li v-for="entry in history" :key="`${entry.id}-${entry.at}`" class="step">
                        <span class="step__num" aria-hidden="true">{{ SUBMISSION_STATUSES[entry.action].glyph }}</span>
                        <span>
                            <b>{{ entry.id }} — {{ SUBMISSION_STATUSES[entry.action].label }}</b>
                            <span style="display: block; color: var(--body)">{{ entry.note }}</span>
                        </span>
                        <span class="step__time">{{ entry.by }} · {{ entry.at }}</span>
                    </li>
                </ol>
            </div>

            <div class="cluster">
                <StatusPill kind="submission" value="disetujui" />
                <StatusPill kind="submission" value="perbaikan" />
                <StatusPill kind="submission" value="ditolak" />
                <span class="help">Riwayat bersifat simulasi dan tidak tersimpan ke basis data.</span>
            </div>
        </div>
    </section>
</template>