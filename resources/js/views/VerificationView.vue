<script setup>
import { computed, onMounted, ref } from 'vue';
import { t } from '../i18n';
import QueueList from '../components/verification/QueueList.vue';
import SubmissionDetail from '../components/verification/SubmissionDetail.vue';
import FacilityReview from '../components/verification/FacilityReview.vue';
import StatCard from '../components/dashboard/StatCard.vue';
import StatusPill from '../components/common/StatusPill.vue';
import { useVerification } from '../composables/useVerification';
import { useToast } from '../composables/useToast';
import { QUEUE_BUCKETS, VERIFICATION, formatDate, statusMeta } from '../data/meta';

const { items, stats, history, filters, meta, status, loadError, selected, load, decide, verify, select } = useVerification();
const toast = useToast();

const busy = ref(false);
const decideError = ref('');

/** Hasil pemeriksaan fasilitas (kolom database) → label status di antarmuka. */
const FACILITY_ACTIONS = { verified: 'terverifikasi', needs_repair: 'perlu-perbaikan', rejected: 'ditolak' };

const currentPage = computed(() => meta.currentPage ?? 1);
const lastPage = computed(() => meta.lastPage ?? 1);

const rangeLabel = computed(() =>
    meta.from
        ? t('Menampilkan {0}–{1} dari {2} baris antrean', [meta.from, meta.to, meta.total])
        : t('Tidak ada baris pada halaman ini'),
);

/** Jendela 5 nomor di sekitar halaman aktif; sisanya lewat tombol sebelumnya/berikutnya. */
const pageNumbers = computed(() => {
    const start = Math.max(1, Math.min(currentPage.value - 2, lastPage.value - 4));

    return Array.from({ length: Math.min(5, lastPage.value) }, (_, index) => start + index);
});

// Antrean dibaca dari basis data; hanya halaman ini yang butuh sesi petugas.
onMounted(() => load(1));

const applyFilters = () => load(1);

const onDecide = async ({ action, note }) => {
    if (busy.value || !selected.value) return;

    const item = selected.value;

    busy.value = true;
    decideError.value = '';

    try {
        if (item.kind === 'fasilitas') {
            const facility = await verify(item.id, action, note);
            const label = VERIFICATION[FACILITY_ACTIONS[action]].label;

            toast.push(
                `${facility.id} · ${t(label)}`,
                facility.verifiedBy ? t('Diperiksa oleh {0}.', [facility.verifiedBy]) : t('Keputusan tersimpan.'),
            );
        } else {
            const submission = await decide(item.id, action, note);

            toast.push(
                `${submission.id} · ${t(statusMeta(item.kind, submission.status).label)}`,
                submission.decidedBy ? t('Diputuskan oleh {0}.', [submission.decidedBy]) : t('Keputusan tersimpan.'),
            );
        }
    } catch (error) {
        decideError.value = Object.values(error.errors ?? {})[0]?.[0] ?? error.message;
    } finally {
        busy.value = false;
    }
};
</script>

