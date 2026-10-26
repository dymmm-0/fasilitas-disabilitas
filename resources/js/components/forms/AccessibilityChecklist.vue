<script setup>
import { AVAILABILITY, ATTRIBUTES } from '../../data/meta';

const props = defineProps({
    modelValue: { type: Object, required: true },
});

const emit = defineEmits(['update:modelValue']);

const setValue = (id, value) => {
    emit('update:modelValue', { ...props.modelValue, [id]: value });
};
</script>

<template>
    <div class="stack">
        <fieldset v-for="attribute in ATTRIBUTES" :key="attribute.id">
            <legend class="fieldset__legend">{{ $t(attribute.label) }}</legend>
            <p class="help">{{ $t(attribute.hint) }}</p>
            <div class="choice-grid">
                <label v-for="(meta, key) in AVAILABILITY" :key="key" class="choice">
                    <input
                        type="radio"
                        :name="`atribut-${attribute.id}`"
                        :value="key"
                        :checked="modelValue[attribute.id] === key"
                        @change="setValue(attribute.id, key)"
                    >
                    <span aria-hidden="true">{{ meta.glyph }}</span>
                    {{ $t(meta.label) }}
                </label>
            </div>
        </fieldset>
    </div>
</template>