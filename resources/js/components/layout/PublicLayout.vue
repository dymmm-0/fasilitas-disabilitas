<script setup>
import PublicHeader from './PublicHeader.vue';
import AppFooter from './AppFooter.vue';
</script>

<template>
    <a class="skip-link" href="#konten">{{ $t('Lewati ke konten utama') }}</a>

    <PublicHeader />

    <main id="konten" tabindex="-1">
        <RouterView />
    </main>

    <AppFooter />
</template>