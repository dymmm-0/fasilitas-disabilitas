<script setup>
import { categoryLabel, daysSince, formatDate, STALE_DAYS, VERIFICATION } from '../../data/meta';
import StatusPill from '../common/StatusPill.vue';
import CompletenessMeter from '../facilities/CompletenessMeter.vue';

defineProps({
    facilities: { type: Array, required: true },
    caption: { type: String, default: 'Kelengkapan data dihitung dari atribut aksesibilitas yang sudah diketahui.' },
});

const stale = (facility) => daysSince(facility.lastChecked) > STALE_DAYS;
</script>

<template>
    <div class="table-wrap" tabindex="0" role="region" aria-label="Tabel fasilitas">
        <table class="table">
            <caption class="help" style="text-align: left; padding-bottom: 12px">{{ caption }}</caption>
            <thead>
                <tr>
                    <th scope="col">Fasilitas</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Kelengkapan data</th>
                    <th scope="col">Verifikasi</th>
                    <th scope="col">Pemeriksaan terakhir</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="facility in facilities" :key="facility.id">
                    <td>
                        <b>{{ facility.name }}</b>
                        <span class="meta" style="display: block">{{ facility.city }} · {{ facility.id }}</span>
                    </td>
                    <td>{{ categoryLabel(facility.category) }}</td>
                    <td style="min-width: 200px"><CompletenessMeter :facility="facility" /></td>
                    <td><StatusPill kind="verification" :value="facility.verification" /></td>
                    <td>
                        {{ formatDate(facility.lastChecked) }}
                        <span v-if="stale(facility)" class="pill pill--pending" style="margin-top: 6px">
                            <span class="pill__glyph" aria-hidden="true">△</span>Perlu diperbarui
                        </span>
                    </td>
                    <td>
                        <RouterLink class="btn btn--sm" :to="`/fasilitas/${facility.id}`">Detail</RouterLink>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="help">{{ VERIFICATION.terverifikasi.label }} berarti data sudah diperiksa minimal satu verifikator.</p>
    </div>
</template>