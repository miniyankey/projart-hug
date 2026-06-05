<script setup>
// Layer 3 — scenery filling the space between path segments.
// Each item is { x, y, type, landmark? }; the matching SVG file lives in
// public/img/game/deco/<type>.svg (viewBox "-32 -44 64 92") and is placed via
// <image>. Every sprite is anchored by its base-centre at (d.x, d.y + 48) so a
// scaled-up landmark still stands on the same ground point as a normal prop.
defineProps({
    items: { type: Array, required: true },
});

const BASE_W = 64;
const BASE_H = 92;
const BASE_OFFSET = 48; // viewBox bottom relative to the anchor (d.y)

function box(d) {
    const s = d.scale || 1;
    const w = BASE_W * s;
    const h = BASE_H * s;

    return {
        x: d.x - w / 2,
        y: d.y + BASE_OFFSET - h,
        w,
        h,
    };
}
</script>

<template>
    <g>
        <image
            v-for="(d, i) in items"
            :key="i"
            :href="`/img/game/deco/${d.type}.svg`"
            :x="box(d).x"
            :y="box(d).y"
            :width="box(d).w"
            :height="box(d).h"
        />
    </g>
</template>
