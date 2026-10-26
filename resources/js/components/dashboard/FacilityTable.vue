<script setup>
import { categoryLabel, daysSince, formatDate, STALE_DAYS, VERIFICATION } from '../../data/meta';
import { t } from '../../i18n';
import StatusPill from '../common/StatusPill.vue';
import CompletenessMeter from '../facilities/CompletenessMeter.vue';

defineProps({
    facilities: { type: Array, required: true },
    caption: { type: String, default: t('Kelengkapan data dihitung dari atribut aksesibilitas yang sudah diketahui.') },
});

const stale = (facility) => daysSince(facility.lastChecked) > STALE_DAYS;
</script>

<template>
    <div class="table-wrap" tabindex="0" role="region" :aria-label="$t('Tabel fasilitas')">
        <table class="table">
            <caption class="help" style="text-align: left; padding-bottom: 12px">{{ caption }}</caption>
            <thead>
                <tr>
                    <th scope="col">{{ $t('Fasilitas') }}</th>
                    <th scope="col">{{ $t('Kategori') }}</th>
                    <th scope="col">{{ $t('Kelengkapan data') }}</th>
                    <th scope="col">{{ $t('Verifikasi') }}</th>
                    <th scope="col">{{ $t('Pemeriksaan terakhir') }}</th>
                    <th scope="col">{{ $t('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!facilities.length">
                    <td colspan="6" class="help">{{ $t('Tidak ada fasilitas pada halaman ini.') }}</td>
                </tr>
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
                            <span class="pill__glyph" aria-hidden="true">△</span>{{ $t('Perlu diperbarui') }}</span>
                    </td>
                    <td>
                        <RouterLink class="btn btn--sm" :to="`/fasilitas/${facility.id}`">{{ $t('Detail') }}</RouterLink>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="help"><i18n-t keypath="{a} berarti data sudah diperiksa minimal satu verifikator." tag="span" scope="global"><template #a>{{ $t(VERIFICATION.terverifikasi.label) }}</template></i18n-t></p>
    </div>
</template>