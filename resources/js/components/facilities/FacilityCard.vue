<script setup>
import { computed } from 'vue';
import { categoryGlyph, categoryLabel, formatDate, VERIFICATION } from '../../data/meta';

const props = defineProps({
    facility: { type: Object, required: true },
    compact: { type: Boolean, default: false },
    selected: { type: Boolean, default: false },
});

const emit = defineEmits(['select']);

const verified = computed(() => VERIFICATION[props.facility.verification]);
</script>

<template>
    <RouterLink
        class="card card--hover"
        :class="{ 'card--compact': compact }"
        :to="`/fasilitas/${facility.id}`"
        :aria-current="selected ? 'true' : undefined"
        :style="selected ? { borderColor: 'var(--ink)', transform: 'translateY(-6px)' } : null"
        @mouseenter="emit('select', facility.id)"
        @focus="emit('select', facility.id)"
        @click="emit('select', facility.id)"
    >
        <div class="card__media">
            <span class="card__badge">{{ categoryGlyph(facility.category) }} {{ facility.city }}</span>
        </div>

        <div class="card__body">
            <div class="card__meta">
                <span>{{ categoryLabel(facility.category) }}</span>
                <span>{{ facility.id }}</span>
            </div>

            <h3 class="card__title" :class="{ 'card__title--sm': compact }">{{ facility.name }}</h3>

            <p v-if="!compact" class="card__text">{{ facility.notes }}</p>

            <div class="card__foot">
                <span class="pill" :class="verified.pill">
                    <span class="pill__glyph" aria-hidden="true">{{ verified.glyph }}</span>{{ verified.label }}
                </span>
                <span class="meta">Diperbarui {{ formatDate(facility.updatedAt) }}</span>
            </div>
        </div>
    </RouterLink>
</template>