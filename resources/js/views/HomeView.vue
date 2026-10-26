<script setup>
import { computed, ref, watch } from 'vue';
import { useFacilities } from '../composables/useFacilities';
import FacilityFilters from '../components/facilities/FacilityFilters.vue';
import FacilityList from '../components/facilities/FacilityList.vue';
import FacilityMap from '../components/map/FacilityMap.vue';
import MapLegend from '../components/map/MapLegend.vue';
import Reveal from '../components/common/Reveal.vue';

const { filters, facilities, results, selectedId, select, status, loadError } = useFacilities();
const page = ref(1);
const PAGE_SIZE = 5;
const pageCount = computed(() => Math.max(1, Math.ceil(results.value.length / PAGE_SIZE)));
watch(results, () => {
    page.value = 1;
});

const selectFacility = (id) => {
    const index = results.value.findIndex((facility) => facility.id === id);
    if (index >= 0) page.value = Math.floor(index / PAGE_SIZE) + 1;
    select(id);
};

const pageFacilities = computed(() => results.value.slice((page.value - 1) * PAGE_SIZE, page.value * PAGE_SIZE));
const rangeStart = computed(() => (results.value.length ? (page.value - 1) * PAGE_SIZE + 1 : 0));
const rangeEnd = computed(() => Math.min(page.value * PAGE_SIZE, results.value.length));
const paginationPages = computed(() => {
    const start = Math.max(1, Math.min(page.value - 2, pageCount.value - 4));
    const end = Math.min(pageCount.value, start + 4);

    return Array.from({ length: end - start + 1 }, (_, index) => start + index);
});
const showPage = (number) => { page.value = Math.min(Math.max(number, 1), pageCount.value); };

const verified = computed(() => facilities.value.filter((f) => f.verification === 'terverifikasi').length);
const cities = computed(() => new Set(facilities.value.map((f) => f.city)).size);
</script>

