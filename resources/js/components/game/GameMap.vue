<script setup>
import { gsap } from 'gsap';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import GameCheckpoint from './GameCheckpoint.vue';
import GameDecorations from './GameDecorations.vue';
import GamePath from './GamePath.vue';
import GamePochy from './GamePochy.vue';
import GameScrollHint from './GameScrollHint.vue';
import { memoryGet, memorySet } from '@/composables/useSessionPersistence';
import {
    buildDecos,
    buildMap,
    buildZones,
    generateAsphaltTile,
    generateGroundTile,
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
    // URL de l'icône thématique par checkpoint (index = question)
    icons: { type: Array, default: () => [] },
    // Variante de la mascotte (suffixe du fichier /img/pochy/pochy-<variant>.webp)
    pochy: { type: String, default: '0' },
    // Clé de persistance (mémoire module) : on y garde la position de Pochy pour
    // la restaurer à travers la navigation interne SPA. Vide → pas de persistance.
    storageKey: { type: String, default: '' },
});

// Icônes de début et de fin de parcours
const START_ICON = '/img/game/deco/start-flag.svg';
const END_ICON = '/img/game/deco/grand-hospital.svg';

const emit = defineEmits(['reach', 'select', 'ready', 'finish']);

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
const groundTile = ref(''); // data-URL generated at mount via Canvas
const asphaltTile = ref(''); // data-URL pour le goudron des quartiers
const hasMoved = ref(false);
const moving = ref(false); // Pochy se déplace → animation de marche

let lastTouchY = 0;
let resizeObserver = null;
let resizeRaf = 0;
let progressSaveTimer = null;

// Position de Pochy persistée (débouncée pour ne pas écrire à chaque frame).
function persistProgress() {
    if (!props.storageKey) {
        return;
    }

    clearTimeout(progressSaveTimer);
    progressSaveTimer = setTimeout(() => {
        memorySet(`${props.storageKey}:progress`, progress.value);
    }, 200);
}
let moveTimer = null;

// L'indice « Scrolle pour avancer » n'apparaît qu'au lancement : dès le premier
// déplacement, il disparaît définitivement (pas de réapparition à l'inactivité).
function markMoved() {
    hasMoved.value = true;
}

// Pochy « marche » tant que sa position change ; on coupe l'animation après une
// courte inactivité (le mouvement est terminé). On en profite pour persister sa
// position (débouncé).
watch(progress, () => {
    moving.value = true;
    clearTimeout(moveTimer);
    moveTimer = setTimeout(() => {
        moving.value = false;
    }, 140);
    persistProgress();
});

