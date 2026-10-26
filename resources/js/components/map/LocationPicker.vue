<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { t } from '../../i18n';
import FacilityMap from './FacilityMap.vue';

const ACCURACY_WARNING_METERS = 100;
const DEFAULT_CENTER = [-6.1944, 106.8229];

const props = defineProps({
    lat: { type: Number, required: true },
    lng: { type: Number, required: true },
    coordinateError: { type: String, default: '' },
});

const emit = defineEmits(['update:lat', 'update:lng']);

const locating = ref(false);
const locationError = ref('');
const locationStatus = ref('');
const accuracy = ref(null);
let mounted = true;

const center = computed(() => [
    Number.isFinite(props.lat) ? props.lat : DEFAULT_CENTER[0],
    Number.isFinite(props.lng) ? props.lng : DEFAULT_CENTER[1],
]);

const pick = ({ lat, lng }, keepAccuracy = false) => {
    if (!keepAccuracy) accuracy.value = null;
    emit('update:lat', Number(lat.toFixed(5)));
    emit('update:lng', Number(lng.toFixed(5)));
};

const updateManualCoordinate = (key, value) => {
    accuracy.value = null;
    emit(`update:${key}`, value.trim() === '' ? NaN : Number(value));
};

const locationErrorMessage = (error) => {
    if (error.code === 1) {
        return t('Izin lokasi ditolak. Aktifkan izin lokasi untuk situs ini melalui pengaturan browser jika ingin mencoba lagi.');
    }

    if (error.code === 2) {
        return t('Lokasi tidak dapat ditemukan. Periksa GPS, sinyal, atau izin lokasi perangkat.');
    }

    if (error.code === 3) {
        return t('Waktu pencarian lokasi habis. Coba lagi atau pilih titik secara manual di peta.');
    }

    return t('Lokasi tidak dapat diambil. Pilih titik secara manual di peta atau coba lagi.');
};

const useMyLocation = () => {
    locationError.value = '';
    locationStatus.value = '';

    if (!window.isSecureContext) {
        locationError.value = t('Pengambilan lokasi membutuhkan koneksi aman (HTTPS atau localhost). Pilih titik secara manual di peta.');
        return;
    }

    if (!('geolocation' in navigator)) {
        locationError.value = t('Browser ini tidak mendukung pengambilan lokasi. Pilih titik secara manual di peta.');
        return;
    }

    locating.value = true;
    navigator.geolocation.getCurrentPosition(
        (position) => {
            if (!mounted) return;

            const { latitude, longitude, accuracy: accuracyMeters } = position.coords;
            if (
                !Number.isFinite(latitude)
                || !Number.isFinite(longitude)
                || latitude < -90
                || latitude > 90
                || longitude < -180
                || longitude > 180
            ) {
                locating.value = false;
                locationError.value = t('Koordinat lokasi tidak valid. Pilih titik secara manual di peta.');
                return;
            }

            accuracy.value = Number.isFinite(accuracyMeters) && accuracyMeters > 0 ? accuracyMeters : null;
            pick({ lat: latitude, lng: longitude }, true);
            locating.value = false;
            locationStatus.value = Number.isFinite(accuracyMeters) && accuracyMeters > ACCURACY_WARNING_METERS
                ? t('Lokasi ditemukan. Akurasi sekitar {0} meter; pastikan titik fasilitas sudah benar.', [Math.round(accuracyMeters)])
                : t('Lokasi ditemukan. Pastikan titik ini benar-benar lokasi fasilitas yang dilaporkan.');
        },
        (error) => {
            if (!mounted) return;

            locating.value = false;
            locationError.value = locationErrorMessage(error);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 },
    );
};

onBeforeUnmount(() => {
    mounted = false;
});
</script>

<template>
    <div class="stack--sm">
        <FacilityMap
            mode="pick"
            :facilities="[]"
            :center="center"
            :zoom="14"
            :accuracy="accuracy"
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
                    min="-90"
                    max="90"
                    step="0.00001"
                    :aria-invalid="Boolean(coordinateError)"
                    :aria-describedby="coordinateError ? 'koordinat-error' : 'koordinat-help'"
                    :value="Number.isFinite(lat) ? lat : ''"
                    @input="updateManualCoordinate('lat', $event.target.value)"
                >
            </div>
            <div class="field">
                <label for="koordinat-lng">Longitude</label>
                <input
                    id="koordinat-lng"
                    class="input"
                    type="number"
                    min="-180"
                    max="180"
                    step="0.00001"
                    :aria-invalid="Boolean(coordinateError)"
                    :aria-describedby="coordinateError ? 'koordinat-error' : 'koordinat-help'"
                    :value="Number.isFinite(lng) ? lng : ''"
                    @input="updateManualCoordinate('lng', $event.target.value)"
                >
            </div>
        </div>

        <div class="inline-actions">
            <button
                type="button"
                class="btn"
                :disabled="locating"
                :aria-busy="locating"
                @click="useMyLocation"
            >
                {{ locating ? $t('Mencari lokasi…') : $t('Gunakan Lokasi Saya') }}
            </button>
            <span class="help">{{ $t('Lokasi hanya diambil setelah tombol ditekan dan dikirim saat formulir dikirim.') }}</span>
        </div>
        <p v-if="locationStatus" class="help" role="status" aria-live="polite">{{ locationStatus }}</p>
        <p v-if="locationError" class="error-text" role="alert">{{ locationError }}</p>
        <p id="koordinat-help" class="help">{{ $t('Klik pada peta atau isi koordinat secara manual. Koordinat dikirim hanya sebagai lokasi fasilitas saat formulir dikirim.') }}</p>
    </div>
</template>