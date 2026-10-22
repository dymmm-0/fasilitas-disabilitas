<script setup>
import { onMounted, ref } from 'vue';
import { request } from '../../composables/useAuth';

const users = ref([]);
const error = ref('');

onMounted(async () => {
    try {
        users.value = await request('/api/users');
    } catch {
        error.value = 'Daftar akun hanya dapat dibaca setelah masuk sebagai admin.';
    }
});
</script>

<template>
    <div class="table-wrap" tabindex="0" role="region" aria-label="Tabel akun admin">
        <table class="table">
            <caption class="help" style="text-align: left; padding-bottom: 12px">
                Satu-satunya akun adalah admin. Masyarakat tidak memiliki akun — kontribusi cukup mengisi nama pada
                formulir.
            </caption>
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Surel</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td><b>{{ user.name }}</b></td>
                    <td>{{ user.email }}</td>
                </tr>
            </tbody>
        </table>
        <p v-if="error" class="error-text" role="alert">{{ error }}</p>
    </div>
</template>