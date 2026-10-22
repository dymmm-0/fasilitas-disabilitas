<script setup>
import { computed } from 'vue';
import { completeness } from '../data/facilities';
import { daysSince, STALE_DAYS, VERIFICATION } from '../data/meta';
import { useFacilities } from '../composables/useFacilities';
import StatCard from '../components/dashboard/StatCard.vue';
import CompletenessChart from '../components/dashboard/CompletenessChart.vue';
import FacilityTable from '../components/dashboard/FacilityTable.vue';
import CategoryManager from '../components/dashboard/CategoryManager.vue';
import UserRolesPanel from '../components/dashboard/UserRolesPanel.vue';

const { facilities } = useFacilities();

const verified = computed(() => facilities.value.filter((f) => f.verification === 'terverifikasi').length);
const pending = computed(() => facilities.value.filter((f) => f.verification !== 'terverifikasi').length);
const average = computed(() =>
    facilities.value.length
        ? Math.round(facilities.value.reduce((sum, f) => sum + completeness(f), 0) / facilities.value.length)
        : 0,
);
// Tabel hanya memuat sebagian baris supaya dashboard tetap ringan; daftar penuh ada di peta.
const tableRows = computed(() => facilities.value.slice(0, 50));
const stale = computed(() =>
    facilities.value.filter((f) => daysSince(f.lastChecked) > STALE_DAYS),
);
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
                <FacilityTable
                    :facilities="tableRows"
                    :caption="`Menampilkan ${tableRows.length} dari ${facilities.length} fasilitas. Kelengkapan data dihitung dari atribut aksesibilitas yang sudah diketahui.`"
                />
            </div>

            <div class="grid grid--2 grid--stack">
                <div class="panel stack--sm">
                    <h2 class="display-sm">Kategori</h2>
                    <CategoryManager />
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">Pengguna dan peran</h2>
                    <p class="help">
                        Administrator mengelola fasilitas dan kategori; verifikator memutuskan pengajuan; kontributor
                        mengirim usulan dan koreksi.
                    </p>
                    <UserRolesPanel />
                </div>
            </div>

            <div class="notice">
                <b>Data fasilitas dari database</b>
                <span>
                    Angka di atas dihitung dari fasilitas hasil impor dataset OpenStreetMap ({{ VERIFICATION.menunggu.label }}
                    termasuk status menunggu). Autentikasi, kontribusi masyarakat, dan verifikasi masih berupa demonstrasi di
                    sisi klien.
                </span>
            </div>
        </div>
    </section>
</template>