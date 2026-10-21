import { ref } from 'vue';

/** Notifikasi ringan untuk konfirmasi aksi (pengajuan, keputusan verifikasi). */
const messages = ref([]);
let seq = 0;

const dismiss = (id) => {
    messages.value = messages.value.filter((m) => m.id !== id);
};

const push = (title, text, timeout = 6000) => {
    const id = ++seq;
    messages.value = [...messages.value, { id, title, text }];
    window.setTimeout(() => dismiss(id), timeout);
};

export function useToast() {
    return { messages, push, dismiss };
}