// ─── Computed ────────────────────────────────────────────────────────────────
const curPos = computed(() => smoothPos(segments.value, progress.value));
const mapTX = computed(() => Math.round(viewW.value / 2 - curPos.value.x));
const mapTY = computed(() => Math.round(viewH.value / 2 - curPos.value.y));
const polyPtsStr = computed(() => polyPts(segments.value)); // built once, used by 4 polylines
const zones = computed(() =>
    buildZones(
        MAPW.value,
        mapH.value,
        segments.value,
        props.questionCount + 77,
    ),
);
const decos = computed(() =>
    buildDecos(
        segments.value,
        MAPW.value,
        mapH.value,
        props.questionCount + 77,
        cps.value,
        props.icons,
        zones.value,
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
// Emit `finish` once when Pochy reaches the very end (after all questions answered).
const stopReached = ref(false);
const endReached = ref(false);

watch(progress, (val) => {
    if (props.clearedCount >= cps.value.length) {
        // All checkpoints answered — detect arrival at the end of the path
        if (!endReached.value && val >= totalLen.value - 0.5) {
            endReached.value = true;
            emit('finish');
        } else if (endReached.value && val < totalLen.value - 100) {
            endReached.value = false;
        }

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
    gsap.to(progress, {
        value: targetPrg.value,
        duration: 0.6,
        ease: 'power3.out',
    });
    markMoved();
}

// Clic sur un checkpoint :
// - déjà répondu (index < clearedCount) → réouvre sa question ;
// - verrouillé (le prochain ou plus loin) → anime Pochy jusqu'au prochain
//   checkpoint atteignable (cap), ce qui ouvre sa question à l'arrivée. Permet
//   de jouer sans la molette.
function onCheckpointSelect(index) {
    if (index < props.clearedCount) {
        emit('select', index);

        return;
    }

    // Pochy est déjà arrivé sur le prochain checkpoint → (ré)ouvre directement sa
    // question (sinon, comme progress = cap, l'animation ne déclencherait pas `reach`).
    if (progress.value >= cap.value - 0.5) {
        emit('reach', props.clearedCount);

        return;
    }

    targetPrg.value = cap.value;
    gsap.killTweensOf(progress);
    gsap.to(progress, {
        value: cap.value,
        duration: 1.2,
        ease: 'power2.inOut',
    });
    markMoved();
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

// ─── Deco preloading ──────────────────────────────────────────────────────────
const DECO_NAMES = [
    // Bâtiments / ville
    'building-a',
    'building-b',
    'house',
    'kiosk',
    'market-stall',
    // Clins d'œil thématiques
    'noticeboard',
    'fountain',
    'pharmacy-shop',
    'travel-shop',
    'hospital-building',
    'dentist-shop',
    'park-plot',
    'tattoo-shop',
    'clinic',
    // Végétation
    'oak',
    'pine',
    'bush',
    'flower',
    'rock',
    'mushroom',
    // Lac Léman
    'sailboat',
    'jet-deau',
    // Repères genevois (droite / nature)
    'train-station',
    'stadium',
    'un-building',
    'flag-swiss',
    'flag-geneva',
    // En vol
    'plane',
    'eagle',
    // Animaux
    'dog',
    'cat',
    'bird',
];

function preloadDecos() {
    return Promise.all(
        DECO_NAMES.map(
            (name) =>
                new Promise((resolve) => {
                    const img = new Image();
                    img.onload = resolve;
                    img.onerror = resolve; // ne pas bloquer si un asset est absent
                    img.src = `/img/game/deco/${name}.svg`;
                }),
        ),
    );
}

// Restaure la position de Pochy sauvegardée (navigation interne SPA). On
// neutralise le déclenchement automatique de `reach`/`finish` au point restauré :
// la vue en cours (question/résultat) est déjà rétablie côté page, on ne veut pas
// la réécraser.
function restoreProgress() {
    if (!props.storageKey) {
        return;
    }

    const saved = Number(memoryGet(`${props.storageKey}:progress`));

    if (!Number.isFinite(saved) || saved <= 0) {
        return;
    }

    progress.value = Math.max(0, Math.min(saved, cap.value));
    targetPrg.value = progress.value;

    if (progress.value >= cap.value - 0.5) {
        stopReached.value = true;
    }

    if (
        props.clearedCount >= cps.value.length &&
        progress.value >= totalLen.value - 0.5
    ) {
        endReached.value = true;
    }
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────
const LOADING_MIN_MS = 600;

onMounted(() => {
    const el = rootRef.value;

    if (!el) {
        return;
    }

    const t0 = Date.now();
    groundTile.value = generateGroundTile();
    asphaltTile.value = generateAsphaltTile();
    rebuild();
    restoreProgress();

    preloadDecos().then(() => {
        const elapsed = Date.now() - t0;
        const delay = Math.max(0, LOADING_MIN_MS - elapsed);
        setTimeout(() => emit('ready'), delay);
    });

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
    clearTimeout(moveTimer);
    clearTimeout(progressSaveTimer);
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
                backgroundImage: groundTile ? `url(${groundTile})` : 'none',
                backgroundSize: '32px 32px',
            }"
        >
            <svg
                :width="MAPW"
                :height="mapH"
                xmlns="http://www.w3.org/2000/svg"
                shape-rendering="crispEdges"
            >
                <defs>
                    <pattern
                        v-if="asphaltTile"
                        id="asphalt-tile"
                        width="32"
                        height="32"
                        patternUnits="userSpaceOnUse"
                    >
                        <image :href="asphaltTile" width="32" height="32" />
                    </pattern>
                </defs>

                <!-- Layer 1 : zones de sol (Léman + quartiers), derrière le chemin -->
                <g>
                    <template v-for="(z, zi) in zones" :key="`z-${zi}`">
                        <!-- Quartier bétonné (goudron texturé) -->
                        <template v-if="z.kind === 'district'">
                            <!-- Bordure de terre (transition avec l'herbe) -->
                            <rect
                                :x="z.x - 5"
                                :y="z.y - 5"
                                :width="z.w + 10"
                                :height="z.h + 10"
                                fill="#7a5a32"
                            />
                            <!-- Goudron texturé -->
                            <rect
                                :x="z.x"
                                :y="z.y"
                                :width="z.w"
                                :height="z.h"
                                :fill="
                                    asphaltTile
                                        ? 'url(#asphalt-tile)'
                                        : '#7c7e83'
                                "
                            />
                            <!-- Liseré de trottoir clair -->
                            <rect
                                :x="z.x + 5"
                                :y="z.y + 5"
                                :width="z.w - 10"
                                :height="z.h - 10"
                                fill="none"
                                stroke="#9a9ca1"
                                stroke-width="3"
                                opacity="0.6"
                            />
                        </template>
                        <!-- Voie ferrée CFF -->
                        <template v-else-if="z.kind === 'rail'">
                            <!-- Ballast (gravier) -->
                            <rect
                                :x="z.cx - 16"
                                :y="0"
                                width="32"
                                :height="z.h"
                                fill="#9a948c"
                            />
                            <rect
                                :x="z.cx - 16"
                                :y="0"
                                width="32"
                                :height="z.h"
                                :fill="
                                    asphaltTile
                                        ? 'url(#asphalt-tile)'
                                        : '#9a948c'
                                "
                                opacity="0.35"
                            />
                            <!-- Traverses -->
                            <rect
                                v-for="ty in Math.floor(z.h / 24)"
                                :key="`tie-${zi}-${ty}`"
                                :x="z.cx - 13"
                                :y="ty * 24 - 18"
                                width="26"
                                height="5"
                                fill="#6b4a2a"
                            />
                            <!-- Rails acier -->
                            <rect
                                :x="z.cx - 8"
                                :y="0"
                                width="3"
                                :height="z.h"
                                fill="#4a4a4e"
                            />
                            <rect
                                :x="z.cx + 5"
                                :y="0"
                                width="3"
                                :height="z.h"
                                fill="#4a4a4e"
                            />
                            <rect
                                :x="z.cx - 8"
                                :y="0"
                                width="1"
                                :height="z.h"
                                fill="#7a7a80"
                            />
                            <rect
                                :x="z.cx + 5"
                                :y="0"
                                width="1"
                                :height="z.h"
                                fill="#7a7a80"
                            />
                        </template>
                        <!-- Le Léman (berge organique) -->
                        <template v-else>
                            <!-- Liseré de rive (terre/sable) -->
                            <polyline
                                :points="z.shore"
                                fill="none"
                                stroke="#7a5a32"
                                stroke-width="11"
                                stroke-linejoin="round"
                                stroke-linecap="round"
                            />
                            <!-- Plan d'eau -->
                            <polygon :points="z.points" fill="#2f93c4" />
                            <!-- Bas-fond plus clair près de la rive -->
                            <polyline
                                :points="z.shore"
                                fill="none"
                                stroke="#5bb6e6"
                                stroke-width="7"
                                stroke-linejoin="round"
                                stroke-linecap="round"
                                opacity="0.6"
                            />
                            <!-- Reflets / ondulations -->
                            <rect
                                v-for="(t, ti) in z.ripples"
                                :key="`zw-${zi}-${ti}`"
                                :x="t.dx"
                                :y="t.dy"
                                width="7"
                                height="2"
                                fill="#7ec3e8"
                                opacity="0.7"
                            />
                        </template>
                    </template>
                </g>

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

                <!-- Layer 5 : icônes thématiques au-dessus des checkpoints -->
                <image
                    :href="START_ICON"
                    :x="startPt.x - 32"
                    :y="startPt.y - 104"
                    width="64"
                    height="64"
                />
                <image
                    v-for="cp in cps"
                    v-show="icons[cp.index]"
                    :key="`ic-${cp.index}`"
                    :href="icons[cp.index]"
                    :x="cp.x - 36"
                    :y="cp.y - 110"
                    width="72"
                    height="72"
                    class="cursor-pointer"
                    @click="onCheckpointSelect(cp.index)"
                />
                <image
                    :href="END_ICON"
                    :x="endPt.x - 40"
                    :y="endPt.y - 106"
                    width="80"
                    height="66"
                />
            </svg>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             POCHY — pinned dead-centre, never moves, world moves around it
        ═══════════════════════════════════════════════════════════════ -->
        <div class="pochy-anchor">
            <GamePochy
                :variant="pochy"
                class="pochy-sprite"
                :class="{ 'pochy-walk': moving }"
            />
            <div
                class="pochy-shadow"
                :class="{ 'pochy-shadow--walk': moving }"
            />
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
    background: #6aae3f;
}

.map-inner {
    position: absolute;
    top: 0;
    left: 0;
    will-change: transform;
    image-rendering: pixelated; /* keeps ground tile crisp on hi-DPI screens */
    background-color: #6aae3f; /* fallback before tile data-URL is ready */
}

/* Dead-centre, never moves — the world scrolls around Pochy */
.pochy-anchor {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -100%);
    width: 128px;
    pointer-events: none;
    z-index: 20;
}

.pochy-sprite {
    display: block;
    width: 128px;
    height: 128px;
    image-rendering: pixelated;
    transform-origin: bottom center;
}

/* Sautillement de marche : bond + léger écrasement à l'atterrissage. */
.pochy-walk {
    animation: pochy-hop 0.5s ease-in-out infinite;
}

@keyframes pochy-hop {
    0%,
    100% {
        transform: translateY(0) scale(1, 1);
    }
    25% {
        transform: translateY(-10px) scale(0.97, 1.05);
    }
    55% {
        transform: translateY(0) scale(1.05, 0.95);
    }
    75% {
        transform: translateY(-3px) scale(1, 1);
    }
}

.pochy-shadow {
    width: 84px;
    height: 16px;
    margin: -4px auto 0;
    background: radial-gradient(
        ellipse at center,
        rgba(0, 0, 0, 0.28) 0%,
        transparent 72%
    );
    border-radius: 50%;
    transform-origin: center;
}

/* L'ombre se rétrécit quand Pochy saute, grandit à l'atterrissage. */
.pochy-shadow--walk {
    animation: pochy-shadow-hop 0.5s ease-in-out infinite;
}

@keyframes pochy-shadow-hop {
    0%,
    100% {
        transform: scale(1);
        opacity: 1;
    }
    25% {
        transform: scale(0.7);
        opacity: 0.6;
    }
    55% {
        transform: scale(1.05);
        opacity: 1;
    }
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
