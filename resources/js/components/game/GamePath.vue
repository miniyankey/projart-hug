<script setup>
// Layer 2 — le sentier de terre/sable (style overworld), dessiné en polylines
// superposées : bordure de terre → sable → centre plus clair → gravillons.
// Bords arrondis (round) pour un tracé organique, pas un trottoir.
defineProps({
    points: { type: String, required: true },
    width: { type: Number, default: 32 },
});
</script>

<template>
    <g>
        <!-- Bordure de terre (rive du sentier) -->
        <polyline
            :points="points"
            fill="none"
            stroke="#7a5a32"
            :stroke-width="width + 10"
            stroke-linejoin="round"
            stroke-linecap="round"
        />
        <!-- Terre plus sombre sous le sable -->
        <polyline
            :points="points"
            fill="none"
            stroke="#a07b46"
            :stroke-width="width + 2"
            stroke-linejoin="round"
            stroke-linecap="round"
        />
        <!-- Sable -->
        <polyline
            :points="points"
            fill="none"
            stroke="#e0c98c"
            :stroke-width="width - 2"
            stroke-linejoin="round"
            stroke-linecap="round"
        />
        <!-- Centre plus clair -->
        <polyline
            :points="points"
            fill="none"
            stroke="#ecdca8"
            :stroke-width="width - 14"
            stroke-linejoin="round"
            stroke-linecap="round"
        />
        <!-- Gravillons (pointillé discret) -->
        <polyline
            :points="points"
            fill="none"
            stroke="#c8ad72"
            :stroke-width="3"
            stroke-linejoin="round"
            stroke-linecap="round"
            stroke-dasharray="3 22"
            opacity="0.7"
        />
    </g>
</template>
