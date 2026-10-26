<script setup>
import { AVAILABILITY, ATTRIBUTES } from '../../data/meta';
import StatusPill from '../common/StatusPill.vue';

defineProps({
    facility: { type: Object, required: true },
});
</script>

<template>
    <div class="kv">
        <div v-for="attribute in ATTRIBUTES" :key="attribute.id" class="kv__row accessibility-row">
            <span class="kv__label">{{ $t(attribute.label) }}</span>
            <StatusPill kind="availability" :value="facility.accessibility[attribute.id]" />
            <span v-if="facility.accessibility[attribute.id] === 'unknown'" class="kv__note">{{ $t(attribute.hint) }}</span>
            <span v-else class="kv__note">
                {{ facility.accessibilityDetails?.[attribute.id]
                    || $t(AVAILABILITY[facility.accessibility[attribute.id]].label) + ' ' + $t('menurut data sumber.') }}
            </span>
        </div>
    </div>
</template>