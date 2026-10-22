<script setup>
import { computed, onMounted } from 'vue';
import { completeness } from '../data/facilities';
import { daysSince, STALE_DAYS, VERIFICATION } from '../data/meta';
import { useFacilities } from '../composables/useFacilities';
import StatCard from '../components/dashboard/StatCard.vue';
import CompletenessChart from '../components/dashboard/CompletenessChart.vue';
import FacilityTable from '../components/dashboard/FacilityTable.vue';
import CategoryManager from '../components/dashboard/CategoryManager.vue';
import UserRolesPanel from '../components/dashboard/UserRolesPanel.vue';

const { facilities, tablePage, loadTablePage } = useFacilities();

const verified = computed(() => facilities.value.filter((f) => f.verification === 'terverifikasi').length);
const pending = computed(() => facilities.value.filter((f) => f.verification !== 'terverifikasi').length);
const average = computed(() =>
    facilities.value.length
        ? Math.round(facilities.value.reduce((sum, f) => sum + completeness(f), 0) / facilities.value.length)
        : 0,
);
const stale = computed(() =>
    facilities.value.filter((f) => daysSince(f.lastChecked) > STALE_DAYS),
);

// Tabel Pengelolaan fasilitas: satu halaman per permintaan, bukan 1.117 baris sekaligus.
const perPage = computed(() => tablePage.meta?.perPage ?? 10);
const currentPage = computed(() => tablePage.meta?.currentPage ?? 1);
const lastPage = computed(() => tablePage.meta?.lastPage ?? 1);

const rangeLabel = computed(() =>
    tablePage.meta?.from
        ? `Menampilkan ${tablePage.meta.from}–${tablePage.meta.to} dari ${tablePage.total} fasilitas`
        : 'Tidak ada fasilitas pada halaman ini',
);

/** Jendela 5 nomor di sekitar halaman aktif; sisanya lewat tombol sebelumnya/berikutnya. */
const pageNumbers = computed(() => {
    const start = Math.max(1, Math.min(currentPage.value - 2, lastPage.value - 4));

    return Array.from({ length: Math.min(5, lastPage.value) }, (_, index) => start + index);
});

const goToPage = (number) => loadTablePage(number, perPage.value);
const changePerPage = (event) => loadTablePage(1, Number(event.target.value));

onMounted(() => loadTablePage(1, 10));
</script>

<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Dashboard administrasi</p>
                <h1 class="display-lg">Kelola fasilitas, kategori, dan <em>peran pengguna.</em></h1>
                <p class="body-copy">
                    Ringkasan kualitas data pemetaan: berapa fasilitas yang sudah terverifikasi, seberapa lengkap
                    datanya, dan mana yang perlu diperiksa ulang.
                </p>
            </div>

            <div class="grid grid--4">
                <StatCard :value="facilities.length" label="Fasilitas terpetakan" hint="Seluruh kategori" />
                <StatCard :value="verified" label="Terverifikasi" hint="Sudah diperiksa" />
                <StatCard :value="pending" label="Belum terverifikasi" hint="Menunggu atau perlu perbaikan" />
                <StatCard :value="`${average}%`" label="Rata-rata kelengkapan" hint="Atribut yang diketahui" />
            </div>

            <div class="grid grid--sidebar">
                <div class="panel stack--sm">
                    <h2 class="display-sm">Kelengkapan data per kategori</h2>
                    <p class="help">
                        Kategori dengan rata-rata terendah berada di atas — prioritas pemeriksaan lapangan
                        berikutnya.
                    </p>
                    <CompletenessChart :facilities="facilities" />
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">Perlu diperbarui</h2>
                    <p class="help">
                        Pemeriksaan terakhir lebih dari {{ STALE_DAYS }} hari lalu. Data lama berisiko menyesatkan
                        pengguna.
                    </p>
                    <ul class="steps">
                        <li v-for="facility in stale" :key="facility.id" class="step">
                            <span class="step__num" aria-hidden="true">△</span>
                            <span>
                                <b>{{ facility.name }}</b>
                                <span style="display: block; color: var(--body)">{{ facility.city }}</span>
                            </span>
                            <span class="step__time">{{ daysSince(facility.lastChecked) }} hari</span>
                        </li>
                    </ul>
                    <p v-if="!stale.length" class="help">Semua fasilitas diperiksa dalam {{ STALE_DAYS }} hari terakhir.</p>
                </div>
            </div>

            <div class="panel stack--sm">
                <h2 class="display-sm">Pengelolaan fasilitas</h2>

                <div class="cluster cluster--between">
                    <p class="help" role="status" aria-live="polite">{{ rangeLabel }}</p>
                    <div class="field" style="width: 110px">
                        <label for="baris-per-halaman">Baris per halaman</label>
                        <select id="baris-per-halaman" class="select" :value="perPage" @change="changePerPage">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                        </select>
                    </div>
                </div>

                <FacilityTable :facilities="tablePage.rows" />

                <nav v-if="tablePage.meta" class="cluster" aria-label="Navigasi halaman fasilitas">
                    <button type="button" class="btn btn--sm" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
                        Sebelumnya
                    </button>
                    <button
                        v-for="number in pageNumbers"
                        :key="number"
                        type="button"
                        class="tab"
                        :aria-pressed="number === currentPage"
                        @click="goToPage(number)"
                    >
                        {{ number }}
                    </button>
                    <button type="button" class="btn btn--sm" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">
                        Berikutnya
                    </button>
                </nav>

                <p v-if="tablePage.status === 'gagal'" class="error-text">
                    Gagal memuat halaman fasilitas ({{ tablePage.error }}). Coba muat ulang halaman.
                </p>
            </div>

            <div class="grid grid--2 grid--stack">
                <div class="panel stack--sm">
                    <h2 class="display-sm">Kategori</h2>
                    <CategoryManager />
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">Akun admin</h2>
                    <p class="help">
                        Hanya admin yang memiliki akun dan dapat masuk; masyarakat mengirim usulan tanpa mendaftar.
                    </p>
                    <UserRolesPanel />
                </div>
            </div>

            <div class="notice">
                <b>Data fasilitas dari database</b>
                <span>
                    Angka di atas dihitung dari fasilitas hasil impor dataset OpenStreetMap ({{ VERIFICATION.menunggu.label }}
                    termasuk status menunggu). Akun admin sudah asli; kontribusi masyarakat dan verifikasi masih
                    berupa demonstrasi di sisi klien.
                </span>
            </div>
        </div>
    </section>
</template>