<script setup>
import { formatDate, SUBMISSION_STATUSES, SUBMISSION_TYPES } from '../../data/meta';

defineProps({
    submissions: { type: Array, required: true },
    selectedId: { type: String, default: null },
});

const emit = defineEmits(['select']);
</script>

<template>
    <ul v-if="submissions.length" class="list-scroll" style="list-style: none">
        <li v-for="submission in submissions" :key="submission.id">
            <button
                type="button"
                class="card card--hover"
                :style="
                    submission.id === selectedId
                        ? { borderColor: 'var(--ink)', transform: 'translateY(-6px)' }
                        : null
                "
                :aria-current="submission.id === selectedId ? 'true' : undefined"
                @click="emit('select', submission.id)"
            >
                <div class="card__body">
                    <div class="card__meta">
                        <span>
                            <span aria-hidden="true">{{ SUBMISSION_TYPES[submission.type].glyph }}</span>
                            {{ SUBMISSION_TYPES[submission.type].label }}
                        </span>
                        <span>{{ submission.id }}</span>
                    </div>

                    <h3 class="card__title card__title--sm">{{ submission.facilityName }}</h3>

                    <p class="card__text">{{ submission.summary }}</p>

                    <div class="card__foot">
                        <span class="pill" :class="SUBMISSION_STATUSES[submission.status].pill">
                            <span class="pill__glyph" aria-hidden="true">{{ SUBMISSION_STATUSES[submission.status].glyph }}</span>
                            {{ SUBMISSION_STATUSES[submission.status].label }}
                        </span>
                        <span class="meta">{{ submission.submittedBy }} · {{ formatDate(submission.submittedAt) }}</span>
                    </div>
                </div>
            </button>
        </li>
    </ul>

    <div v-else class="empty-state" role="status">
        <span class="label">Antrean kosong</span>
        <p class="card__text">Tidak ada pengajuan yang cocok dengan filter saat ini.</p>
    </div>
</template>