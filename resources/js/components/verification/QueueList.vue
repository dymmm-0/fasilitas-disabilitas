<script setup>
import { formatDate, QUEUE_TYPES, statusMeta } from '../../data/meta';

defineProps({
    items: { type: Array, required: true },
    selectedId: { type: String, default: null },
});

const emit = defineEmits(['select']);
</script>

<template>
    <ul v-if="items.length" class="list-scroll" style="list-style: none">
        <li v-for="item in items" :key="`${item.kind}-${item.id}`">
            <button
                type="button"
                class="card card--hover"
                :style="item.id === selectedId ? { borderColor: 'var(--ink)', transform: 'translateY(-6px)' } : null"
                :aria-current="item.id === selectedId ? 'true' : undefined"
                @click="emit('select', item.id)"
            >
                <div class="card__body">
                    <div class="card__meta">
                        <span>
                            <span aria-hidden="true">{{ QUEUE_TYPES[item.kind].glyph }}</span>
                            {{ QUEUE_TYPES[item.kind].label }} · {{ item.source }}
                        </span>
                        <span>{{ item.id }}</span>
                    </div>

                    <h3 class="card__title card__title--sm">{{ item.name }}</h3>

                    <p class="card__text">
                        {{ item.city }}
                        <template v-if="item.pendingReports.length">
                            · {{ item.pendingReports.length }} laporan koreksi menunggu keputusan
                        </template>
                    </p>

                    <div class="card__foot">
                        <span class="pill" :class="statusMeta(item.kind, item.status).pill">
                            <span class="pill__glyph" aria-hidden="true">{{ statusMeta(item.kind, item.status).glyph }}</span>
                            {{ statusMeta(item.kind, item.status).label }}
                        </span>
                        <span class="meta">{{ item.submittedBy ?? 'Tanpa pengaju' }} · {{ formatDate(item.createdAt) }}</span>
                    </div>
                </div>
            </button>
        </li>
    </ul>

    <div v-else class="empty-state" role="status">
        <span class="label">Antrean kosong</span>
        <p class="card__text">Tidak ada baris yang cocok dengan penyaring saat ini.</p>
    </div>
</template>
