<script setup>
import { gsap } from 'gsap';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import GameCheckpoint from './GameCheckpoint.vue';
import GameDecorations from './GameDecorations.vue';
import GamePath from './GamePath.vue';
import {
    buildDecos,
    buildMap,
    computeCorners,
    generateGrassTile,
    mkRng,
    polyPts,
    smoothPos,
} from '@/lib/gameMap';

const props = defineProps({
    questionCount: { type: Number, required: true },
    // Nombre de checkpoints déjà franchis (question répondue). Pochy ne peut
    // pas scroller au-delà du prochain checkpoint non encore franchi.
    clearedCount: { type: Number, default: 0 },
    // Statut par checkpoint : 'locked' | 'eligible' | 'ineligible'
    statuses: { type: Array, default: () => [] },
});

const emit = defineEmits(['reach', 'select']);

// ─── Constants ───────────────────────────────────────────────────────────────
const PATH_W = 32; // dirt road stroke width
const CORNER_BLEND = 100; // px — bezier rounding radius at H/V corners

// ─── State ───────────────────────────────────────────────────────────────────
const rootRef = ref(null);
const viewH = ref(0);
const viewW = ref(0);
const MAPW = ref(0);
const mapH = ref(0);
const segments = ref([]);
const cps = ref([]);
const startPt = ref({ x: 0, y: 0 });
const endPt = ref({ x: 0, y: 0 });
const totalLen = ref(0);
const progress = ref(0);
const targetPrg = ref(0);
const grassTile = ref(''); // data-URL generated at mount via Canvas

let lastTouchY = 0;
let resizeObserver = null;
let resizeRaf = 0;

// ─── Computed ────────────────────────────────────────────────────────────────
const cornerData = computed(() => computeCorners(segments.value, CORNER_BLEND));
const curPos = computed(() =>
    smoothPos(segments.value, cornerData.value, progress.value),
);
const mapTX = computed(() => Math.round(viewW.value / 2 - curPos.value.x));
const mapTY = computed(() => Math.round(viewH.value / 2 - curPos.value.y));
const polyPtsStr = computed(() => polyPts(segments.value)); // built once, used by 4 polylines
const decos = computed(() =>
    buildDecos(
        segments.value,
        MAPW.value,
        mapH.value,
        props.questionCount + 77,
    ),
);

// ─── Checkpoint stop logic ────────────────────────────────────────────────────
// Pochy may scroll only up to the next un-cleared checkpoint. Once all
// checkpoints are answered, the end zone (totalLen) becomes reachable.
const cap = computed(() => {
    const list = cps.value;

    if (props.clearedCount >= list.length) {
        return totalLen.value;
    }

    return list[props.clearedCount].progress;
});

// Emit `reach` once when Pochy arrives at the current target checkpoint.
const stopReached = ref(false);

watch(progress, (val) => {
    if (props.clearedCount >= cps.value.length) {
        return;
    }

    if (!stopReached.value && val >= cap.value - 0.5) {
        stopReached.value = true;
        emit('reach', props.clearedCount);
    } else if (stopReached.value && val < cap.value - 40) {
        // Scrolled back up → autorise un nouveau déclenchement (ex. après « Retour »)
        stopReached.value = false;
    }
});

// A new checkpoint was cleared → allow scrolling to the next one
watch(
    () => props.clearedCount,
    () => {
        stopReached.value = false;
    },
);

// ─── Scroll — slower multiplier so each checkpoint takes more wheel effort ────
function onWheel(e) {
    e.preventDefault();
    targetPrg.value = Math.max(
        0,
        Math.min(cap.value, targetPrg.value + e.deltaY * 0.55),
    );
    gsap.killTweensOf(progress);
    gsap.to(progress, {
        value: targetPrg.value,
        duration: 0.6,
        ease: 'power3.out',
    });
}

function onTouchStart(e) {
    lastTouchY = e.touches[0].clientY;
}

function onTouchMove(e) {
    e.preventDefault();
    const d = lastTouchY - e.touches[0].clientY;
    lastTouchY = e.touches[0].clientY;
    targetPrg.value = Math.max(
        0,
        Math.min(cap.value, targetPrg.value + d * 1.1),
    );
    gsap.killTweensOf(progress);
    gsap.to(progress, {
        value: targetPrg.value,
        duration: 0.3,
        ease: 'power2.out',
    });
}

