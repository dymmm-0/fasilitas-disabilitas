<script setup>
import { computed, ref } from 'vue';
import { SUBMISSION_STATUSES } from '../../data/meta';

const props = defineProps({
    submission: { type: Object, required: true },
});

const emit = defineEmits(['decide']);

const note = ref('');

const decided = computed(() => ['disetujui', 'ditolak'].includes(props.submission.status));

const decide = (action) => {
    emit('decide', { id: props.submission.id, action, note: note.value.trim() });
    note.value = '';
};
</script>

<template>
    <form class="stack--sm" @submit.prevent>
        <div class="field">
            <label :for="`catatan-${submission.id}`">Catatan verifikasi (opsional)</label>
            <textarea
                :id="`catatan-${submission.id}`"
                v-model="note"
                class="textarea"
                placeholder="Alasan keputusan atau hal yang perlu dilengkapi."
                :disabled="decided"
            ></textarea>
        </div>

        <div class="inline-actions">
            <button type="button" class="btn btn--primary" :disabled="decided" @click="decide('disetujui')">
                ✓ Setujui
            </button>
            <button type="button" class="btn" :disabled="decided" @click="decide('perbaikan')">△ Minta perbaikan</button>
            <button type="button" class="btn" :disabled="decided" @click="decide('ditolak')">✕ Tolak</button>
        </div>

        <p v-if="decided" class="help" role="status">
            Keputusan sudah dibuat: {{ SUBMISSION_STATUSES[submission.status].label }}. Riwayat tercatat di bawah.
        </p>
        <p v-else class="help">Simulasi: keputusan tidak tersimpan ke basis data, hanya mengubah state demo.</p>
    </form>
</template>