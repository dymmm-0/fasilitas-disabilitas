<script setup>
import { computed } from 'vue';
import { AVAILABILITY, SUBMISSION_STATUSES, VERIFICATION } from '../../data/meta';

const props = defineProps({
    kind: { type: String, default: 'availability' },
    value: { type: String, required: true },
});

const SOURCES = { availability: AVAILABILITY, verification: VERIFICATION, submission: SUBMISSION_STATUSES };

const meta = computed(
    () => SOURCES[props.kind]?.[props.value] ?? { label: props.value, glyph: '—', pill: 'pill--unknown' },
);
</script>

<template>
    <span class="pill" :class="meta.pill">
        <span class="pill__glyph" aria-hidden="true">{{ meta.glyph }}</span>{{ $t(meta.label) }}
    </span>
</template>