<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import GameIntro from '@/components/game/GameIntro.vue';
import GameMap from '@/components/game/GameMap.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

const { t } = useI18n();

const props = defineProps({
    questions: Array,
    company: Object,
    token: String,
    // préparation du quiz, à terme à récupérer pour les KPIS
    collectId: Number,
    questions: Array,
});

const phase = ref('intro'); // 'intro' | 'map' | 'question' | 'result' | 'finished'
const answers = ref({}); // questionId → choiceId[] — alimenté par les phases suivantes

const questionsAnswered = computed(() => Object.keys(answers.value).length);
const progressPct = computed(() =>
    props.questions.length > 0
        ? Math.round((questionsAnswered.value / props.questions.length) * 100)
        : 0,
);

function onPlay() {
    phase.value = 'map';
}

// ─── Hauteur exacte sous la navbar ────────────────────────────────────────────
// La hauteur de la navbar varie (≈57px mobile, ≈81px desktop), donc un
// calc(100vh - 64px) déborde et provoque un léger scroll parasite. On mesure
// la position réelle du conteneur et on l'étire jusqu'au bas du viewport.
const containerRef = ref(null);

function fitHeight() {
    const el = containerRef.value;

    if (!el) {
        return;
    }

    const top = el.getBoundingClientRect().top;

    el.style.height = `${window.innerHeight - top}px`;
}

onMounted(() => {
    fitHeight();
    window.addEventListener('resize', fitHeight);
});

onUnmounted(() => {
    window.removeEventListener('resize', fitHeight);
});
</script>

<template>
    <PublicLayout :company="company" :token="token">
        <Head :title="t('eligibilite.title')" />

        <div ref="containerRef" class="game-container">
            <!-- ── Phase intro ────────────────────────────────────────────── -->
            <Transition
                leave-to-class="opacity-0 scale-105"
                leave-active-class="transition-all duration-400 ease-in"
            >
                <GameIntro
                    v-if="phase === 'intro'"
                    :question-count="questions.length"
                    class="game-layer"
                    @play="onPlay"
                />
            </Transition>

            <!-- ── Phases suivantes (à implémenter) ───────────────────────── -->
            <div v-if="phase !== 'intro'" class="game-layer flex flex-col">
                <!-- Barre de progression -->
                <div
                    class="flex items-center gap-3 border-b border-black/10 bg-white/80 px-4 py-2 backdrop-blur-sm"
                >
                    <div class="h-1.5 flex-1 bg-gray-200">
                        <div
                            class="h-full bg-[var(--brand,#7c3aed)] transition-all duration-500"
                            :style="{ width: `${progressPct}%` }"
                        />
                    </div>
                    <span
                        class="shrink-0 font-pixel text-[0.45rem] text-gray-500"
                    >
                        {{ questionsAnswered }} / {{ questions.length }}
                    </span>
                </div>

                <!-- Carte scrollable -->
                <GameMap :question-count="questions.length" class="flex-1" />
            </div>
        </div>
    </PublicLayout>
</template>

<style scoped>
.game-container {
    position: relative;
    height: calc(
        100vh - 64px
    ); /* fallback avant le calcul JS dans fitHeight() */
    overflow: hidden;
}

.game-layer {
    position: absolute;
    inset: 0;
}

.font-pixel {
    font-family: 'Press Start 2P', monospace;
}
</style>
