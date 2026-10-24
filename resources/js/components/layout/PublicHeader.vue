<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';

// Navigasi pengunjung. Menu internal (pengelolaan fasilitas, verifikasi, kategori)
// hanya ada di AdminHeader; tautan Dashboard muncul setelah petugas masuk.
const LINKS = [
    { to: '/', label: 'Beranda' },
    { to: '/kontribusi', label: 'Kontribusi' },
    { to: '/status', label: 'Status' },
    { to: '/tentang', label: 'Tentang' },
];

const { user, logout } = useAuth();
const router = useRouter();
const route = useRoute();

const open = ref(false);

watch(() => route.fullPath, () => (open.value = false));

const onKeydown = (event) => {
    if (event.key === 'Escape') open.value = false;
};

const onLogout = async () => {
    await logout();
    await router.push('/');
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
                <span v-if="user" class="meta desktop-only">{{ user.name }}</span>
                <RouterLink v-if="user" class="btn btn--sm desktop-only" to="/admin">Dashboard</RouterLink>
                <button v-if="user" type="button" class="btn btn--sm desktop-only" @click="onLogout">Keluar</button>
                <RouterLink v-else class="btn btn--primary btn--sm desktop-only" to="/masuk">Masuk</RouterLink>

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
                <RouterLink v-if="user" to="/admin">Dashboard</RouterLink>
                <RouterLink v-else to="/masuk">Masuk</RouterLink>
                <button v-if="user" type="button" class="btn btn--sm" @click="onLogout">Keluar ({{ user.name }})</button>
            </nav>
        </div>
    </header>
</template>