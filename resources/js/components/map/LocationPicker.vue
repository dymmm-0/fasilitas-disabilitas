<script setup>
import { computed } from 'vue';
import FacilityMap from './FacilityMap.vue';

const props = defineProps({
    lat: { type: Number, required: true },
    lng: { type: Number, required: true },
});

const emit = defineEmits(['update:lat', 'update:lng']);

const center = computed(() => [props.lat, props.lng]);

const pick = ({ lat, lng }) => {
    emit('update:lat', Number(lat.toFixed(5)));
    emit('update:lng', Number(lng.toFixed(5)));
};
</script>

<template>
    <div class="stack--sm">
        <FacilityMap
            mode="pick"
            :facilities="[]"
            :center="center"
            :zoom="14"
            label="Peta pemilihan koordinat. Klik untuk menandai lokasi fasilitas."
            @pick="pick"
        />

        <div class="grid grid--form">
            <div class="field">
                <label for="koordinat-lat">Latitude</label>
                <input
                    id="koordinat-lat"
                    class="input"
                    type="number"
                    step="0.00001"
                    :value="lat"
                    @input="emit('update:lat', Number($event.target.value))"
                >
            </div>
            <div class="field">
                <label for="koordinat-lng">Longitude</label>
                <input
                    id="koordinat-lng"
                    class="input"
                    type="number"
                    step="0.00001"
                    :value="lng"
                    @input="emit('update:lng', Number($event.target.value))"
                >
            </div>
        </div>
        <p class="help">Klik pada peta atau isi koordinat secara manual.</p>
    </div>
</template>