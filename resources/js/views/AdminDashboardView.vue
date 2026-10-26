<script setup>
import { computed, onMounted } from 'vue';
import { t } from '../i18n';
import { completeness } from '../data/facilities';
import { daysSince, needsVerification, STALE_DAYS, VERIFICATION } from '../data/meta';
import { useFacilities } from '../composables/useFacilities';
import StatCard from '../components/dashboard/StatCard.vue';
import CompletenessChart from '../components/dashboard/CompletenessChart.vue';
import FacilityTable from '../components/dashboard/FacilityTable.vue';
import CategoryManager from '../components/dashboard/CategoryManager.vue';
import UserRolesPanel from '../components/dashboard/UserRolesPanel.vue';

const { facilities, tablePage, loadTablePage } = useFacilities();

const verified = computed(() => facilities.value.filter((f) => f.verification === 'terverifikasi').length);
const pending = computed(() => facilities.value.filter(needsVerification).length);
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
        ? t('Menampilkan {0}–{1} dari {2} fasilitas', [tablePage.meta.from, tablePage.meta.to, tablePage.total])
        : t('Tidak ada fasilitas pada halaman ini'),
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
                <p class="label">{{ $t('Dashboard administrasi') }}</p>
                <h1 class="display-lg"><i18n-t keypath="Kelola fasilitas, kategori, dan {a}" tag="span" scope="global"><template #a><em>{{ $t('peran pengguna.') }}</em></template></i18n-t></h1>
                <p class="body-copy">{{ $t('Ringkasan kualitas data pemetaan: berapa fasilitas yang sudah terverifikasi, seberapa lengkap datanya, dan mana yang perlu diperiksa ulang.') }}</p>
            </div>

            <div class="grid grid--4">
                <StatCard :value="facilities.length" label="Fasilitas terpetakan" hint="Seluruh kategori" />
                <StatCard :value="verified" label="Terverifikasi" hint="Sudah diperiksa" />
                <StatCard :value="pending" label="Belum terverifikasi" hint="Menunggu atau perlu perbaikan" />
                <StatCard :value="`${average}%`" label="Rata-rata kelengkapan" hint="Atribut yang diketahui" />
            </div>

            <div class="grid grid--sidebar">
                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Kelengkapan data per kategori') }}</h2>
                    <p class="help">{{ $t('Kategori dengan rata-rata terendah berada di atas — prioritas pemeriksaan lapangan berikutnya.') }}</p>
                    <CompletenessChart :facilities="facilities" />
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Perlu diperbarui') }}</h2>
                    <p class="help"><i18n-t keypath="Pemeriksaan terakhir lebih dari {a} hari lalu. Data lama berisiko menyesatkan pengguna." tag="span" scope="global"><template #a>{{ STALE_DAYS }}</template></i18n-t></p>
                    <ul class="steps">
                        <li v-for="facility in stale" :key="facility.id" class="step">
                            <span class="step__num" aria-hidden="true">△</span>
                            <span>
                                <b>{{ facility.name }}</b>
                                <span style="display: block; color: var(--body)">{{ facility.city }}</span>
                            </span>
                            <span class="step__time">{{ $t('{0} hari', [daysSince(facility.lastChecked)]) }}</span>
                        </li>
                    </ul>
                    <p v-if="!stale.length" class="help"><i18n-t keypath="Semua fasilitas diperiksa dalam {a} hari terakhir." tag="span" scope="global"><template #a>{{ STALE_DAYS }}</template></i18n-t></p>
                </div>
            </div>

            <div id="pengelolaan-fasilitas" class="panel stack--sm">
                <h2 class="display-sm">{{ $t('Pengelolaan fasilitas') }}</h2>

                <div class="cluster cluster--between">
                    <p class="help" role="status" aria-live="polite">{{ rangeLabel }}</p>
                    <div class="field" style="width: 110px">
                        <label for="baris-per-halaman">{{ $t('Baris per halaman') }}</label>
                        <select id="baris-per-halaman" class="select" :value="perPage" @change="changePerPage">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                        </select>
                    </div>
                </div>

                <FacilityTable :facilities="tablePage.rows" />

                <nav v-if="tablePage.meta" class="cluster" :aria-label="$t('Navigasi halaman fasilitas')">
                    <button type="button" class="btn btn--sm" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">{{ $t('Sebelumnya') }}</button>
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
                    <button type="button" class="btn btn--sm" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">{{ $t('Berikutnya') }}</button>
                </nav>

                <p v-if="tablePage.status === 'gagal'" class="error-text"><i18n-t keypath="Gagal memuat halaman fasilitas ({a}). Coba muat ulang halaman." tag="span" scope="global"><template #a>{{ tablePage.error }}</template></i18n-t></p>
            </div>

            <div class="grid grid--2 grid--stack">
                <div id="kategori" class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Kategori') }}</h2>
                    <CategoryManager />
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Akun admin') }}</h2>
                    <p class="help">{{ $t('Hanya admin yang memiliki akun dan dapat masuk; masyarakat mengirim usulan tanpa mendaftar.') }}</p>
                    <UserRolesPanel />
                </div>
            </div>

            <div class="notice">
                <b>{{ $t('Data fasilitas dari database') }}</b>
                <span><i18n-t keypath="Angka di atas dihitung dari fasilitas hasil impor dataset OpenStreetMap ({a} termasuk status menunggu). Akun admin, antrean kontribusi masyarakat, dan keputusan verifikasi tersimpan di basis data aplikasi." tag="span" scope="global"><template #a>{{ $t(VERIFICATION.menunggu.label) }}</template></i18n-t></span>
            </div>
        </div>
    </section>
</template>