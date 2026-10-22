<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';

const LINKS = [
    { to: '/', label: 'Peta', hideForAdmin: true },
    { to: '/kontribusi', label: 'Kontribusi' },
    { to: '/verifikasi', label: 'Verifikasi', admin: true },
    { to: '/admin', label: 'Admin', admin: true },
    { to: '/tentang', label: 'Tentang', hideForAdmin: true },
];

const { user, logout } = useAuth();
const router = useRouter();
const route = useRoute();

// Link admin hanya tampil setelah masuk; Peta & Tentang disembunyikan saat admin
// bekerja di dashboard. Kedua halaman itu tetap bisa dibuka lewat wordmark.
const links = computed(() =>
    LINKS.filter((link) => (!link.admin || user.value) && !(link.hideForAdmin && user.value)),
);

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
                <RouterLink v-for="link in links" :key="link.to" :to="link.to">{{ link.label }}</RouterLink>
            </nav>

            <div class="cluster" style="gap: 8px">
                <span v-if="user" class="meta desktop-only">{{ user.name }}</span>
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
                <RouterLink v-for="link in links" :key="link.to" :to="link.to">{{ link.label }}</RouterLink>
                <RouterLink v-if="!user" to="/masuk">Masuk</RouterLink>
                <button v-else type="button" class="btn btn--sm" @click="onLogout">Keluar ({{ user.name }})</button>
            </nav>
        </div>
    </header>
</template>