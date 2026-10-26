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
        <div class="table-wrap" tabindex="0" role="region" :aria-label="$t('Tabel kategori fasilitas')">
            <table class="table">
                <caption class="help" style="text-align: left; padding-bottom: 12px">{{ $t('Jumlah fasilitas dihitung dari data yang sedang tampil. Menonaktifkan kategori menyembunyikannya dari filter peta pada implementasi nyata.') }}</caption>
                <thead>
                    <tr>
                        <th scope="col">{{ $t('Kode') }}</th>
                        <th scope="col">{{ $t('Kategori') }}</th>
                        <th scope="col">{{ $t('Fasilitas') }}</th>
                        <th scope="col">{{ $t('Tampil di filter') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id">
                        <td class="mono">{{ category.glyph }}</td>
                        <td><b>{{ $t(category.label) }}</b></td>
                        <td>{{ counts.get(category.id) ?? 0 }}</td>
                        <td>
                            <label class="toggle">
                                <input v-model="category.active" type="checkbox">
                                <span>{{ category.active ? $t('Aktif') : $t('Nonaktif') }}</span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form class="inline-actions" @submit.prevent="addCategory">
            <div class="field" style="min-width: 240px">
                <label for="kategori-baru">{{ $t('Tambah kategori') }}</label>
                <input id="kategori-baru" v-model="newName" class="input" :placeholder="$t('Mis. Ruang terbuka hijau')">
            </div>
            <button type="submit" class="btn">{{ $t('Tambah') }}</button>
        </form>
    </div>
</template>
