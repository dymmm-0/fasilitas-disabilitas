<script setup>
import { ref } from 'vue';
import { ROLES } from '../../data/meta';
import { USERS } from '../../data/users';

// Salinan lokal: pengelolaan pengguna bersifat simulasi UI, tanpa autentikasi.
const users = ref(USERS.map((user) => ({ ...user })));
</script>

<template>
    <div class="table-wrap" tabindex="0" role="region" aria-label="Tabel pengguna dan peran">
        <table class="table">
            <caption class="help" style="text-align: left; padding-bottom: 12px">
                Peran diubah langsung pada tabel untuk kebutuhan demonstrasi; tidak ada penyimpanan permanen.
            </caption>
            <thead>
                <tr>
                    <th scope="col">Pengguna</th>
                    <th scope="col">Kota</th>
                    <th scope="col">Peran</th>
                    <th scope="col">Kontribusi</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>
                        <b>{{ user.name }}</b>
                        <span class="meta" style="display: block">{{ user.email }}</span>
                    </td>
                    <td>{{ user.city }}</td>
                    <td>
                        <label class="visually-hidden" :for="`peran-${user.id}`">Peran untuk {{ user.name }}</label>
                        <select :id="`peran-${user.id}`" v-model="user.role" class="select">
                            <option v-for="(role, key) in ROLES" :key="key" :value="key">{{ role.label }}</option>
                        </select>
                        <span class="help" style="display: block">{{ ROLES[user.role].desc }}</span>
                    </td>
                    <td>{{ user.submissions }} pengajuan</td>
                    <td>
                        <label class="toggle">
                            <input v-model="user.status" type="checkbox" true-value="aktif" false-value="nonaktif">
                            <span>{{ user.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>