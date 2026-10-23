<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AdminLayout from './components/layout/AdminLayout.vue';
import PublicLayout from './components/layout/PublicLayout.vue';
import ToastHost from './components/common/ToastHost.vue';

// Pemisahan area ditentukan route (`meta.layout`), bukan deteksi tab/storage.
const route = useRoute();
const layout = computed(() => (route.meta.layout === 'admin' ? AdminLayout : PublicLayout));
</script>

<template>
    <component :is="layout" />

    <ToastHost />
</template>