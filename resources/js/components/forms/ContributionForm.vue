<script setup>
import { reactive, ref } from 'vue';
import AccessibilityChecklist from './AccessibilityChecklist.vue';
import LocationPicker from '../map/LocationPicker.vue';
import { ATTRIBUTES, CATEGORIES } from '../../data/meta';

const emit = defineEmits(['submit']);

const form = reactive({
    name: '',
    category: 'transportasi',
    address: '',
    city: '',
    lat: -6.1944,
    lng: 106.8229,
    notes: '',
    accessibility: Object.fromEntries(ATTRIBUTES.map((a) => [a.id, 'belum'])),
    submitter: '',
    email: '',
});

const photo = ref(null);
const preview = ref(null);
const errors = reactive({});

const onPhoto = (event) => {
    const file = event.target.files?.[0] ?? null;
    if (preview.value) URL.revokeObjectURL(preview.value);
    photo.value = file ? file.name : null;
    preview.value = file ? URL.createObjectURL(file) : null;
};

const validate = () => {
    Object.keys(errors).forEach((key) => delete errors[key]);

    if (!form.name.trim()) errors.name = 'Nama fasilitas wajib diisi.';
    if (!form.address.trim()) errors.address = 'Alamat wajib diisi.';
    if (!form.city.trim()) errors.city = 'Kota wajib diisi.';
    if (!form.submitter.trim()) errors.submitter = 'Nama pengusul wajib diisi.';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.email = 'Alamat surel tidak valid.';
    if (!Number.isFinite(form.lat) || !Number.isFinite(form.lng)) errors.lat = 'Koordinat wajib diisi.';

    return Object.keys(errors).length === 0;
};

const submit = () => {
    if (!validate()) return;

    emit('submit', {
        type: 'usulan',
        facilityName: form.name.trim(),
        category: form.category,
        city: form.city.trim(),
        summary: `Usulan fasilitas ${form.name.trim()} di ${form.city.trim()}.`,
        submittedBy: form.submitter.trim(),
        payload: {
            address: form.address.trim(),
            lat: form.lat,
            lng: form.lng,
            accessibility: { ...form.accessibility },
            notes: form.notes.trim(),
        },
        proofs: photo.value ? [{ type: 'foto', caption: photo.value, date: new Date().toISOString().slice(0, 10) }] : [],
        contact: form.email.trim(),
    });
};
</script>

<template>
    <form class="stack" novalidate @submit.prevent="submit">
        <div class="grid grid--form">
            <div class="field">
                <label for="usulan-nama">Nama fasilitas</label>
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
                <label for="usulan-kategori">Kategori</label>
                <select id="usulan-kategori" v-model="form.category" class="select">
                    <option v-for="category in CATEGORIES" :key="category.id" :value="category.id">{{ category.label }}</option>
                </select>
            </div>

            <div class="field">
                <label for="usulan-kota">Kota / kabupaten</label>
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
            <label for="usulan-alamat">Alamat lengkap</label>
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
            <legend class="fieldset__legend">Koordinat lokasi</legend>
            <LocationPicker v-model:lat="form.lat" v-model:lng="form.lng" />
            <p v-if="errors.lat" class="error-text" role="alert">{{ errors.lat }}</p>
        </fieldset>

        <fieldset>
            <legend class="fieldset__legend">Checklist aksesibilitas</legend>
            <AccessibilityChecklist v-model="form.accessibility" />
        </fieldset>

        <div class="grid grid--form">
            <div class="field">
                <label for="usulan-catatan">Catatan tambahan</label>
                <textarea id="usulan-catatan" v-model="form.notes" class="textarea"></textarea>
            </div>

            <div class="stack--sm">
                <div class="field">
                    <label for="usulan-foto">Foto bukti (opsional)</label>
                    <input id="usulan-foto" class="input" type="file" accept="image/*" @change="onPhoto">
                    <p class="help">Demonstrasi: berkas tidak diunggah ke server.</p>
                </div>

                <div
                    v-if="preview"
                    class="detail-media"
                    :style="{ backgroundImage: `url(${preview})`, backgroundSize: 'cover', color: 'transparent' }"
                    role="img"
                    aria-label="Pratinjau foto yang dipilih"
                ></div>
            </div>
        </div>

        <div class="grid grid--form">
            <div class="field">
                <label for="usulan-pengusul">Nama pengusul</label>
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
                <label for="usulan-email">Surel pengusul</label>
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

        <div class="inline-actions">
            <button type="submit" class="btn btn--primary">Kirim usulan</button>
            <span class="meta">Simulasi: pengajuan masuk antrean verifikasi, tidak tersimpan ke basis data.</span>
        </div>
    </form>
</template>