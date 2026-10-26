<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../../composables/useAuth';
import LanguageSwitch from '../common/LanguageSwitch.vue';

// Navigasi area admin. "Fasilitas" dan "Kategori" adalah bagian di dalam
// dashboard, jadi tautannya berupa lompatan bagian halaman (`active-class=""`
// agar tidak berebut status aktif dengan tautan Dashboard).
const LINKS = [
    { to: '/admin', label: 'Dashboard' },
    { to: '/admin#pengelolaan-fasilitas', label: 'Fasilitas', jump: true },
    { to: '/verifikasi', label: 'Verifikasi' },
    { to: '/admin#kategori', label: 'Kategori', jump: true },
];

// Situs publik dibuka di tab baru: sesi admin tidak tersentuh, dashboard tetap
// hidup di tab asal.
const PUBLIC_SITE = '/';

const { user, logout } = useAuth();
const router = useRouter();
const route = useRoute();

const open = ref(false);

// Tautan lompatan bagian dashboard; status aktif hanya dipunyai tautan Dashboard
// supaya tidak ada dua penanda "halaman saat ini".
const go = (to) => {
    open.value = false;

    return route.fullPath === to ? undefined : router.push(to);
};

watch(() => route.fullPath, () => (open.value = false));

const onKeydown = (event) => {
    if (event.key === 'Escape') open.value = false;
};

const onLogout = async () => {
    await logout();
    await router.push('/masuk');
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <header class="site-header">
        <div class="container site-header__row">
            <div class="cluster" style="gap: 10px">
                <RouterLink class="wordmark" to="/admin" :aria-label="$t('AksesKita admin, ke dashboard')">Akses<em>Kita</em></RouterLink>
                <span class="pill pill--accent">Admin</span>
            </div>

            <nav class="site-nav" :aria-label="$t('Navigasi administrasi')">
                <template v-for="link in LINKS" :key="link.to">
                    <a v-if="link.jump" :href="link.to" @click.prevent="go(link.to)">{{ $t(link.label) }}</a>
                    <RouterLink v-else :to="link.to">{{ $t(link.label) }}</RouterLink>
                </template>
            </nav>

            <div class="cluster" style="gap: 8px">
                <span v-if="user" class="meta desktop-only">{{ user.name }}</span>
                <a class="btn btn--sm desktop-only" :href="PUBLIC_SITE" target="_blank" rel="noopener noreferrer">{{ $t('Kunjungi Situs') }}<span aria-hidden="true">↗</span>
                </a>
                <button v-if="user" type="button" class="btn btn--sm desktop-only" @click="onLogout">{{ $t('Keluar') }}</button>

                <LanguageSwitch />

                <button
                    type="button"
                    class="icon-btn menu-toggle"
                    :aria-expanded="open"
                    aria-controls="navigasi-seluler"
                    @click="open = !open"
                >
                    <span aria-hidden="true">{{ open ? '×' : '≡' }}</span>
                    <span class="visually-hidden">{{ open ? $t('Tutup menu') : $t('Buka menu') }}</span>
                </button>
            </div>
        </div>

        <div v-if="open" id="navigasi-seluler" class="container mobile-only">
            <nav class="mobile-nav" :aria-label="$t('Navigasi administrasi seluler')">
                <template v-for="link in LINKS" :key="link.to">
                    <a v-if="link.jump" :href="link.to" @click.prevent="go(link.to)">{{ $t(link.label) }}</a>
                    <RouterLink v-else :to="link.to">{{ $t(link.label) }}</RouterLink>
                </template>
                <a :href="PUBLIC_SITE" target="_blank" rel="noopener noreferrer">{{ $t('Kunjungi Situs ↗') }}</a>
                <button v-if="user" type="button" class="btn btn--sm" @click="onLogout"><i18n-t keypath="Keluar ({a})" tag="span" scope="global"><template #a>{{ user.name }}</template></i18n-t></button>
            </nav>
        </div>
    </header>
</template>