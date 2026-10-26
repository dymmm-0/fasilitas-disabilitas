<script setup>
import { useI18n } from 'vue-i18n';
import { setLocale } from '../../i18n';

// Label pilihan bahasa ditulis dalam bahasanya sendiri, jadi tidak diterjemahkan.
const LOCALES = [
    { id: 'id', label: 'ID' },
    { id: 'en', label: 'EN' },
];

const { locale } = useI18n();

const onChange = (event) => setLocale(event.target.value);
</script>

<template>
    <div class="lang-select">
        <select
            class="select lang-select__control"
            :value="locale"
            :aria-label="$t('Pilih bahasa')"
            @change="onChange"
        >
            <option v-for="item in LOCALES" :key="item.id" :value="item.id" :lang="item.id">
                {{ item.label }}
            </option>
        </select>
        <span class="lang-select__arrow" aria-hidden="true">▾</span>
    </div>
</template>
