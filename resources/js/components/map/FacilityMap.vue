<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { categoryGlyph, categoryLabel, VERIFICATION } from '../../data/meta';

const props = defineProps({
    facilities: { type: Array, required: true },
    selectedId: { type: String, default: null },
    mode: { type: String, default: 'view' },
    center: { type: Array, default: () => [-6.2, 106.82] },
    zoom: { type: Number, default: 11 },
    accuracy: { type: Number, default: null },
    label: { type: String, default: 'Peta fasilitas publik ramah disabilitas' },
});

const emit = defineEmits(['select', 'pick']);

const container = ref(null);
let map = null;
let layer = null;
let pickMarker = null;
const markers = new Map();
const markerElements = new Map();

const applyMarkerElement = (marker, facility) => {
    const element = marker.getElement();
    if (!element) return;

    element.setAttribute('tabindex', '0');
    element.setAttribute('role', 'button');
    element.setAttribute('aria-label', facilityLabel(facility));
    if (markerElements.get(facility.id) === element) return;

    element.addEventListener('focus', () => emit('select', facility.id));
    element.addEventListener('click', () => emit('select', facility.id));
    element.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            emit('select', facility.id);
        }
    });
    markerElements.set(facility.id, element);
};

const markerHandlers = (marker, facility) => {
    marker.on('add', () => applyMarkerElement(marker, facility));
};

const tooltipContent = (name) => {
    const content = document.createElement('span');
    content.textContent = name;
    return content;
};

const createMarker = (facility) => {
    const marker = L.marker([facility.lat, facility.lng], {
        icon: pinIcon(facility, facility.id === props.selectedId),
        title: facility.name,
        alt: facilityLabel(facility),
    });

    marker.bindTooltip(tooltipContent(facility.name), { direction: 'top', offset: [0, -14] });
    markerHandlers(marker, facility);
    marker.addTo(layer);
    markers.set(facility.id, marker);
};

const updateMarkers = (fit = true) => {
    if (!map || !layer) return;

    const facilityIds = new Set(props.facilities.map((facility) => facility.id));

    markers.forEach((marker, id) => {
        if (!facilityIds.has(id)) {
            marker.remove();
            markers.delete(id);
            markerElements.delete(id);
        }
    });

    props.facilities.forEach((facility) => {
        const marker = markers.get(facility.id);
        if (marker) {
            marker.setLatLng([facility.lat, facility.lng]);
            marker.setIcon(pinIcon(facility, facility.id === props.selectedId));
            marker.setTooltipContent(tooltipContent(facility.name));
            applyMarkerElement(marker, facility);
            return;
        }

        createMarker(facility);
    });

    if (fit && props.mode === 'view' && props.facilities.length > 1) {
        map.fitBounds(L.latLngBounds(props.facilities.map((f) => [f.lat, f.lng])), { padding: [40, 40] });
    }
};
let accuracyCircle = null;
let resizeObserver = null;
let resizeFrame = null;

const refreshMapSize = () => {
    if (!map || !container.value || container.value.clientWidth === 0 || container.value.clientHeight === 0) return;

    map.invalidateSize({ pan: false });
};

const scheduleMapRefresh = () => {
    if (resizeFrame !== null) cancelAnimationFrame(resizeFrame);

    resizeFrame = requestAnimationFrame(() => {
        resizeFrame = null;
        refreshMapSize();
    });
};

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

const facilityLabel = (facility) =>
    `${facility.name}. Kategori ${categoryLabel(facility.category)}. Status ${VERIFICATION[facility.verification].label}`;


/** Penanda awal pada mode pemilihan koordinat. */
const drawPickMarker = () => {
    if (!map) return;

    pickMarker?.remove();
    pickMarker = L.marker(props.center, {
        icon: pinIcon({ category: 'transportasi', verification: 'menunggu' }, true),
    }).addTo(map);

    accuracyCircle?.remove();
    accuracyCircle = Number.isFinite(props.accuracy) && props.accuracy > 0
        ? L.circle(props.center, {
            radius: props.accuracy,
            color: '#a8542a',
            fillColor: '#a8542a',
            fillOpacity: 0.12,
            weight: 1,
        }).addTo(map)
        : null;
};

const fitPickerView = () => {
    if (!map) return;

    if (accuracyCircle) {
        map.fitBounds(accuracyCircle.getBounds(), { padding: [40, 40], maxZoom: 17 });
        return;
    }

    map.setView(props.center, 16);
};

const drawPickerLocation = () => {
    drawPickMarker();
    fitPickerView();
};

onMounted(() => {
    map = L.map(container.value, { scrollWheelZoom: true, zoomControl: true }).setView(props.center, props.zoom);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    layer = L.layerGroup().addTo(map);

    if (props.mode === 'pick') {
        map.on('click', (event) => emit('pick', { lat: event.latlng.lat, lng: event.latlng.lng }));
        drawPickMarker();
    }

    updateMarkers();
    resizeObserver = new ResizeObserver(scheduleMapRefresh);
    resizeObserver.observe(container.value);
    nextTick(scheduleMapRefresh);
});

watch(() => props.facilities, () => updateMarkers(true), { deep: true });

const updateMarkerSelection = (id) => {
    if (!id) return;

    const facility = props.facilities.find((item) => item.id === id);
    const marker = markers.get(id);
    if (!facility || !marker) return;

    marker.setIcon(pinIcon(facility, id === props.selectedId));
    applyMarkerElement(marker, facility);
};

watch(
    () => props.selectedId,
    (selectedId, previousId) => {
        updateMarkerSelection(previousId);
        updateMarkerSelection(selectedId);
    },
);

// Marker sementara pada mode pemilihan koordinat.
watch(
    [() => props.center, () => props.accuracy],
    () => {
        if (props.mode !== 'pick' || !map) return;

        drawPickerLocation();
    },
    { deep: true },
);

onBeforeUnmount(() => {
    resizeObserver?.disconnect();
    resizeObserver = null;
    if (resizeFrame !== null) cancelAnimationFrame(resizeFrame);
    resizeFrame = null;
    accuracyCircle?.remove();
    accuracyCircle = null;
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