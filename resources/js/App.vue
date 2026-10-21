<script setup>
import AppHeader from './components/layout/AppHeader.vue';
import AppFooter from './components/layout/AppFooter.vue';
import ToastHost from './components/common/ToastHost.vue';
</script>

<template>
    <a class="skip-link" href="#konten">Lewati ke konten utama</a>

    <AppHeader />

    <main id="konten" tabindex="-1">
        <RouterView />
    </main>

    <AppFooter />

    <ToastHost />
</template>