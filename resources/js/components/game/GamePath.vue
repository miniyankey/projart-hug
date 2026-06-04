<script setup>
// Layer 2 — le trottoir/rue urbain, dessiné en 4 polylines superposées
// (ombre → surface béton → bande centrale → joints de dalles).
defineProps({
    points: { type: String, required: true },
    width: { type: Number, default: 32 },
});
</script>

<template>
    <g>
        <!-- Ombre portée -->
        <polyline
            :points="points"
            fill="none"
            stroke="#111111"
            :stroke-width="width + 10"
            stroke-linejoin="miter"
            stroke-linecap="square"
        />
        <!-- Surface béton / trottoir -->
        <polyline
            :points="points"
            fill="none"
            stroke="#868686"
            :stroke-width="width"
            stroke-linejoin="miter"
            stroke-linecap="square"
        />
        <!-- Bande centrale plus claire -->
        <polyline
            :points="points"
            fill="none"
            stroke="#a0a0a0"
            :stroke-width="width - 8"
            stroke-linejoin="miter"
            stroke-linecap="square"
        />
        <!-- Joints de dalles (tirets blancs semi-transparents) -->
        <polyline
            :points="points"
            fill="none"
            stroke="#ffffffaa"
            :stroke-width="2"
            stroke-linejoin="miter"
            stroke-linecap="square"
            stroke-dasharray="16 14"
            opacity="0.45"
        />
    </g>
</template>
