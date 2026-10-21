<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';

const LINKS = [
    { to: '/', label: 'Peta' },
    { to: '/kontribusi', label: 'Kontribusi' },
    { to: '/verifikasi', label: 'Verifikasi' },
    { to: '/admin', label: 'Admin' },
    { to: '/tentang', label: 'Tentang' },
];

const open = ref(false);
const route = useRoute();

watch(() => route.fullPath, () => (open.value = false));

const onKeydown = (event) => {
    if (event.key === 'Escape') open.value = false;
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <header class="site-header">
        <div class="container site-header__row">
            <RouterLink class="wordmark" to="/" aria-label="AksesKita, ke beranda">Akses<em>Kita</em></RouterLink>

            <nav class="site-nav" aria-label="Navigasi utama">
                <RouterLink v-for="link in LINKS" :key="link.to" :to="link.to">{{ link.label }}</RouterLink>
            </nav>

            <div class="cluster" style="gap: 8px">
                <RouterLink class="btn btn--primary btn--sm desktop-only" to="/kontribusi">Usulkan fasilitas</RouterLink>
                <button
                    type="button"
                    class="icon-btn menu-toggle"
                    :aria-expanded="open"
                    aria-controls="navigasi-seluler"
                    @click="open = !open"
                >
                    <span aria-hidden="true">{{ open ? '×' : '≡' }}</span>
                    <span class="visually-hidden">{{ open ? 'Tutup menu' : 'Buka menu' }}</span>
                </button>
            </div>
        </div>

        <div v-if="open" id="navigasi-seluler" class="container mobile-only">
            <nav class="mobile-nav" aria-label="Navigasi seluler">
                <RouterLink v-for="link in LINKS" :key="link.to" :to="link.to">{{ link.label }}</RouterLink>
            </nav>
        </div>
    </header>
</template>