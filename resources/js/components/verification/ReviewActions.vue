<script setup>
import { computed, ref } from 'vue';

/**
 * Formulir catatan + tombol keputusan. Dipakai untuk pengajuan warga maupun
 * fasilitas: daftar tombol dan teks "sudah diputuskan" datang dari pemanggil.
 */
const props = defineProps({
    subjectId: { type: String, required: true },
    actions: { type: Array, required: true },
    busy: { type: Boolean, default: false },
    error: { type: String, default: '' },
    doneLabel: { type: String, default: '' },
});

const emit = defineEmits(['decide']);

const note = ref('');

const disabled = computed(() => props.busy || props.doneLabel !== '');

const decide = (action) => {
    if (disabled.value) return;

    emit('decide', { action, note: note.value.trim() });
};
</script>

<template>
    <form class="stack--sm" @submit.prevent>
        <div class="field">
            <label :for="`catatan-${subjectId}`">Catatan verifikasi (wajib untuk minta perbaikan atau tolak)</label>
            <textarea
                :id="`catatan-${subjectId}`"
                v-model="note"
                class="textarea"
                placeholder="Alasan keputusan atau hal yang perlu dilengkapi."
                :disabled="disabled"
            ></textarea>
        </div>

        <div class="inline-actions">
            <button
                v-for="action in actions"
                :key="action.id"
                type="button"
                :class="action.variant === 'primary' ? 'btn btn--primary' : 'btn'"
                :disabled="disabled"
                @click="decide(action.id)"
            >
                {{ action.label }}
            </button>
        </div>

        <p v-if="error" class="error-text" role="alert">{{ error }}</p>

        <p v-if="busy" class="help" role="status">Menyimpan keputusan...</p>
        <p v-else-if="doneLabel" class="help" role="status">{{ doneLabel }}</p>
        <p v-else class="help">
            Keputusan disimpan server bersama identitas petugas dan waktu keputusan.
        </p>
    </form>
</template>
