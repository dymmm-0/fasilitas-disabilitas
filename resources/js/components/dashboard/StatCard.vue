<script setup>
defineProps({
    value: { type: [String, Number], required: true },
    label: { type: String, required: true },
    hint: { type: String, default: '' },
});
</script>

<template>
    <div class="stat">
        <span class="stat__value">{{ value }}</span>
        <span class="stat__label">{{ label }}</span>
        <span v-if="hint" class="help">{{ hint }}</span>
    </div>
</template>