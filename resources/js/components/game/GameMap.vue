<script setup>
import { gsap } from 'gsap';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import GameCheckpoint from './GameCheckpoint.vue';
import GameDecorations from './GameDecorations.vue';
import GamePath from './GamePath.vue';
import GamePochy from './GamePochy.vue';
import GameScrollHint from './GameScrollHint.vue';
import {
    buildDecos,
    buildMap,
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
    // Variante de la mascotte (suffixe du fichier /img/pochy/pochy-<variant>.png)
    pochy: { type: String, default: '0' },
});

const emit = defineEmits(['reach', 'select', 'ready']);

// ─── Constants ───────────────────────────────────────────────────────────────
const PATH_W = 32; // dirt road stroke width

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
const hasMoved = ref(false);

let lastTouchY = 0;
let resizeObserver = null;
let resizeRaf = 0;
let inactivityTimer = null;

function markMoved() {
    hasMoved.value = true;
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        hasMoved.value = false;
    }, 4000);
}

// ─── Computed ────────────────────────────────────────────────────────────────
const curPos = computed(() => smoothPos(segments.value, progress.value));
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
    markMoved();
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
    markMoved();
}

function onKeyDown(e) {
    const STEP = 120;
    let delta = 0;

    if (e.key === 'ArrowDown' || e.key === ' ' || e.key === 'PageDown') {
        delta = STEP;
    } else if (e.key === 'ArrowUp' || e.key === 'PageUp') {
        delta = -STEP;
    } else {
        return;
    }

    e.preventDefault();
    targetPrg.value = Math.max(0, Math.min(cap.value, targetPrg.value + delta));
    gsap.killTweensOf(progress);
    gsap.to(progress, { value: targetPrg.value, duration: 0.6, ease: 'power3.out' });
    markMoved();
}

// Clic sur un checkpoint : si c'est le prochain à franchir, anime Pochy jusqu'à
// lui ; sinon retransmet l'événement (checkpoint déjà répondu → réouvre question).
function onCheckpointSelect(index) {
    if (index === props.clearedCount) {
        targetPrg.value = cap.value;
        gsap.killTweensOf(progress);
        gsap.to(progress, { value: cap.value, duration: 1.2, ease: 'power2.inOut' });
        markMoved();
    } else {
        emit('select', index);
    }
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
const LOADING_MIN_MS = 600;

onMounted(() => {
    const el = rootRef.value;

    if (!el) {
        return;
    }

    const t0 = Date.now();
    grassTile.value = generateGrassTile();
    rebuild();

    const elapsed = Date.now() - t0;
    const delay = Math.max(0, LOADING_MIN_MS - elapsed);
    setTimeout(() => emit('ready'), delay);

    el.addEventListener('wheel', onWheel, { passive: false });
    el.addEventListener('touchstart', onTouchStart, { passive: true });
    el.addEventListener('touchmove', onTouchMove, { passive: false });
    el.addEventListener('keydown', onKeyDown);

    // Recompute on zoom / window resize so Pochy stays centred on the path
    resizeObserver = new ResizeObserver(() => {
        cancelAnimationFrame(resizeRaf);
        resizeRaf = requestAnimationFrame(rebuild);
    });
    resizeObserver.observe(el);
});

onUnmounted(() => {
    cancelAnimationFrame(resizeRaf);
    clearTimeout(inactivityTimer);
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
    el.removeEventListener('keydown', onKeyDown);
});
</script>

<template>
    <div ref="rootRef" class="map-root" tabindex="0">
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
                    @select="onCheckpointSelect(cp.index)"
                />
                <GameCheckpoint :x="endPt.x" :y="endPt.y" variant="end" />
            </svg>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             POCHY — pinned dead-centre, never moves, world moves around it
        ═══════════════════════════════════════════════════════════════ -->
        <div class="pochy-anchor">
            <GamePochy :variant="pochy" class="pochy-sprite" />
            <div class="pochy-shadow" />
        </div>

        <!-- Indice de scroll — visible tant que l'utilisateur n'a pas bougé -->
        <Transition
            enter-from-class="opacity-0"
            enter-active-class="transition-opacity duration-300"
            leave-to-class="opacity-0"
            leave-active-class="transition-opacity duration-200"
        >
            <div v-if="!hasMoved" class="scroll-hint-anchor">
                <GameScrollHint />
            </div>
        </Transition>
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

/* Indice scroll — centré horizontalement, juste sous Pochy */
.scroll-hint-anchor {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, 12px);
    pointer-events: none;
    z-index: 21;
}
</style>
