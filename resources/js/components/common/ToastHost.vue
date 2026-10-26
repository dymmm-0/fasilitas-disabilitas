<script setup>
import { useToast } from '../../composables/useToast';

const { messages, dismiss } = useToast();
</script>

<template>
    <div class="toast-host" role="status" aria-live="polite">
        <div v-for="message in messages" :key="message.id" class="toast">
            <b>{{ message.title }}</b>
            <span>{{ message.text }}</span>
            <button type="button" class="toast__close" @click="dismiss(message.id)">{{ $t('Tutup pemberitahuan') }}</button>
        </div>
    </div>
</template>