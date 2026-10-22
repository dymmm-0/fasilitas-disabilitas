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
            <span v-if="facility.accessibility[attribute.id] === 'belum'" class="kv__note">{{ attribute.hint }}</span>
            <span v-else class="kv__note">
                {{
                    facility.accessibilityDetails?.[attribute.id]
                        || AVAILABILITY[facility.accessibility[attribute.id]].label + ' menurut data sumber.'
                }}
            </span>
        </div>
    </div>
</template>