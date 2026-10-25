<script setup>
import FacilityCard from './FacilityCard.vue';

const props = defineProps({
    facilities: { type: Array, required: true },
    selectedId: { type: String, default: null },
    compact: { type: Boolean, default: true },
    emptyTitle: { type: String, default: 'Tidak ada fasilitas yang cocok' },
    emptyText: { type: String, default: 'Ubah kata kunci atau kosongkan filter untuk melihat semua fasilitas.' },
});

const emit = defineEmits(['select']);
</script>

<template>
    <ul v-if="facilities.length" class="list-scroll list-scroll--facilities" style="list-style: none">
        <li v-for="facility in facilities" :key="facility.id">
            <FacilityCard
                :facility="facility"
                :compact="compact"
                :selected="facility.id === selectedId"
                @select="emit('select', $event)"
            />
        </li>
    </ul>

    <div v-else class="empty-state" role="status">
        <span class="label">Hasil kosong</span>
        <p class="card__title card__title--sm">{{ emptyTitle }}</p>
        <p class="card__text">{{ emptyText }}</p>
    </div>

</template>