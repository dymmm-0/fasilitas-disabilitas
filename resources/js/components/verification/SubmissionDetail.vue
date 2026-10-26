<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { attributeLabel, AVAILABILITY, formatDate, SUBMISSION_STATUSES, SUBMISSION_TYPES } from '../../data/meta';
import StatusPill from '../common/StatusPill.vue';
import ReviewActions from './ReviewActions.vue';

const { t } = useI18n();

const props = defineProps({
    submission: { type: Object, required: true },
    busy: { type: Boolean, default: false },
    error: { type: String, default: '' },
});

const emit = defineEmits(['decide']);

const isUsulan = computed(() => props.submission.type === 'usulan');
const accessibility = computed(() => props.submission.payload.accessibility ?? {});

const actions = [
    { id: 'disetujui', label: '✓ Setujui', variant: 'primary' },
    { id: 'perbaikan', label: '△ Minta perbaikan' },
    { id: 'ditolak', label: '✕ Tolak' },
];

const doneLabel = computed(() =>
    ['disetujui', 'ditolak'].includes(props.submission.status)
        ? t('Keputusan sudah dibuat: {0}. Riwayat tersimpan di basis data.', [t(SUBMISSION_STATUSES[props.submission.status].label)])
        : '',
);
</script>

<template>
    <div class="panel stack">
        <div class="cluster cluster--between">
            <div>
                <p class="meta">
                    {{ $t(SUBMISSION_TYPES[submission.type].label) }} · {{ submission.id }} ·
                    {{ formatDate(submission.submittedAt) }}
                </p>
                <h2 class="display-sm">{{ submission.facilityName }}</h2>
            </div>
            <StatusPill kind="submission" :value="submission.status" />
        </div>

        <p class="body-copy">{{ submission.summary }}</p>

        <div class="kv">
            <div class="kv__row">
                <span class="kv__label">{{ $t('Kategori') }}</span>
                <span>{{ submission.category }}</span>
                <span class="kv__note">{{ submission.city }}</span>
            </div>

            <template v-if="isUsulan">
                <div class="kv__row">
                    <span class="kv__label">{{ $t('Alamat') }}</span>
                    <span>{{ submission.payload.address }}</span>
                    <span class="kv__note">
                        {{ submission.payload.lat }}, {{ submission.payload.lng }}
                    </span>
                </div>
                <div v-for="(value, key) in accessibility" :key="key" class="kv__row">
                    <span class="kv__label">{{ $t(attributeLabel(key)) }}</span>
                    <StatusPill kind="availability" :value="value" />
                    <span class="kv__note"><i18n-t keypath="{a} menurut pengusul." tag="span" scope="global"><template #a>{{ $t(AVAILABILITY[value].label) }}</template></i18n-t></span>
                </div>
                <div class="kv__row">
                    <span class="kv__label">{{ $t('Catatan') }}</span>
                    <span class="kv__note">{{ submission.payload.notes || $t('Tanpa catatan.') }}</span>
                    <span></span>
                </div>
            </template>

            <template v-else>
                <div class="kv__row">
                    <span class="kv__label">{{ $t('Atribut dikoreksi') }}</span>
                    <span>{{ $t(attributeLabel(submission.payload.field)) }}</span>
                    <span class="kv__note">
                        {{ $t(AVAILABILITY[submission.payload.from].label) }} →
                        {{ $t(AVAILABILITY[submission.payload.to].label) }}
                    </span>
                </div>
                <div class="kv__row">
                    <span class="kv__label">{{ $t('Catatan pelapor') }}</span>
                    <span class="kv__note">{{ submission.payload.notes || $t('Tanpa catatan.') }}</span>
                    <span></span>
                </div>
            </template>

            <div class="kv__row">
                <span class="kv__label">{{ $t('Pengaju') }}</span>
                <span>{{ submission.submittedBy }}</span>
                <span v-if="submission.decidedBy" class="kv__note">{{ $t('Diputuskan oleh') }} {{ submission.decidedBy }}
                    <template v-if="submission.decidedAt">· {{ formatDate(submission.decidedAt) }}</template>
                </span>
            </div>

            <div v-if="submission.createdFacilityId" class="kv__row">
                <span class="kv__label">{{ $t('Fasilitas dibuat') }}</span>
                <RouterLink :to="`/fasilitas/${submission.createdFacilityId}`">{{ submission.createdFacilityId }}</RouterLink>
                <span class="kv__note">{{ $t('Status verifikasi fasilitas tetap menunggu sampai diperiksa lapangan.') }}</span>
            </div>
        </div>

        <div>
            <h3 class="meta">{{ $t('Bukti pendukung') }}</h3>
            <ul v-if="submission.proofs.length" class="proof-list" style="margin-top: 12px">
                <li v-for="proof in submission.proofs" :key="proof.id" class="proof">
                    <img class="proof__thumb" :src="proof.url" :alt="$t('Bukti: {0}', [proof.caption])" loading="lazy">
                    <span>
                        <b style="display: block; font-size: 14px">{{ proof.caption }}</b>
                        <span class="meta">{{ formatDate(proof.date) }}</span>
                    </span>
                </li>
            </ul>
            <p v-else class="help">{{ $t('Belum ada bukti foto yang dilampirkan.') }}</p>
        </div>

        <ReviewActions
            :subject-id="submission.id"
            :actions="actions"
            :done-label="doneLabel"
            :busy="busy"
            :error="error"
            @decide="emit('decide', $event)"
        />
    </div>
</template>