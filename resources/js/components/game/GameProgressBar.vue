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
        class="progress"
        role="progressbar"
        :aria-valuenow="value"
        :aria-valuemax="total"
    >
        <div class="progress__fill" :style="{ width: `${pct}%` }" />
    </div>
</template>

<style scoped>
.progress {
    width: 100%;
    height: 10px;
    background: #d4d4d4;
    overflow: hidden;
}

.progress__fill {
    height: 100%;
    background: var(--brand, #7c3aed);
    transition: width 350ms ease;
}
</style>
