import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import i18n from './i18n';
import { useAuth } from './composables/useAuth';

// Sesi dimuat lebih dulu supaya header tidak sempat tampil sebagai guest.
useAuth()
    .load()
    .finally(() => createApp(App).use(router).use(i18n).mount('#app'));