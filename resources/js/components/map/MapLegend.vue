<script setup>
import { CATEGORIES } from '../../data/meta';
</script>

<template>
    <div class="map-legend">
        <span class="map-legend__item">
            <span class="map-legend__swatch" aria-hidden="true">✓</span>{{ $t('Terverifikasi') }}</span>
        <span class="map-legend__item">
            <span class="map-legend__swatch map-legend__swatch--pending" aria-hidden="true">◐</span>{{ $t('Menunggu / perlu perbaikan') }}</span>
        <span class="map-legend__item"><i18n-t keypath="Kode penanda: {a}" tag="span" scope="global"><template #a>{{ CATEGORIES.map((c) => `${c.glyph} ${$t(c.label)}`).join(' · ') }}</template></i18n-t></span>
    </div>
</template>