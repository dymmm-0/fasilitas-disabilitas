<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

defineProps({
    as: { type: String, default: 'div' },
});

const el = ref(null);
const shown = ref(false);
let observer;

onMounted(() => {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduced || !('IntersectionObserver' in window)) {
        shown.value = true;
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                shown.value = true;
                observer.disconnect();
            }
        },
        { threshold: 0.1 },
    );

    observer.observe(el.value);
});

onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
    <component :is="as" ref="el" class="reveal" :class="{ 'is-in': shown }">
        <slot />
    </component>
</template>