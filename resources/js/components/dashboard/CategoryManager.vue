<script setup>
import { computed, ref } from 'vue';
import { CATEGORIES } from '../../data/meta';
import { useFacilities } from '../../composables/useFacilities';

const { facilities } = useFacilities();

// Jumlah fasilitas dihitung dari data yang sedang tampil, bukan angka tetap.
const counts = computed(() => {
    const map = new Map();

    facilities.value.forEach((facility) => map.set(facility.category, (map.get(facility.category) ?? 0) + 1));

    return map;
});

const categories = ref(CATEGORIES.map((category) => ({ ...category, active: true })));
const newName = ref('');

const addCategory = () => {
    const name = newName.value.trim();
    if (!name) return;

    const id = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    if (categories.value.some((category) => category.id === id)) return;

    categories.value = [...categories.value, { id, label: name, glyph: '—', active: true }];
    newName.value = '';
};
</script>

<template>
    <div class="stack--sm">
        <div class="table-wrap" tabindex="0" role="region" aria-label="Tabel kategori fasilitas">
            <table class="table">
                <caption class="help" style="text-align: left; padding-bottom: 12px">
                    Jumlah fasilitas dihitung dari data yang sedang tampil. Menonaktifkan kategori menyembunyikannya
                    dari filter peta pada implementasi nyata.
                </caption>
                <thead>
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Fasilitas</th>
                        <th scope="col">Tampil di filter</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id">
                        <td class="mono">{{ category.glyph }}</td>
                        <td><b>{{ category.label }}</b></td>
                        <td>{{ counts.get(category.id) ?? 0 }}</td>
                        <td>
                            <label class="toggle">
                                <input v-model="category.active" type="checkbox">
                                <span>{{ category.active ? 'Aktif' : 'Nonaktif' }}</span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form class="inline-actions" @submit.prevent="addCategory">
            <div class="field" style="min-width: 240px">
                <label for="kategori-baru">Tambah kategori</label>
                <input id="kategori-baru" v-model="newName" class="input" placeholder="Mis. Ruang terbuka hijau">
            </div>
            <button type="submit" class="btn">Tambah</button>
        </form>
    </div>
</template>
