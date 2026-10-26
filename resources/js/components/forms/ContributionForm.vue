<script setup>
import { reactive, ref } from 'vue';
import { t } from '../../i18n';
import AccessibilityChecklist from './AccessibilityChecklist.vue';
import LocationPicker from '../map/LocationPicker.vue';
import { ATTRIBUTES, CATEGORIES } from '../../data/meta';

const emit = defineEmits(['submit']);

const props = defineProps({
    busy: { type: Boolean, default: false },
    error: { type: String, default: '' },
});

const form = reactive({
    name: '',
    category: 'transportasi',
    address: '',
    city: '',
    lat: 3.595,
    lng: 98.672,
    notes: '',
    accessibility: Object.fromEntries(ATTRIBUTES.map((a) => [a.id, 'unknown'])),
    submitter: '',
    email: '',
});

const photo = ref(null);
const preview = ref(null);
const errors = reactive({});

const onPhoto = (event) => {
    const file = event.target.files?.[0] ?? null;
    if (preview.value) URL.revokeObjectURL(preview.value);
    photo.value = file;
    preview.value = file ? URL.createObjectURL(file) : null;
};

const validate = () => {
    Object.keys(errors).forEach((key) => delete errors[key]);

    if (!form.name.trim()) errors.name = t('Nama fasilitas wajib diisi.');
    if (!form.address.trim()) errors.address = t('Alamat wajib diisi.');
    if (!form.city.trim()) errors.city = t('Kota wajib diisi.');
    if (!form.submitter.trim()) errors.submitter = t('Nama pengusul wajib diisi.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.email = t('Alamat surel tidak valid.');
    if (
        !Number.isFinite(form.lat)
        || !Number.isFinite(form.lng)
        || form.lat < -90
        || form.lat > 90
        || form.lng < -180
        || form.lng > 180
    ) {
        errors.lat = t('Latitude harus -90 sampai 90 dan longitude -180 sampai 180.');
    }

    return Object.keys(errors).length === 0;
};

const submit = () => {
    if (props.busy || !validate()) return;

    emit('submit', {
        type: 'usulan',
        facilityName: form.name.trim(),
        category: form.category,
        city: form.city.trim(),
        summary: t('Usulan fasilitas {0} di {1}.', [form.name.trim(), form.city.trim()]),
        submittedBy: form.submitter.trim(),
        payload: {
            address: form.address.trim(),
            lat: form.lat,
            lng: form.lng,
            accessibility: { ...form.accessibility },
            notes: form.notes.trim(),
        },
        proof: photo.value,
        contact: form.email.trim(),
    });
};
</script>

<template>
    <form class="stack" novalidate @submit.prevent="submit">
        <div class="grid grid--form">
            <div class="field">
                <label for="usulan-nama">{{ $t('Nama fasilitas') }}</label>
                <input
                    id="usulan-nama"
                    v-model="form.name"
                    class="input"
                    :aria-invalid="Boolean(errors.name)"
                    :aria-describedby="errors.name ? 'usulan-nama-error' : undefined"
                >
                <p v-if="errors.name" id="usulan-nama-error" class="error-text" role="alert">{{ errors.name }}</p>
            </div>

            <div class="field">
                <label for="usulan-kategori">{{ $t('Kategori') }}</label>
                <select id="usulan-kategori" v-model="form.category" class="select">
                    <option v-for="category in CATEGORIES" :key="category.id" :value="category.id">{{ $t(category.label) }}</option>
                </select>
            </div>

            <div class="field">
                <label for="usulan-kota">{{ $t('Kota / kabupaten') }}</label>
                <input
                    id="usulan-kota"
                    v-model="form.city"
                    class="input"
                    :aria-invalid="Boolean(errors.city)"
                    :aria-describedby="errors.city ? 'usulan-kota-error' : undefined"
                >
                <p v-if="errors.city" id="usulan-kota-error" class="error-text" role="alert">{{ errors.city }}</p>
            </div>
        </div>

        <div class="field">
            <label for="usulan-alamat">{{ $t('Alamat lengkap') }}</label>
            <textarea
                id="usulan-alamat"
                v-model="form.address"
                class="textarea"
                :aria-invalid="Boolean(errors.address)"
                :aria-describedby="errors.address ? 'usulan-alamat-error' : undefined"
            ></textarea>
            <p v-if="errors.address" id="usulan-alamat-error" class="error-text" role="alert">{{ errors.address }}</p>
        </div>

        <fieldset>
            <legend class="fieldset__legend">{{ $t('Koordinat lokasi') }}</legend>
            <LocationPicker
                v-model:lat="form.lat"
                v-model:lng="form.lng"
                :coordinate-error="errors.lat"
            />
            <p v-if="errors.lat" id="koordinat-error" class="error-text" role="alert">{{ errors.lat }}</p>
        </fieldset>

        <fieldset>
            <legend class="fieldset__legend">{{ $t('Checklist aksesibilitas') }}</legend>
            <AccessibilityChecklist v-model="form.accessibility" />
        </fieldset>

        <div class="grid grid--form">
            <div class="field">
                <label for="usulan-catatan">{{ $t('Catatan tambahan') }}</label>
                <textarea id="usulan-catatan" v-model="form.notes" class="textarea"></textarea>
            </div>

            <div class="stack--sm">
                <div class="field">
                    <label for="usulan-foto">{{ $t('Foto bukti (opsional)') }}</label>
                    <input
                        id="usulan-foto"
                        class="input"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        :disabled="busy"
                        @change="onPhoto"
                    >
                    <p class="help">{{ $t('JPG, PNG, atau WebP, maksimal 4 MB. Berkas disimpan di server dan hanya dibuka petugas verifikasi.') }}</p>
                </div>

                <div
                    v-if="preview"
                    class="detail-media"
                    :style="{ backgroundImage: `url(${preview})`, backgroundSize: 'cover', color: 'transparent' }"
                    role="img"
                    :aria-label="$t('Pratinjau foto yang dipilih')"
                ></div>
            </div>
        </div>

        <div class="grid grid--form">
            <div class="field">
                <label for="usulan-pengusul">{{ $t('Nama pengusul') }}</label>
                <input
                    id="usulan-pengusul"
                    v-model="form.submitter"
                    class="input"
                    :aria-invalid="Boolean(errors.submitter)"
                    :aria-describedby="errors.submitter ? 'usulan-pengusul-error' : undefined"
                >
                <p v-if="errors.submitter" id="usulan-pengusul-error" class="error-text" role="alert">{{ errors.submitter }}</p>
            </div>

            <div class="field">
                <label for="usulan-email">{{ $t('Surel pengusul') }}</label>
                <input
                    id="usulan-email"
                    v-model="form.email"
                    class="input"
                    type="email"
                    :aria-invalid="Boolean(errors.email)"
                    :aria-describedby="errors.email ? 'usulan-email-error' : undefined"
                >
                <p v-if="errors.email" id="usulan-email-error" class="error-text" role="alert">{{ errors.email }}</p>
            </div>
        </div>

        <p v-if="error" class="error-text" role="alert">{{ error }}</p>

        <div class="inline-actions">
            <button type="submit" class="btn btn--primary" :disabled="busy">
                {{ busy ? $t('Mengirim…') : $t('Kirim usulan') }}
            </button>
            <span class="meta">{{ $t('Pengajuan tersimpan di basis data dan masuk antrean verifikasi.') }}</span>
        </div>
    </form>
</template>