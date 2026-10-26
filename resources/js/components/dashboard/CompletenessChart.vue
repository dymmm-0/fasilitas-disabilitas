<script setup>
import { computed } from 'vue';
import { categoryLabel } from '../../data/meta';
import { completeness } from '../../data/facilities';

const props = defineProps({
    facilities: { type: Array, required: true },
});

/** Rata-rata kelengkapan data per kategori, diurutkan dari yang paling rendah. */
const rows = computed(() => {
    const groups = new Map();

    props.facilities.forEach((facility) => {
        const group = groups.get(facility.category) ?? [];
        group.push(completeness(facility));
        groups.set(facility.category, group);
    });

    return [...groups.entries()]
        .map(([category, values]) => ({
            id: category,
            label: categoryLabel(category),
            count: values.length,
            average: Math.round(values.reduce((sum, value) => sum + value, 0) / values.length),
        }))
        .sort((a, b) => a.average - b.average);
});
</script>

<template>
    <div class="chart">
        <div v-for="row in rows" :key="row.id" class="bar-row">
            <span>
                {{ $t(row.label) }}
                <span class="meta"><i18n-t keypath="· {a} fasilitas" tag="span" scope="global"><template #a>{{ row.count }}</template></i18n-t></span>
            </span>
            <span
                class="meter__track"
                role="img"
                :aria-label="$t('Rata-rata kelengkapan data kategori {0}: {1} persen', [row.label, row.average])"
            >
                <span
                    class="meter__fill"
                    :class="{ 'meter__fill--low': row.average < 60 }"
                    :style="{ display: 'block', height: '100%', width: `${row.average}%` }"
                ></span>
            </span>
            <span class="mono">{{ row.average }}%</span>
        </div>
    </div>
</template>