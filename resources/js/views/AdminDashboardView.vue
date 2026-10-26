<script setup>
import { computed, ref, watch } from 'vue';
import { t } from '../i18n';
import { completeness } from '../data/facilities';
import { daysSince, needsVerification, STALE_DAYS, VERIFICATION } from '../data/meta';
import { useFacilities } from '../composables/useFacilities';
import StatCard from '../components/dashboard/StatCard.vue';
import CompletenessChart from '../components/dashboard/CompletenessChart.vue';
import FacilityTable from '../components/dashboard/FacilityTable.vue';
import FacilityFilters from '../components/facilities/FacilityFilters.vue';
import CategoryManager from '../components/dashboard/CategoryManager.vue';
import UserRolesPanel from '../components/dashboard/UserRolesPanel.vue';

const { facilities, results, status, loadError } = useFacilities();

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

// Tabel Pengelolaan fasilitas disaring di klien dari daftar yang sudah dimuat peta,
// sama seperti pencarian beranda (`FacilityFilters` + `filters`/`results`). Pagination
// mengikuti hasil saringan, bukan seluruh 1.118 baris.
const perPage = ref(10);
const page = ref(1);
const pageCount = computed(() => Math.max(1, Math.ceil(results.value.length / perPage.value)));
watch(results, () => { page.value = 1; });

const pageRows = computed(() =>
    results.value.slice((page.value - 1) * perPage.value, page.value * perPage.value),
);

const rangeLabel = computed(() =>
    results.value.length
        ? t('Menampilkan {0}–{1} dari {2} fasilitas', [
            (page.value - 1) * perPage.value + 1,
            Math.min(page.value * perPage.value, results.value.length),
            results.value.length,
        ])
        : t('Tidak ada fasilitas pada halaman ini'),
);

/** Jendela 5 nomor di sekitar halaman aktif; sisanya lewat tombol sebelumnya/berikutnya. */
const pageNumbers = computed(() => {
    const start = Math.max(1, Math.min(page.value - 2, pageCount.value - 4));

    return Array.from({ length: Math.min(5, pageCount.value) }, (_, index) => start + index);
});

const goToPage = (number) => { page.value = Math.min(Math.max(number, 1), pageCount.value); };
const changePerPage = (event) => { perPage.value = Number(event.target.value); page.value = 1; };
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

                <FacilityFilters />

                <div v-if="status === 'gagal'" class="notice" role="alert">
                    <b>{{ $t('Gagal memuat data') }}</b>
                    <span><i18n-t keypath="Server tidak mengirim daftar fasilitas ({a}). Muat ulang halaman untuk mencoba lagi." tag="span" scope="global"><template #a>{{ loadError }}</template></i18n-t></span>
                </div>

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

                <FacilityTable v-if="pageRows.length" :facilities="pageRows" />
                <div v-else-if="status !== 'gagal'" class="empty-state" role="status">
                    <span class="label">{{ $t('Hasil kosong') }}</span>
                    <p class="card__title card__title--sm">{{ $t('Tidak ada fasilitas yang cocok') }}</p>
                    <p class="card__text">{{ $t('Ubah kata kunci atau kosongkan filter untuk melihat semua fasilitas.') }}</p>
                </div>

                <nav v-if="pageCount > 1" class="pagination" :aria-label="$t('Navigasi halaman fasilitas')">
                    <div class="pagination__controls">
                        <button type="button" class="btn btn--sm" :disabled="page === 1" @click="goToPage(page - 1)">{{ $t('Sebelumnya') }}</button>
                        <button
                            v-for="number in pageNumbers"
                            :key="number"
                            type="button"
                            class="btn btn--sm"
                            :class="{ 'btn--primary': number === page }"
                            :aria-current="number === page ? 'page' : undefined"
                            @click="goToPage(number)"
                        >
                            {{ number }}
                        </button>
                        <button type="button" class="btn btn--sm" :disabled="page === pageCount" @click="goToPage(page + 1)">{{ $t('Berikutnya') }}</button>
                    </div>
                </nav>
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