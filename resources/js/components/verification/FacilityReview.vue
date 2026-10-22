<script setup>
import { computed } from 'vue';
import { ATTRIBUTES, AVAILABILITY, VERIFICATION, formatDate } from '../../data/meta';
import CompletenessMeter from '../facilities/CompletenessMeter.vue';
import StatusPill from '../common/StatusPill.vue';
import ReviewActions from './ReviewActions.vue';

/** Detail fasilitas yang diperiksa petugas: sumber data, indikator, dan keputusan. */
const props = defineProps({
    item: { type: Object, required: true },
    busy: { type: Boolean, default: false },
    error: { type: String, default: '' },
});

const emit = defineEmits(['decide']);

const facility = computed(() => props.item.facility);

const actions = [
    { id: 'verified', label: '✓ Tandai terverifikasi', variant: 'primary' },
    { id: 'needs_repair', label: '△ Minta perbaikan' },
    { id: 'rejected', label: '✕ Tandai ditolak' },
];

const doneLabel = computed(() => {
    if (facility.value.verification !== 'terverifikasi') return '';

    const by = facility.value.verifiedBy ? ` oleh ${facility.value.verifiedBy}` : '';
    const at = facility.value.verifiedAt ? ` pada ${formatDate(facility.value.verifiedAt)}` : '';

    return `Fasilitas sudah ditandai terverifikasi${by}${at}.`;
});

const verificationNote = computed(() => facility.value.verificationNote || '');
</script>

<template>
    <div class="panel stack">
        <div class="cluster cluster--between">
            <div>
                <p class="meta">
                    {{ item.source }} · {{ facility.id }} · diperbarui {{ formatDate(facility.updatedAt) }}
                </p>
                <h2 class="display-sm">{{ facility.name }}</h2>
            </div>
            <StatusPill kind="verification" :value="facility.verification" />
        </div>

        <div class="kv">
            <div class="kv__row">
                <span class="kv__label">Alamat</span>
                <span>{{ facility.address || 'Alamat jalan belum ada di sumber data.' }}</span>
                <span class="kv__note">{{ [facility.village, facility.subdistrict, facility.city].filter(Boolean).join(' · ') }}</span>
            </div>

            <div v-for="attribute in ATTRIBUTES" :key="attribute.id" class="kv__row">
                <span class="kv__label">{{ attribute.label }}</span>
                <StatusPill kind="availability" :value="facility.accessibility[attribute.id]" />
                <span class="kv__note">
                    {{ facility.accessibilityDetails[attribute.id] || AVAILABILITY[facility.accessibility[attribute.id]].label }}
                </span>
            </div>

            <div class="kv__row">
                <span class="kv__label">Sumber data</span>
                <span>{{ facility.contributor || 'Tidak diketahui' }}</span>
                <a
                    v-if="facility.source.url"
                    class="kv__note"
                    :href="facility.source.url"
                    target="_blank"
                    rel="noopener"
                >{{ facility.source.ref }}</a>
                <span v-else class="kv__note">{{ facility.source.ref || 'Tanpa ID sumber' }}</span>
            </div>

            <div class="kv__row">
                <span class="kv__label">Koordinat</span>
                <span>{{ facility.lat }}, {{ facility.lng }}</span>
                <span class="kv__note">{{ facility.id }}</span>
            </div>

            <div v-if="item.pendingReports.length" class="kv__row">
                <span class="kv__label">Laporan koreksi menunggu</span>
                <span>
                    <span v-for="report in item.pendingReports" :key="report.id" style="margin-right: 12px">
                        {{ report.id }} ({{ report.status }})
                    </span>
                </span>
                <span class="kv__note">Laporan ini terhubung ke fasilitas {{ facility.id }}, bukan baris baru.</span>
            </div>

            <div v-if="verificationNote" class="kv__row">
                <span class="kv__label">Catatan pemeriksaan</span>
                <span class="kv__note">{{ verificationNote }}</span>
                <span></span>
            </div>
        </div>

        <div class="stack--sm">
            <h3 class="meta">Kelengkapan data</h3>
            <CompletenessMeter :facility="facility" />
        </div>

        <p v-if="facility.verification === 'ditolak'" class="help" role="status">
            Fasilitas ini ditandai {{ VERIFICATION.ditolak.label.toLowerCase() }}, tetapi barisnya tetap tersimpan.
        </p>

        <RouterLink class="btn" :to="`/fasilitas/${facility.id}`">Buka halaman fasilitas</RouterLink>

        <ReviewActions
            :subject-id="facility.id"
            :actions="actions"
            :done-label="doneLabel"
            :busy="busy"
            :error="error"
            @decide="emit('decide', $event)"
        />
    </div>
</template>