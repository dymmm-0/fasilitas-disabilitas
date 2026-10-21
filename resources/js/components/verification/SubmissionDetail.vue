<script setup>
import { computed } from 'vue';
import { attributeLabel, AVAILABILITY, formatDate, SUBMISSION_TYPES } from '../../data/meta';
import StatusPill from '../common/StatusPill.vue';
import ReviewActions from './ReviewActions.vue';

const props = defineProps({
    submission: { type: Object, required: true },
});

const emit = defineEmits(['decide']);

const isUsulan = computed(() => props.submission.type === 'usulan');
const accessibility = computed(() => props.submission.payload.accessibility ?? {});
</script>

<template>
    <div class="panel stack">
        <div class="cluster cluster--between">
            <div>
                <p class="meta">
                    {{ SUBMISSION_TYPES[submission.type].label }} · {{ submission.id }} ·
                    {{ formatDate(submission.submittedAt) }}
                </p>
                <h2 class="display-sm">{{ submission.facilityName }}</h2>
            </div>
            <StatusPill kind="submission" :value="submission.status" />
        </div>

        <p class="body-copy">{{ submission.summary }}</p>

        <div class="kv">
            <div class="kv__row">
                <span class="kv__label">Kategori</span>
                <span>{{ submission.category }}</span>
                <span class="kv__note">{{ submission.city }}</span>
            </div>

            <template v-if="isUsulan">
                <div class="kv__row">
                    <span class="kv__label">Alamat</span>
                    <span>{{ submission.payload.address }}</span>
                    <span class="kv__note">
                        {{ submission.payload.lat }}, {{ submission.payload.lng }}
                    </span>
                </div>
                <div v-for="(value, key) in accessibility" :key="key" class="kv__row">
                    <span class="kv__label">{{ attributeLabel(key) }}</span>
                    <StatusPill kind="availability" :value="value" />
                    <span class="kv__note">{{ AVAILABILITY[value].label }} menurut pengusul.</span>
                </div>
                <div class="kv__row">
                    <span class="kv__label">Catatan</span>
                    <span class="kv__note">{{ submission.payload.notes || 'Tanpa catatan.' }}</span>
                    <span></span>
                </div>
            </template>

            <template v-else>
                <div class="kv__row">
                    <span class="kv__label">Atribut dikoreksi</span>
                    <span>{{ attributeLabel(submission.payload.field) }}</span>
                    <span class="kv__note">
                        {{ AVAILABILITY[submission.payload.from].label }} →
                        {{ AVAILABILITY[submission.payload.to].label }}
                    </span>
                </div>
                <div class="kv__row">
                    <span class="kv__label">Catatan pelapor</span>
                    <span class="kv__note">{{ submission.payload.notes || 'Tanpa catatan.' }}</span>
                    <span></span>
                </div>
            </template>

            <div class="kv__row">
                <span class="kv__label">Pengaju</span>
                <span>{{ submission.submittedBy }}</span>
                <span v-if="submission.decidedBy" class="kv__note">Diputuskan oleh {{ submission.decidedBy }}</span>
            </div>
        </div>

        <div>
            <h3 class="meta">Bukti pendukung</h3>
            <ul v-if="submission.proofs.length" class="proof-list" style="margin-top: 12px">
                <li v-for="proof in submission.proofs" :key="proof.caption" class="proof">
                    <span class="proof__thumb" aria-hidden="true">{{ proof.type === 'foto' ? 'FOTO' : 'CATATAN' }}</span>
                    <span>
                        <b style="display: block; font-size: 14px">{{ proof.caption }}</b>
                        <span class="meta">{{ formatDate(proof.date) }}</span>
                    </span>
                </li>
            </ul>
            <p v-else class="help">Belum ada bukti foto atau catatan yang dilampirkan.</p>
        </div>

        <ReviewActions :submission="submission" @decide="emit('decide', $event)" />
    </div>
</template>