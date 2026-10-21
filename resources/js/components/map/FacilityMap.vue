<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { categoryGlyph, categoryLabel, VERIFICATION } from '../../data/meta';

const props = defineProps({
    facilities: { type: Array, required: true },
    selectedId: { type: String, default: null },
    mode: { type: String, default: 'view' },
    center: { type: Array, default: () => [-6.2, 106.82] },
    zoom: { type: Number, default: 11 },
    label: { type: String, default: 'Peta fasilitas publik ramah disabilitas' },
});

const emit = defineEmits(['select', 'pick']);

const container = ref(null);
let map = null;
let layer = null;
let pickMarker = null;

const pinIcon = (facility, selected) => {
    const classes = ['map-pin'];
    if (facility.verification === 'menunggu') classes.push('map-pin--pending');
    if (selected) classes.push('map-pin--selected');

    return L.divIcon({
        className: '',
        html: `<span class="${classes.join(' ')}" aria-hidden="true">${categoryGlyph(facility.category)}</span>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    });
};

const label = (facility) =>
    `${facility.name}. Kategori ${categoryLabel(facility.category)}. Status ${VERIFICATION[facility.verification].label}.`;

const render = (fit = true) => {
    if (!map) return;

    layer?.clearLayers();

    props.facilities.forEach((facility) => {
        const marker = L.marker([facility.lat, facility.lng], {
            icon: pinIcon(facility, facility.id === props.selectedId),
            title: facility.name,
            alt: label(facility),
        });

        marker.bindTooltip(facility.name, { direction: 'top', offset: [0, -14] });

        marker.on('add', () => {
            const element = marker.getElement();
            if (!element) return;

            element.setAttribute('tabindex', '0');
            element.setAttribute('role', 'button');
            element.setAttribute('aria-label', label(facility));
            element.addEventListener('focus', () => emit('select', facility.id));
            element.addEventListener('click', () => emit('select', facility.id));
            element.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    emit('select', facility.id);
                }
            });
        });

        marker.addTo(layer);
    });

    if (fit && props.mode === 'view' && props.facilities.length > 1) {
        map.fitBounds(L.latLngBounds(props.facilities.map((f) => [f.lat, f.lng])), { padding: [40, 40] });
    }
};

/** Penanda awal pada mode pemilihan koordinat. */
const drawPickMarker = () => {
    if (!map) return;

    pickMarker?.remove();
    pickMarker = L.marker(props.center, {
        icon: pinIcon({ category: 'transportasi', verification: 'menunggu' }, true),
    }).addTo(map);
};

onMounted(() => {
    map = L.map(container.value, { scrollWheelZoom: false, zoomControl: true }).setView(props.center, props.zoom);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    layer = L.layerGroup().addTo(map);

    if (props.mode === 'pick') {
        map.on('click', (event) => emit('pick', { lat: event.latlng.lat, lng: event.latlng.lng }));
        drawPickMarker();
    }

    render();
});

watch(() => props.facilities, () => render(true), { deep: true });

watch(
    () => props.selectedId,
    () => render(false),
);

// Marker sementara pada mode pemilihan koordinat.
watch(
    () => props.center,
    ([lat, lng]) => {
        if (props.mode !== 'pick' || !map) return;

        drawPickMarker();
        map.setView([lat, lng]);
    },
    { deep: true },
);

onBeforeUnmount(() => {
    map?.remove();
    map = null;
});
</script>

<template>
    <div
        ref="container"
        class="map"
        :class="{ 'map--picker': mode === 'pick' }"
        role="application"
        :aria-label="label"
    ></div>
</template>