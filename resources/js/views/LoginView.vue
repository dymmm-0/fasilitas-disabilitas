<script setup>
import { onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const { login } = useAuth();
const router = useRouter();
const route = useRoute();

const section = ref(null);
let footer = null;

onMounted(() => {
    footer = document.querySelector('.site-footer');

    if (footer) footer.style.display = 'none';

    const header = document.querySelector('.site-header');

    if (header && section.value) {
        section.value.style.minHeight = `calc(100svh - ${header.offsetHeight}px)`;
    }
});

onBeforeUnmount(() => {
    if (footer) footer.style.display = '';
});

const form = reactive({ email: '', password: '' });
const error = ref('');
const sending = ref(false);

const submit = async () => {
    error.value = '';
    sending.value = true;

    try {
        await login(form.email, form.password);

        const redirect = route.query.redirect;
        await router.replace(typeof redirect === 'string' && redirect.startsWith('/') ? redirect : '/admin');
    } catch (e) {
        error.value =
            e.errors?.email?.[0] ??
            (e.status === 419 ? 'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.' : 'Tidak dapat masuk. Periksa koneksi lalu coba lagi.');
    } finally {
        sending.value = false;
    }
};
</script>

<template>
    <section ref="section" class="container section">
        <div class="panel stack" style="max-width: 420px; margin: 0 auto">
            <p class="label">Akun admin - Robert</p>
            <h1 class="display-md">Masuk untuk <em>mengelola data.</em></h1>
            <p class="body-copy">
                Hanya admin yang memiliki akun. Masyarakat tidak perlu mendaftar 
            </p>

            <form class="stack" novalidate @submit.prevent="submit">
                <div class="field">
                    <label for="masuk-surel">Surel</label>
                    <input
                        id="masuk-surel"
                        v-model="form.email"
                        class="input"
                        type="email"
                        autocomplete="username"
                        required
                        :aria-invalid="Boolean(error)"
                    >
                </div>

                <div class="field">
                    <label for="masuk-sandi">Kata sandi</label>
                    <input
                        id="masuk-sandi"
                        v-model="form.password"
                        class="input"
                        type="password"
                        autocomplete="current-password"
                        required
                        :aria-invalid="Boolean(error)"
                        :aria-describedby="error ? 'masuk-error' : undefined"
                    >
                </div>

                <p v-if="error" id="masuk-error" class="error-text" role="alert">{{ error }}</p>

                <button type="submit" class="btn btn--primary" :disabled="sending">
                    {{ sending ? 'Memproses…' : 'Masuk' }}
                </button>
            </form>

            <p class="help">
                Peta, detail fasilitas, dan halaman kontribusi tetap dapat dibuka tanpa masuk.
            </p>
        </div>
    </section>
</template>

<style scoped>
/* Halaman masuk dipadatkan agar muat dalam satu layar tanpa gulir. */
.section {
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    padding-block: clamp(20px, 4vh, 48px);
    min-height: calc(100vh - 58px);
    min-height: calc(100svh - 58px);
}

.panel {
    width: 100%;
    padding: clamp(20px, 3vh, 28px);
}

.stack {
    gap: clamp(14px, 1.8vh, 22px);
}
</style>