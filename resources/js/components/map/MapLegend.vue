<script setup>
import { CATEGORIES } from '../../data/meta';
</script>

<template>
    <div class="map-legend">
        <span class="map-legend__item">
            <span class="map-legend__swatch" aria-hidden="true">✓</span>
            Terverifikasi
        </span>
        <span class="map-legend__item">
            <span class="map-legend__swatch map-legend__swatch--pending" aria-hidden="true">◐</span>
            Menunggu / perlu perbaikan
        </span>
        <span class="map-legend__item">Kode penanda: {{ CATEGORIES.map((c) => `${c.glyph} ${c.label}`).join(' · ') }}</span>
    </div>
</template>