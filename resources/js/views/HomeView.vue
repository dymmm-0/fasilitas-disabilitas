<script setup>
import { computed } from 'vue';
import { useFacilities } from '../composables/useFacilities';
import FacilityFilters from '../components/facilities/FacilityFilters.vue';
import FacilityList from '../components/facilities/FacilityList.vue';
import FacilityMap from '../components/map/FacilityMap.vue';
import MapLegend from '../components/map/MapLegend.vue';
import Reveal from '../components/common/Reveal.vue';

const { filters, facilities, results, selectedId, select } = useFacilities();

const verified = computed(() => facilities.value.filter((f) => f.verification === 'terverifikasi').length);
const cities = computed(() => new Set(facilities.value.map((f) => f.city)).size);
</script>

<template>
    <section class="container hero">
        <Reveal class="stack">
            <p class="label">Platform pemetaan fasilitas publik</p>
            <h1 class="display-xl">Temukan fasilitas yang bisa <em>diakses semua orang.</em></h1>
            <p class="body-copy">
                AksesKita memetakan ramp, toilet aksesibel, lift, dan jalur kursi roda di fasilitas publik — lalu
                memverifikasinya bersama masyarakat agar keputusan berkegiatan tidak lagi menebak-nebak.
            </p>

            <form class="searchbar" role="search" @submit.prevent>
                <div class="field">
                    <label for="hero-cari">Cari fasilitas atau kota</label>
                    <input
                        id="hero-cari"
                        v-model="filters.query"
                        class="input"
                        type="search"
                        placeholder="Contoh: stasiun, rumah sakit, Yogyakarta"
                        autocomplete="off"
                    >
                </div>
                <a class="btn btn--primary" href="#peta">
                    Lihat peta
                    <span aria-hidden="true">↓</span>
                </a>
            </form>

            <p class="meta" role="status" aria-live="polite">
                {{ results.length }} fasilitas sesuai pencarian · {{ verified }} terverifikasi · {{ cities }} kota
            </p>
        </Reveal>

        <Reveal class="hero__media hero-shadow">
            <div class="hero__frame"></div>
            <p class="stamp">
                {{ facilities.length }} fasilitas<b>dipetakan warga</b>
            </p>
        </Reveal>
    </section>

    <section id="peta" class="section">
        <div class="container stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Peta dan daftar</p>
                <h2 class="display-lg">Cari, saring, lalu buka <em>detail aksesibilitasnya.</em></h2>
                <p class="body-copy">
                    Pilih kategori atau atribut aksesibilitas yang Anda butuhkan. Klik penanda di peta atau kartu di
                    samping — keduanya menampilkan fasilitas yang sama.
                </p>
            </div>

            <FacilityFilters />

            <div class="grid grid--map">
                <div class="map-shell">
                    <FacilityMap
                        :facilities="results"
                        :selected-id="selectedId"
                        @select="select"
                    />
                    <MapLegend />
                    <p class="help">
                        Peta bersifat visual. Informasi yang sama tersedia sebagai daftar kartu di samping, dan setiap
                        penanda dapat difokuskan dengan tombol Tab.
                    </p>
                </div>

                <div>
                    <div class="results-head">
                        <h3 class="display-sm">{{ results.length }} fasilitas</h3>
                        <RouterLink class="btn btn--sm" to="/kontribusi">Usulkan fasilitas baru</RouterLink>
                    </div>
                    <FacilityList :facilities="results" :selected-id="selectedId" @select="select" />
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container stack">
            <div class="section__head">
                <p class="label">Cara kerja</p>
                <h2 class="display-lg">Dari temuan warga sampai <em>data terverifikasi.</em></h2>
            </div>

            <ol class="steps">
                <li class="step">
                    <span class="step__num">i.</span>
                    <span><b>Warga mengusulkan.</b> Nama, alamat, koordinat, foto, dan checklist aksesibilitas dikirim lewat formulir kontribusi.</span>
                    <span class="step__time">Tahap 1</span>
                </li>
                <li class="step">
                    <span class="step__num">ii.</span>
                    <span><b>Verifikator memeriksa.</b> Bukti foto dan koordinat ditinjau; pengajuan dapat disetujui, ditolak, atau diminta diperbaiki.</span>
                    <span class="step__time">Tahap 2</span>
                </li>
                <li class="step">
                    <span class="step__num">iii.</span>
                    <span><b>Data tampil di peta.</b> Status kelengkapan dan tanggal pemeriksaan terakhir selalu ikut ditampilkan agar pengguna tahu seberapa baru datanya.</span>
                    <span class="step__time">Tahap 3</span>
                </li>
            </ol>

            <div class="inline-actions">
                <RouterLink class="btn btn--primary" to="/kontribusi">Mulai berkontribusi</RouterLink>
                <RouterLink class="btn" to="/tentang">Baca panduan lengkap</RouterLink>
            </div>
        </div>
    </section>
</template>