// ─── Rebuild ──────────────────────────────────────────────────────────────────
// (Re)measures the viewport and regenerates the map. Seed is independent of the
// viewport size so the path shape stays identical across zoom / resize, and the
// scroll progress is preserved as a fraction so Pochy stays on the same spot.
function rebuild() {
    const el = rootRef.value;

    if (!el) {
        return;
    }

    const newW = el.offsetWidth;
    const newH = el.offsetHeight;

    if (newW === 0 || newH === 0) {
        return;
    }

    const frac = totalLen.value > 0 ? progress.value / totalLen.value : 0;

    viewW.value = newW;
    viewH.value = newH;

    const rng = mkRng(props.questionCount * 17 + 101); // stable across resizes
    const built = buildMap(props.questionCount, newW, newH, rng);

    segments.value = built.segs;
    cps.value = built.chkps;
    totalLen.value = built.total;
    mapH.value = built.height;
    MAPW.value = built.mapWidth;
    startPt.value = built.startPt;
    endPt.value = built.endPt;

    gsap.killTweensOf(progress);
    progress.value = frac * built.total;
    targetPrg.value = progress.value;
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(() => {
    const el = rootRef.value;

    if (!el) {
        return;
    }

    grassTile.value = generateGrassTile();
    rebuild();

    el.addEventListener('wheel', onWheel, { passive: false });
    el.addEventListener('touchstart', onTouchStart, { passive: true });
    el.addEventListener('touchmove', onTouchMove, { passive: false });

    // Recompute on zoom / window resize so Pochy stays centred on the path
    resizeObserver = new ResizeObserver(() => {
        cancelAnimationFrame(resizeRaf);
        resizeRaf = requestAnimationFrame(rebuild);
    });
    resizeObserver.observe(el);
});

onUnmounted(() => {
    cancelAnimationFrame(resizeRaf);
    gsap.killTweensOf(progress);

    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }

    const el = rootRef.value;

    if (!el) {
        return;
    }

    el.removeEventListener('wheel', onWheel);
    el.removeEventListener('touchstart', onTouchStart);
    el.removeEventListener('touchmove', onTouchMove);
});
</script>

<template>
    <div ref="rootRef" class="map-root">
        <!-- ══════════════════════════════════════════════════════════════
             MOVING WORLD — translates so that curPos stays under Pochy
        ═══════════════════════════════════════════════════════════════ -->
        <div
            class="map-inner"
            :style="{
                width: MAPW + 'px',
                height: mapH + 'px',
                transform: `translate(${mapTX}px,${mapTY}px)`,
                backgroundImage: grassTile ? `url(${grassTile})` : 'none',
                backgroundSize: '32px 32px',
            }"
        >
            <svg
                :width="MAPW"
                :height="mapH"
                xmlns="http://www.w3.org/2000/svg"
                shape-rendering="crispEdges"
            >
                <!-- Layer 2 : path -->
                <GamePath :points="polyPtsStr" :width="PATH_W" />

                <!-- Layer 3 : decorations -->
                <GameDecorations :items="decos" />

                <!-- Layer 4 : start / checkpoints / end -->
                <GameCheckpoint :x="startPt.x" :y="startPt.y" variant="start" />
                <GameCheckpoint
                    v-for="cp in cps"
                    :key="cp.index"
                    :x="cp.x"
                    :y="cp.y"
                    variant="checkpoint"
                    :status="statuses[cp.index] || 'locked'"
                    @select="emit('select', cp.index)"
                />
                <GameCheckpoint :x="endPt.x" :y="endPt.y" variant="end" />
            </svg>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             POCHY — pinned dead-centre, never moves, world moves around it
        ═══════════════════════════════════════════════════════════════ -->
        <div class="pochy-anchor">
            <img src="/img/mascotte.png" alt="Pochy" class="pochy-sprite" />
            <div class="pochy-shadow" />
        </div>
    </div>
</template>

<style scoped>
.map-root {
    position: absolute;
    inset: 0;
    overflow: hidden;
    background: #4a7c45;
}

.map-inner {
    position: absolute;
    top: 0;
    left: 0;
    will-change: transform;
    image-rendering: pixelated; /* keeps grass tile crisp on hi-DPI screens */
    background-color: #4a7c45; /* fallback before tile data-URL is ready */
}

/* Dead-centre, never moves — the world scrolls around Pochy */
.pochy-anchor {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -100%);
    width: 96px;
    pointer-events: none;
    z-index: 20;
}

.pochy-sprite {
    display: block;
    width: 96px;
    height: 96px;
    image-rendering: pixelated;
}

.pochy-shadow {
    width: 64px;
    height: 14px;
    margin: -4px auto 0;
    background: radial-gradient(
        ellipse at center,
        rgba(0, 0, 0, 0.28) 0%,
        transparent 72%
    );
    border-radius: 50%;
}
</style>
