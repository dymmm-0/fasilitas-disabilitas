<script setup>
import { useAuth } from '../../composables/useAuth';

// Tautan dashboard hanya untuk petugas yang sudah masuk; pengunjung biasa tidak
// diberi menu internal.
const { user } = useAuth();
</script>

<template>
    <footer class="site-footer">
        <div class="container grid">
            <div>
<span class="wordmark" style="font-size: 32px; display: block; margin-bottom: 12px">Akses<em>Kita</em></span>
                <p style="line-height: 1.55; max-width: 320px; color: var(--body)">{{ $t('Platform pemetaan dan verifikasi fasilitas publik ramah disabilitas, dikelola bersama masyarakat.') }}</p>
            </div>

            <div>
                <h2>{{ $t('Jelajahi') }}</h2>
                <RouterLink to="/">{{ $t('Peta fasilitas') }}</RouterLink>
                <RouterLink to="/kontribusi">{{ $t('Usulkan fasilitas') }}</RouterLink>
                <RouterLink to="/kontribusi?mode=koreksi">{{ $t('Laporkan koreksi') }}</RouterLink>
                <RouterLink to="/status">{{ $t('Cek status pengajuan') }}</RouterLink>
            </div>

            <div v-if="user">
                <h2>{{ $t('Pemetaan') }}</h2>
                <RouterLink to="/verifikasi">{{ $t('Dashboard verifikasi') }}</RouterLink>
                <RouterLink to="/admin">{{ $t('Dashboard administrasi') }}</RouterLink>
                <RouterLink to="/tentang">{{ $t('Panduan penggunaan') }}</RouterLink>
            </div>

<div v-else>
                <h2>{{ $t('Panduan') }}</h2>
                <RouterLink to="/tentang">{{ $t('Tentang AksesKita') }}</RouterLink>
                <RouterLink to="/masuk">{{ $t('Masuk petugas') }}</RouterLink>
            </div>

            <div>
                <h2>{{ $t('Kontak') }}</h2>
                <a href="mailto:halo@akeskita.example">halo@akeskita.example</a>
<a href="https://github.com/dymmm-0/fasilitas-disabilitas" rel="noopener">{{ $t('Komunitas relawan AksesKita') }}</a>
                <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>
            </div>
        </div>

        <div class="container site-footer__legal">
<span>{{ $t('© AksesKita 2026 · Proyek kompetisi Fundev') }}</span>
            <span>{{ $t('Fasilitas dari impor dataset OpenStreetMap Kota Medan · pengajuan kontribusi tersimpan di basis data') }}</span>
        </div>
    </footer>
</template>