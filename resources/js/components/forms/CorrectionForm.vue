<script setup>
import { computed, reactive, ref } from 'vue';
import { t } from '../../i18n';
import { ATTRIBUTES, AVAILABILITY, attributeLabel, categoryLabel } from '../../data/meta';
import { useFacilities } from '../../composables/useFacilities';

const emit = defineEmits(['submit']);

const props = defineProps({
    initialFacilityId: { type: String, default: '' },
    busy: { type: Boolean, default: false },
    error: { type: String, default: '' },
});

const { facilities } = useFacilities();

const form = reactive({
    facilityId: props.initialFacilityId || facilities.value[0]?.id || '',
    field: ATTRIBUTES[0].id,
    to: 'yes',
    notes: '',
    submitter: '',
    email: '',
});

const photo = ref(null);
const errors = reactive({});

const selected = computed(() => facilities.value.find((f) => f.id === form.facilityId) ?? null);
const currentValue = computed(() => (selected.value ? selected.value.accessibility[form.field] : 'unknown'));

const onPhoto = (event) => {
    const file = event.target.files?.[0] ?? null;
    photo.value = file;
};

const validate = () => {
    Object.keys(errors).forEach((key) => delete errors[key]);

    if (!form.facilityId) errors.facilityId = t('Pilih fasilitas yang ingin dikoreksi.');
    if (!form.submitter.trim()) errors.submitter = t('Nama pelapor wajib diisi.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.email = t('Alamat surel tidak valid.');
    if (form.to === currentValue.value) errors.to = t('Pilih nilai yang berbeda dari data saat ini.');

    return Object.keys(errors).length === 0;
};

const submit = () => {
    if (props.busy || !validate()) return;

    emit('submit', {
        type: 'koreksi',
        facilityId: form.facilityId,
        facilityName: selected.value?.name ?? '',
        category: selected.value?.category ?? '',
        city: selected.value?.city ?? '',
        summary: t('Koreksi atribut {0}: {1} menjadi {2}.', [
            t(attributeLabel(form.field)),
            t(AVAILABILITY[currentValue.value].label),
            t(AVAILABILITY[form.to].label),
        ]),
        submittedBy: form.submitter.trim(),
        payload: {
            field: form.field,
            from: currentValue.value,
            to: form.to,
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
                <label for="koreksi-fasilitas">{{ $t('Fasilitas') }}</label>
                <select
                    id="koreksi-fasilitas"
                    v-model="form.facilityId"
                    class="select"
                    :aria-invalid="Boolean(errors.facilityId)"
                    :aria-describedby="errors.facilityId ? 'koreksi-fasilitas-error' : undefined"
                >
                    <option v-for="facility in facilities" :key="facility.id" :value="facility.id">
                        {{ facility.name }} — {{ facility.city }}
                    </option>
                </select>
                <p v-if="errors.facilityId" id="koreksi-fasilitas-error" class="error-text" role="alert">
                    {{ errors.facilityId }}
                </p>
            </div>

            <div class="field">
                <label for="koreksi-atribut">{{ $t('Atribut yang dikoreksi') }}</label>
                <select id="koreksi-atribut" v-model="form.field" class="select">
                    <option v-for="attribute in ATTRIBUTES" :key="attribute.id" :value="attribute.id">
                        {{ $t(attribute.label) }}
                    </option>
                </select>
            </div>

            <div class="field">
                <label for="koreksi-nilai">{{ $t('Kondisi yang benar') }}</label>
                <select
                    id="koreksi-nilai"
                    v-model="form.to"
                    class="select"
                    :aria-invalid="Boolean(errors.to)"
                    :aria-describedby="errors.to ? 'koreksi-nilai-error' : undefined"
                >
                    <option v-for="(meta, key) in AVAILABILITY" :key="key" :value="key">{{ $t(meta.label) }}</option>
                </select>
                <p v-if="errors.to" id="koreksi-nilai-error" class="error-text" role="alert">{{ errors.to }}</p>
            </div>
        </div>

        <p class="help" role="status">
            <span v-if="selected"><i18n-t keypath="Data saat ini untuk {a}{b}: {c}. Perubahan akan tercatat sebagai pengajuan koreksi." tag="span" scope="global"><template #a>{{ categoryLabel(selected.category) }}</template><template #b>{{ selected.name }}</template><template #c>{{ $t(AVAILABILITY[currentValue].label) }}</template></i18n-t></span>
        </p>

        <div class="grid grid--form">
            <div class="field">
                <label for="koreksi-catatan">{{ $t('Catatan koreksi') }}</label>
                <textarea id="koreksi-catatan" v-model="form.notes" class="textarea" :placeholder="$t('Apa yang berubah? Kapan diamati?')"></textarea>
            </div>

            <div class="field">
                <label for="koreksi-foto">{{ $t('Foto bukti (opsional)') }}</label>
                <input
                    id="koreksi-foto"
                    class="input"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    :disabled="busy"
                    @change="onPhoto"
                >
                <p class="help">{{ $t('JPG, PNG, atau WebP, maksimal 4 MB. Berkas disimpan di server dan hanya dibuka petugas verifikasi.') }}</p>
            </div>
        </div>

        <div class="grid grid--form">
            <div class="field">
                <label for="koreksi-pelapor">{{ $t('Nama pelapor') }}</label>
                <input
                    id="koreksi-pelapor"
                    v-model="form.submitter"
                    class="input"
                    :aria-invalid="Boolean(errors.submitter)"
                    :aria-describedby="errors.submitter ? 'koreksi-pelapor-error' : undefined"
                >
                <p v-if="errors.submitter" id="koreksi-pelapor-error" class="error-text" role="alert">{{ errors.submitter }}</p>
            </div>

            <div class="field">
                <label for="koreksi-email">{{ $t('Surel pelapor') }}</label>
                <input
                    id="koreksi-email"
                    v-model="form.email"
                    class="input"
                    type="email"
                    :aria-invalid="Boolean(errors.email)"
                    :aria-describedby="errors.email ? 'koreksi-email-error' : undefined"
                >
                <p v-if="errors.email" id="koreksi-email-error" class="error-text" role="alert">{{ errors.email }}</p>
            </div>
        </div>

        <p v-if="error" class="error-text" role="alert">{{ error }}</p>

        <div class="inline-actions">
            <button type="submit" class="btn btn--primary" :disabled="busy">
                {{ busy ? $t('Mengirim…') : $t('Kirim laporan koreksi') }}
            </button>
            <span class="meta">{{ $t('Laporan tersimpan di basis data dan masuk antrean verifikasi.') }}</span>
        </div>
    </form>
</template>