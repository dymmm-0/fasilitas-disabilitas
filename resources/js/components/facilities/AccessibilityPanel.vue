<script setup>
import { AVAILABILITY, ATTRIBUTES } from '../../data/meta';
import StatusPill from '../common/StatusPill.vue';

defineProps({
    facility: { type: Object, required: true },
});
</script>

<template>
    <div class="kv">
        <div v-for="attribute in ATTRIBUTES" :key="attribute.id" class="kv__row">
            <span class="kv__label">{{ attribute.label }}</span>
            <StatusPill kind="availability" :value="facility.accessibility[attribute.id]" />
            <span class="kv__note">
                {{
                    facility.accessibility[attribute.id] === 'belum'
                        ? attribute.hint
                        : AVAILABILITY[facility.accessibility[attribute.id]].label + ' menurut pemeriksaan terakhir.'
                }}
            </span>
        </div>
    </div>
</template>