<script setup>
import { useI18n } from 'vue-i18n';
import { setLocale } from '../../i18n';

// Label pilihan bahasa ditulis dalam bahasanya sendiri, jadi tidak diterjemahkan.
const LOCALES = [
    { id: 'id', label: 'ID' },
    { id: 'en', label: 'EN' },
];

const { locale } = useI18n();
</script>

<template>
    <div class="cluster" role="group" :aria-label="$t('Pilih bahasa')" style="gap: 8px">
        <button
            v-for="item in LOCALES"
            :key="item.id"
            type="button"
            class="chip"
            :lang="item.id"
            :aria-pressed="locale === item.id"
            @click="setLocale(item.id)"
        >
            {{ item.label }}
        </button>
    </div>
</template>