<script setup>
import { computed, ref, watch } from 'vue';
import FacilityCard from './FacilityCard.vue';

const props = defineProps({
    facilities: { type: Array, required: true },
    selectedId: { type: String, default: null },
    compact: { type: Boolean, default: true },
    emptyTitle: { type: String, default: 'Tidak ada fasilitas yang cocok' },
    emptyText: { type: String, default: 'Ubah kata kunci atau kosongkan filter untuk melihat semua fasilitas.' },
});

const emit = defineEmits(['select']);

// Dataset berisi ribuan fasilitas: kartu ditampilkan bertahap, bukan sekaligus.
const PAGE = 40;
const visible = ref(PAGE);
const shown = computed(() => props.facilities.slice(0, visible.value));

watch(() => props.facilities, () => {
    visible.value = PAGE;
});
</script>

<template>
    <ul v-if="shown.length" class="list-scroll list-scroll--facilities" style="list-style: none">
        <li v-for="facility in shown" :key="facility.id">
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

    <button v-if="facilities.length > shown.length" type="button" class="btn btn--sm" @click="visible += PAGE">
        Tampilkan {{ Math.min(PAGE, facilities.length - shown.length) }} lagi ({{ shown.length }} dari {{ facilities.length }})
    </button>
</template>