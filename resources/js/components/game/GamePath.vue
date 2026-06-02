<script setup>
// Layer 2 — the H/V dirt road, drawn as 4 stacked polylines (shadow → surface
// → centre lane → gravel dashes). Receives the pre-built points string.
defineProps({
    points: { type: String, required: true },
    width: { type: Number, default: 32 },
});
</script>

<template>
    <g>
        <!-- Shadow -->
        <polyline
            :points="points"
            fill="none"
            stroke="#3a2008"
            :stroke-width="width + 10"
            stroke-linejoin="miter"
            stroke-linecap="square"
        />
        <!-- Dirt surface -->
        <polyline
            :points="points"
            fill="none"
            stroke="#a06820"
            :stroke-width="width"
            stroke-linejoin="miter"
            stroke-linecap="square"
        />
        <!-- Lighter centre lane -->
        <polyline
            :points="points"
            fill="none"
            stroke="#cc9040"
            :stroke-width="width - 8"
            stroke-linejoin="miter"
            stroke-linecap="square"
        />
        <!-- Gravel dashes -->
        <polyline
            :points="points"
            fill="none"
            stroke="#dca858"
            :stroke-width="4"
            stroke-linejoin="miter"
            stroke-linecap="square"
            stroke-dasharray="14 12"
            opacity="0.65"
        />
    </g>
</template>
