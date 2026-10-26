<script setup>
import AdminHeader from './AdminHeader.vue';
</script>

<template>
    <a class="skip-link" href="#konten">{{ $t('Lewati ke konten utama') }}</a>

    <AdminHeader />

    <main id="konten" tabindex="-1">
        <RouterView />
    </main>

    <!-- Area admin tidak memakai footer publik: navigasinya sudah ada di header. -->
    <footer class="site-footer">
        <div class="container site-footer__legal" style="margin-top: 0; border-top: 0">
            <span>{{ $t('Ruang kerja admin AksesKita') }}</span>
            <span>{{ $t('Keputusan dan perubahan tersimpan di basis data aplikasi') }}</span>
        </div>
    </footer>
</template>