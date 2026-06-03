<script setup>
// Layer 4 — octogone pixel art sur le chemin.
// Variantes : 'start' (▶ brand), 'checkpoint' (? coloré selon le statut),
// 'end' (★ doré + drapeau).
// Statut d'un checkpoint : 'locked' (gris), 'eligible' (brand), 'ineligible' (noir).
// Un checkpoint répondu (statut ≠ locked) est cliquable → émet `select`.
import { computed } from 'vue';
import { octPts } from '@/lib/gameMap';

const props = defineProps({
    x: { type: Number, required: true },
    y: { type: Number, required: true },
    variant: { type: String, default: 'checkpoint' },
    status: { type: String, default: 'locked' },
});

const emit = defineEmits(['select']);

const R = 28; // base octagon radius
const CUT = 11; // base chamfer

// Palette des checkpoints selon le statut
const STATUS = {
    locked: { fill: '#7a7a8c', highlight: '#aaaabe', iconFill: '#33334a' },
    eligible: {
        fill: 'var(--brand,#7c3aed)',
        highlight: '#b090ff',
        iconFill: '#fff',
    },
    ineligible: { fill: '#111', highlight: '#555', iconFill: '#fff' },
};

const cfg = computed(() => {
    if (props.variant === 'start') {
        return {
            big: true,
            fill: 'var(--brand,#7c3aed)',
            highlight: '#b090ff',
            icon: '▶',
            iconFill: '#fff',
            iconSize: 18,
        };
    }

    if (props.variant === 'end') {
        return {
            big: true,
            fill: '#e8b800',
            highlight: '#ffe060',
            icon: '★',
            iconFill: '#333',
            iconSize: 18,
            flag: true,
        };
    }

    // checkpoint « question »
    const s = STATUS[props.status] || STATUS.locked;

    return {
        big: false,
        fill: s.fill,
        highlight: s.highlight,
        icon: '?',
        iconFill: s.iconFill,
        iconSize: 15,
        pixelFont: true,
    };
});

const clickable = computed(
    () => props.variant === 'checkpoint' && props.status !== 'locked',
);

const r = computed(() => (cfg.value.big ? R + 6 : R));
const cut = computed(() => (cfg.value.big ? CUT + 2 : CUT));
const innerR = computed(() => (cfg.value.big ? R + 3 : R - 3));
const innerCut = computed(() => (cfg.value.big ? CUT : CUT - 2));

const highlight = computed(() =>
    cfg.value.big
        ? { x: props.x - R - 1, w: R * 2 - 4 }
        : { x: props.x - R + CUT + 2, w: (R - CUT) * 2 - 4 },
);

const shadowPts = computed(() =>
    octPts(props.x + 3, props.y + 3, r.value, cut.value),
);
const borderPts = computed(() => octPts(props.x, props.y, r.value, cut.value));
const fillPts = computed(() =>
    octPts(props.x, props.y, innerR.value, innerCut.value),
);

const flagPole = computed(() => ({ x: props.x - 2, y: props.y - R - 36 }));
const flagPts = computed(() => {
    const fx = props.x + 2;
    const top = props.y - R - 36;

    return `${fx},${top} ${fx + 20},${top + 8} ${fx},${top + 16}`;
});

function onClick() {
    if (clickable.value) {
        emit('select');
    }
}
</script>

<template>
    <g :class="{ 'cursor-pointer': clickable }" @click="onClick">
        <polygon :points="shadowPts" fill="#00000055" />
        <polygon :points="borderPts" fill="#111" />
        <polygon :points="fillPts" :fill="cfg.fill" />

        <!-- Top highlight stripe -->
        <rect
            :x="highlight.x"
            :y="y - R + 5"
            :width="highlight.w"
            height="5"
            :fill="cfg.highlight"
            opacity="0.8"
        />

        <!-- Flag (end only) -->
        <template v-if="cfg.flag">
            <rect
                :x="flagPole.x"
                :y="flagPole.y"
                width="4"
                height="36"
                fill="#555"
            />
            <polygon
                :points="flagPts"
                :fill="cfg.fill"
                stroke="#111"
                stroke-width="2"
            />
        </template>

        <!-- Icon -->
        <text
            :x="x"
            :y="y + (cfg.pixelFont ? 7 : 8)"
            text-anchor="middle"
            text-rendering="optimizeLegibility"
            :font-size="cfg.iconSize"
            :font-family="
                cfg.pixelFont ? `'Press Start 2P', monospace` : undefined
            "
            :fill="cfg.iconFill"
        >
            {{ cfg.icon }}
        </text>
    </g>
</template>
