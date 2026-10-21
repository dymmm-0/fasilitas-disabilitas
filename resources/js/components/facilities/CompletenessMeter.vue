<script setup>
import { computed } from 'vue';
import { ATTRIBUTES } from '../../data/meta';
import { completeness, knownAttributeCount } from '../../data/facilities';

const props = defineProps({
    facility: { type: Object, required: true },
});

const percent = computed(() => completeness(props.facility));
const known = computed(() => knownAttributeCount(props.facility));
const low = computed(() => percent.value < 60);
</script>

<template>
    <div class="meter">
        <div class="meter__track" role="img" :aria-label="`Kelengkapan data ${percent} persen`">
            <div class="meter__fill" :class="{ 'meter__fill--low': low }" :style="{ width: `${percent}%` }"></div>
        </div>
        <div class="meter__legend">
            <span>
                <span aria-hidden="true">{{ low ? '△' : '✓' }}</span>
                {{ low ? 'Data belum lengkap' : 'Data cukup lengkap' }}
            </span>
            <span>{{ known }}/{{ ATTRIBUTES.length }} atribut · {{ percent }}%</span>
        </div>
    </div>
</template>