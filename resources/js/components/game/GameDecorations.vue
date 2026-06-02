<script setup>
// Layer 3 — scenery filling the space between path segments.
// Each item is { x, y, type }; the matching SVG file lives in
// public/img/game/deco/<type>.svg and is placed via <image>.
// All deco sprites share the same box (anchor 0,0 = the path-side point).
defineProps({
    items: { type: Array, required: true },
});

// Sprite box — must match the viewBox of the files in public/img/game/deco/
const BOX = { x: -32, y: -44, w: 64, h: 92 };
</script>

<template>
    <g>
        <image
            v-for="(d, i) in items"
            :key="i"
            :href="`/img/game/deco/${d.type}.svg`"
            :x="d.x + BOX.x"
            :y="d.y + BOX.y"
            :width="BOX.w"
            :height="BOX.h"
        />
    </g>
</template>