<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">{{ $t('Dashboard verifikasi') }}</p>
                <h1 class="display-lg"><i18n-t keypath="Periksa fasilitas dan pengajuan warga, {a}" tag="span" scope="global"><template #a><em>{{ $t('catat keputusannya.') }}</em></template></i18n-t></h1>
                <p class="body-copy">{{ $t('Antrean berisi fasilitas yang belum diperiksa — termasuk data hasil impor dataset — dan pengajuan masyarakat. Usulan yang disetujui membuat fasilitas baru; laporan koreksi yang disetujui memperbarui data fasilitas yang dilaporkan. Keputusan verifikasi fasilitas tidak mengubah indikator aksesibilitas tanpa bukti.') }}</p>
            </div>

            <div class="grid grid--4">
                <StatCard
                    :value="stats.facilitiesPending ?? 0"
                    label="Fasilitas belum diperiksa"
                    :hint="$t('{0} dari dataset · {1} dari usulan warga', [stats.datasetPending ?? 0, stats.communityPending ?? 0])"
                />
                <StatCard :value="stats.facilitiesNeedsRepair ?? 0" label="Perlu perbaikan data" hint="Sudah diperiksa, menunggu pembaruan" />
                <StatCard :value="stats.submissionsOpen ?? 0" label="Pengajuan menunggu" hint="Usulan atau koreksi belum diputuskan" />
                <StatCard :value="stats.facilitiesVerified ?? 0" label="Terverifikasi" hint="Sudah diperiksa petugas" />
            </div>

            <p class="help" role="status" aria-live="polite"><i18n-t keypath="Total antrean: {a} baris = {b} fasilitas belum diperiksa + {c} perlu perbaikan + {d} pengajuan belum diputuskan. {e} fasilitas sudah ditolak dan tetap tersimpan." tag="span" scope="global"><template #a>{{ stats.queueTotal ?? 0 }}</template><template #b>{{ stats.facilitiesPending ?? 0 }}</template><template #c>{{ stats.facilitiesNeedsRepair ?? 0 }}</template><template #d>{{ stats.submissionsOpen ?? 0 }}</template><template #e>{{ stats.facilitiesRejected ?? 0 }}</template></i18n-t></p>

            <form class="filter-bar" role="search" @submit.prevent="applyFilters">
                <div class="grid grid--form">
                    <div class="field">
                        <label for="antrean-cari">{{ $t('Cari di antrean') }}</label>
                        <input
                            id="antrean-cari"
                            v-model="filters.query"
                            class="input"
                            type="search"
                            :placeholder="$t('ID, nama fasilitas, pengaju, atau kota')"
                            autocomplete="off"
                        >
                    </div>

                    <div class="field">
                        <label for="antrean-baris">{{ $t('Tampilkan') }}</label>
                        <select id="antrean-baris" v-model="filters.bucket" class="select" @change="applyFilters">
                            <option v-for="bucket in QUEUE_BUCKETS" :key="bucket.id" :value="bucket.id">{{ $t(bucket.label) }}</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="antrean-terapkan">&nbsp;</label>
                        <button id="antrean-terapkan" type="submit" class="btn">{{ $t('Terapkan') }}</button>
                    </div>
                </div>
                <p class="meta">{{ rangeLabel }}</p>
            </form>

            <div v-if="status === 'memuat'" class="empty-state" role="status">
                <span class="label">{{ $t('Memuat antrean...') }}</span>
                <p class="card__text">{{ $t('Mengambil fasilitas, pengajuan, dan riwayat dari server.') }}</p>
            </div>

            <div v-else-if="status === 'gagal'" class="empty-state" role="alert">
                <span class="label">{{ $t('Antrean gagal dimuat') }}</span>
                <p class="card__text">{{ loadError }}</p>
                <button type="button" class="btn btn--sm" @click="load(currentPage)">{{ $t('Coba lagi') }}</button>
            </div>

            <div v-else class="grid grid--sidebar">
                <div class="stack--sm">
                    <h2 class="display-sm">{{ $t('Antrean verifikasi') }}</h2>
                    <QueueList :items="items" :selected-id="selected?.id ?? null" @select="select" />

                    <nav v-if="lastPage > 1" class="cluster" :aria-label="$t('Navigasi halaman antrean')">
                        <button type="button" class="btn btn--sm" :disabled="currentPage === 1" @click="load(currentPage - 1)">{{ $t('Sebelumnya') }}</button>
                        <button
                            v-for="number in pageNumbers"
                            :key="number"
                            type="button"
                            class="tab"
                            :aria-pressed="number === currentPage"
                            @click="load(number)"
                        >
                            {{ number }}
                        </button>
                        <button type="button" class="btn btn--sm" :disabled="currentPage === lastPage" @click="load(currentPage + 1)">{{ $t('Berikutnya') }}</button>
                    </nav>
                </div>

                <div class="stack--sm">
                    <h2 class="display-sm">{{ $t('Detail dan keputusan') }}</h2>
                    <template v-if="selected">
                        <FacilityReview
                            v-if="selected.kind === 'fasilitas'"
                            :item="selected"
                            :busy="busy"
                            :error="decideError"
                            @decide="onDecide"
                        />
                        <SubmissionDetail
                            v-else
                            :submission="selected.submission"
                            :busy="busy"
                            :error="decideError"
                            @decide="onDecide"
                        />
                    </template>
                    <div v-else class="empty-state">
                        <span class="label">{{ $t('Tidak ada baris') }}</span>
                        <p class="card__text">{{ $t('Ubah penyaring untuk menampilkan baris antrean lain.') }}</p>
                    </div>
                </div>
            </div>

            <div class="stack--sm">
                <h2 class="display-sm">{{ $t('Riwayat verifikasi') }}</h2>
                <ol v-if="history.length" class="steps">
                    <li v-for="entry in history" :key="`${entry.kind}-${entry.id}-${entry.at}-${entry.action}`" class="step">
                        <span class="step__num" aria-hidden="true">{{ statusMeta(entry.kind, entry.action).glyph }}</span>
                        <span>
                            <b>{{ entry.id }} — {{ $t(statusMeta(entry.kind, entry.action).label) }}</b>
                            <span style="display: block; color: var(--body)">{{ entry.note }}</span>
                        </span>
                        <span class="step__time">{{ entry.by }} · {{ formatDate(entry.at) }}</span>
                    </li>
                </ol>
                <div v-else class="empty-state" role="status">
                    <span class="label">{{ $t('Belum ada keputusan') }}</span>
                    <p class="card__text">{{ $t('Riwayat akan terisi setelah petugas memutuskan pengajuan atau fasilitas.') }}</p>
                </div>
            </div>

            <div class="cluster">
                <StatusPill kind="verification" value="menunggu" />
                <StatusPill kind="verification" value="perlu-perbaikan" />
                <StatusPill kind="verification" value="terverifikasi" />
                <StatusPill kind="submission" value="perbaikan" />
                <StatusPill kind="submission" value="ditolak" />
                <span class="help">{{ $t('Setiap keputusan tersimpan di basis data bersama identitas petugas dan waktunya.') }}</span>
            </div>
        </div>
    </section>
</template>
