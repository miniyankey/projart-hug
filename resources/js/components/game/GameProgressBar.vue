<script setup>
// Barre de progression fine, pleine largeur (sous la navbar).
import { computed } from 'vue';

const props = defineProps({
    value: { type: Number, default: 0 }, // questions franchies
    total: { type: Number, default: 1 },
});

const pct = computed(() =>
    props.total > 0 ? Math.round((props.value / props.total) * 100) : 0,
);
</script>

<template>
    <div
        class="h-2.5 w-full overflow-hidden bg-neutral-300"
        role="progressbar"
        :aria-valuenow="value"
        :aria-valuemax="total"
    >
        <div
            class="h-full bg-[var(--brand,#7c3aed)] transition-[width] duration-300"
            :style="{ width: `${pct}%` }"
        />
    </div>
</template>
