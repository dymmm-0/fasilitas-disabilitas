<script setup>
import { CATEGORIES, VERIFICATION } from '../../data/meta';
import { useFacilities } from '../../composables/useFacilities';

const { filters, attributes, results, hasFilters, toggleAttribute, resetFilters } = useFacilities();
</script>

<template>
    <form class="filter-bar" role="search" @submit.prevent>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr))">
            <div class="field">
                <label for="filter-cari">Cari fasilitas</label>
                <input
                    id="filter-cari"
                    v-model="filters.query"
                    class="input"
                    type="search"
                    placeholder="Nama, alamat, atau kota"
                    autocomplete="off"
                >
            </div>

            <div class="field">
                <label for="filter-kategori">Kategori</label>
                <select id="filter-kategori" v-model="filters.category" class="select">
                    <option value="all">Semua kategori</option>
                    <option v-for="category in CATEGORIES" :key="category.id" :value="category.id">
                        {{ category.label }}
                    </option>
                </select>
            </div>

            <div class="field">
                <label for="filter-status">Status verifikasi</label>
                <select id="filter-status" v-model="filters.verification" class="select">
                    <option value="all">Semua status</option>
                    <option v-for="(meta, key) in VERIFICATION" :key="key" :value="key">{{ meta.label }}</option>
                </select>
            </div>
        </div>

        <fieldset>
            <legend class="fieldset__legend">Atribut aksesibilitas yang harus tersedia</legend>
            <div class="cluster">
                <button
                    v-for="attribute in attributes"
                    :key="attribute.id"
                    type="button"
                    class="chip"
                    :aria-pressed="filters.attributes.includes(attribute.id)"
                    @click="toggleAttribute(attribute.id)"
                >
                    <span class="chip__mark" aria-hidden="true">{{ filters.attributes.includes(attribute.id) ? '✓' : '+' }}</span>
                    {{ attribute.label }}
                </button>
            </div>
        </fieldset>

        <div class="cluster cluster--between">
            <p class="meta" role="status" aria-live="polite">
                {{ results.length }} fasilitas ditampilkan
                <span v-if="hasFilters">· filter aktif</span>
            </p>
            <button v-if="hasFilters" type="button" class="btn btn--sm" @click="resetFilters">Kosongkan filter</button>
        </div>
    </form>
</template>