<template>
    <section class="container hero">
        <Reveal class="stack">
            <p class="label">{{ $t('Platform pemetaan fasilitas publik') }}</p>
            <h1 class="display-xl"><i18n-t keypath="Temukan fasilitas yang bisa {a}" tag="span" scope="global"><template #a><em>{{ $t('diakses semua orang.') }}</em></template></i18n-t></h1>
            <p class="body-copy">{{ $t('AksesKita memetakan ramp, toilet aksesibel, lift, jalur pejalan kaki aksesibel, dan fasilitas pendukung lain di fasilitas publik — lalu memverifikasinya bersama masyarakat agar keputusan berkegiatan tidak lagi menebak-nebak.') }}</p>

            <form class="searchbar" role="search" @submit.prevent>
                <div class="field">
                    <label for="hero-cari">{{ $t('Cari fasilitas atau kota') }}</label>
                    <input
                        id="hero-cari"
                        v-model="filters.query"
                        class="input"
                        type="search"
                        :placeholder="$t('Contoh: rumah sakit, puskesmas, Medan')"
                        autocomplete="off"
                    >
                </div>
                <a class="btn btn--primary" href="#peta">{{ $t('Lihat peta') }}<span aria-hidden="true">↓</span>
                </a>
            </form>

            <p class="meta" role="status" aria-live="polite">
                <i18n-t keypath="{a} fasilitas sesuai pencarian · {b} terverifikasi · {c} kota" tag="span" scope="global"><template #a>{{ results.length }}</template><template #b>{{ verified }}</template><template #c>{{ cities }}</template></i18n-t></p>
        </Reveal>

        <Reveal class="hero__media hero-shadow">
            <div class="hero__frame"></div>
            <p class="stamp">
                {{ $t('{0} fasilitas', [facilities.length]) }}<b>{{ $t('dipetakan warga') }}</b>
            </p>
        </Reveal>
    </section>

    <section id="peta" class="section">
        <div class="container stack">
            <div class="section__head" style="max-width: none">
                <p class="label">{{ $t('Peta dan daftar') }}</p>
                <h2 class="display-lg"><i18n-t keypath="Cari, saring, lalu buka {a}" tag="span" scope="global"><template #a><em>{{ $t('detail aksesibilitasnya.') }}</em></template></i18n-t></h2>
                <p class="body-copy">{{ $t('Pilih kategori atau atribut aksesibilitas yang Anda butuhkan. Klik penanda di peta atau kartu di samping — keduanya menampilkan fasilitas yang sama.') }}</p>
            </div>

            <FacilityFilters />

            <div class="grid grid--map">
                <div class="map-shell">
                    <FacilityMap
                        :facilities="results"
                        :selected-id="selectedId"
                        @select="selectFacility"
                    />
                    <MapLegend />
                    <p class="help">{{ $t('Peta bersifat visual. Informasi yang sama tersedia sebagai daftar kartu di samping, dan setiap penanda dapat difokuskan dengan tombol Tab.') }}</p>
                </div>

                <div>
                    <div class="results-head">
                        <h3 class="display-sm">
                            {{ status === 'memuat' ? $t('Memuat data fasilitas…') : $t('{0} fasilitas', [results.length]) }}`
                        </h3>
                        <RouterLink class="btn btn--sm" to="/kontribusi">{{ $t('Usulkan fasilitas baru') }}</RouterLink>
                    </div>
                    <div v-if="status === 'gagal'" class="notice" role="alert">
                        <b>{{ $t('Gagal memuat data') }}</b>
                        <span><i18n-t keypath="Server tidak mengirim daftar fasilitas ({a}). Muat ulang halaman untuk mencoba lagi." tag="span" scope="global"><template #a>{{ loadError }}</template></i18n-t></span>
                    </div>
                    <FacilityList :facilities="pageFacilities" :selected-id="selectedId" @select="selectFacility" />
                    <nav v-if="results.length" class="pagination" :aria-label="$t('Pagination fasilitas')">
                        <span class="meta"><i18n-t keypath="{a}–{b} dari {c} fasilitas" tag="span" scope="global"><template #a>{{ rangeStart }}</template><template #b>{{ rangeEnd }}</template><template #c>{{ results.length }}</template></i18n-t></span>
                        <div class="pagination__controls">
                            <button class="btn btn--sm" type="button" :disabled="page === 1" @click="showPage(page - 1)">{{ $t('Sebelumnya') }}</button>
                            <button
                                v-for="number in paginationPages"
                                :key="number"
                                class="btn btn--sm"
                                :class="{ 'btn--primary': number === page }"
                                type="button"
                                :aria-current="number === page ? 'page' : undefined"
                                @click="showPage(number)"
                            >{{ number }}</button>
                            <button class="btn btn--sm" type="button" :disabled="page === pageCount" @click="showPage(page + 1)">{{ $t('Berikutnya') }}</button>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container stack">
            <div class="section__head">
                <p class="label">{{ $t('Cara kerja') }}</p>
                <h2 class="display-lg"><i18n-t keypath="Dari temuan warga sampai {a}" tag="span" scope="global"><template #a><em>{{ $t('data terverifikasi.') }}</em></template></i18n-t></h2>
            </div>

            <ol class="steps">
                <li class="step">
                    <span class="step__num">i.</span>
                    <span><i18n-t keypath="{a} Nama, alamat, koordinat, foto, dan checklist aksesibilitas dikirim lewat formulir kontribusi." tag="span" scope="global"><template #a><b>{{ $t('Warga mengusulkan.') }}</b></template></i18n-t></span>
                    <span class="step__time">{{ $t('Tahap 1') }}</span>
                </li>
                <li class="step">
                    <span class="step__num">ii.</span>
                    <span><i18n-t keypath="{a} Bukti foto dan koordinat ditinjau; pengajuan dapat disetujui, ditolak, atau diminta diperbaiki." tag="span" scope="global"><template #a><b>{{ $t('Verifikator memeriksa.') }}</b></template></i18n-t></span>
                    <span class="step__time">{{ $t('Tahap 2') }}</span>
                </li>
                <li class="step">
                    <span class="step__num">iii.</span>
                    <span><i18n-t keypath="{a} Status kelengkapan dan tanggal pemeriksaan terakhir selalu ikut ditampilkan agar pengguna tahu seberapa baru datanya." tag="span" scope="global"><template #a><b>{{ $t('Data tampil di peta.') }}</b></template></i18n-t></span>
                    <span class="step__time">{{ $t('Tahap 3') }}</span>
                </li>
            </ol>

            <div class="inline-actions">
                <RouterLink class="btn btn--primary" to="/kontribusi">{{ $t('Mulai berkontribusi') }}</RouterLink>
                <RouterLink class="btn" to="/tentang">{{ $t('Baca panduan lengkap') }}</RouterLink>
            </div>
        </div>
    </section>
</template>