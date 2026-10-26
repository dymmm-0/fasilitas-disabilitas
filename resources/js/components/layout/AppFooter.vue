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
                <span class="wordmark"
                    style="font-size: 32px; display: block; margin-bottom: 12px">Akses<em>Kita</em></span>
                <p style="line-height: 1.55; max-width: 320px; color: var(--body)">
                    Platform pemetaan dan verifikasi fasilitas publik ramah disabilitas, dikelola bersama masyarakat.
                </p>
            </div>

            <div>
                <h2>Jelajahi</h2>
                <RouterLink to="/">Peta fasilitas</RouterLink>
                <RouterLink to="/kontribusi">Usulkan fasilitas</RouterLink>
                <RouterLink to="/kontribusi?mode=koreksi">Laporkan koreksi</RouterLink>
                <RouterLink to="/status">Cek status pengajuan</RouterLink>
            </div>

            <div v-if="user">
                <h2>Pemetaan</h2>
                <RouterLink to="/verifikasi">Dashboard verifikasi</RouterLink>
                <RouterLink to="/admin">Dashboard administrasi</RouterLink>
                <RouterLink to="/tentang">Panduan penggunaan</RouterLink>
            </div>

            <div>
                <h2>Kontak</h2>
                <a href="mailto:halo@akeskita.example">halo@akeskita.example</a>
                <a href="https://github.com/dymmm-0/fasilitas-disabilitas">Komunitas relawan AksesKita</a>
            </div>
        </div>

        <div class="container site-footer__legal">
            <span>© 2026 AksesKita </span>
            <span>Data peta © <a href="https://www.openstreetmap.org/copyright" target="_blank"
                    rel="noopener noreferrer">OpenStreetMap contributors</a></span>
        </div>
    </footer>
</template>