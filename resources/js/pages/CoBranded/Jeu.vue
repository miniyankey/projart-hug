<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import GameFinish from '@/components/game/GameFinish.vue';
import GameIntro from '@/components/game/GameIntro.vue';
import GameLoading from '@/components/game/GameLoading.vue';
import GameMap from '@/components/game/GameMap.vue';
import GameProgressBar from '@/components/game/GameProgressBar.vue';
import GameQuestion from '@/components/game/GameQuestion.vue';
import GameResult from '@/components/game/GameResult.vue';
import { useEligibilityQuiz } from '@/composables/useEligibilityQuiz';
import { useTracking } from '@/composables/useTracking';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { computeResult, overallVerdict } from '@/lib/eligibility';

const props = defineProps({
    company: Object,
    token: String,
    // Présent uniquement en mode co-brandé (collecte active) ; null en mode
    // public → aucun tracking KPI (pour le moment à voir dans le futur)
    collect_id: { type: Number, default: null },
    link_appointment: { type: String, default: null },
});

const { t } = useI18n();
const { questions, ineligibleView } = useEligibilityQuiz();
const { trackEligibiliteStep, trackAppointmentClick } = useTracking();

// 'intro' | 'map' | 'finished'. Les écrans question/résultat sont
// des overlays pilotés par activeIndex / resultView, pas par `phase`.
const phase = ref('intro');

const verdict = computed(() =>
    phase.value === 'finished'
        ? overallVerdict(questions.value, answers.value)
        : null,
);

// Réponses (id question → id choix[]), progression et écrans actifs
const answers = ref({});
const clearedCount = ref(0); // checkpoints franchis (= frontière de progression)
const activeIndex = ref(null); // question actuellement ouverte (ou null)
const resultView = ref(null); // vue de résultat affichée (ou null)
const resultPochy = ref('0'); // variante Pochy de l'écran de résultat courant
// Pochy de la carte : change quand la question s'ouvre (overlay couvre la carte
// → transition invisible), pas quand clearedCount change (carte visible).
const mapPochy = ref(questions.value[0]?.pochy ?? '0');
// Statut de chaque checkpoint : 'locked' | 'eligible' | 'ineligible'
const statuses = ref(questions.value.map(() => 'locked'));

watch(activeIndex, (idx) => {
    if (idx !== null) {
        // On la carte, on n'utilise que le niveau de remplissage (ex. "20-travel" → "20")
        const pochy = questions.value[idx]?.pochy ?? '0';
        mapPochy.value = pochy.split('-')[0];
    }
});

const activeQuestion = computed(() =>
    activeIndex.value !== null ? questions.value[activeIndex.value] : null,
);

function onPlay() {
    phase.value = 'loading';
}

function onMapReady() {
    if (phase.value === 'loading') {
        phase.value = 'map';
    }
}

function onMapFinish() {
    phase.value = 'finished';
}

// Pochy atteint un checkpoint → on affiche sa question
function onReach(index) {
    activeIndex.value = index;
    resultView.value = null;
}

// Clic sur un checkpoint déjà répondu → réaffiche sa question (pré-remplie)
function onSelectCheckpoint(index) {
    activeIndex.value = index;
    resultView.value = null;
}

// Ferme question/résultat. Ne libère le checkpoint suivant que si l'on répond à
// la frontière (pas lors de la modification d'une réponse passée).
function finishStep() {
    if (activeIndex.value === clearedCount.value) {
        clearedCount.value += 1;
    }

    resultView.value = null;
    activeIndex.value = null;
}

// ─── Tracking KPI (fire & forget, mode co-brandé uniquement) ──────────────────
// rend l'envoi idempotent (re-réponse via « Changer ma réponse » incluse).
function resultLabel(result) {
    if (result.eligible) {
        return 'eligible';
    }

    return result.days < 0 ? 'ineligible_lifetime' : 'ineligible';
}

function trackStep(result) {
    if (!props.collect_id) {
        return;
    }

    const step = activeIndex.value + 1; // étapes 1-indexées côté KPI

    trackEligibiliteStep(props.collect_id, step, {
        result: resultLabel(result),
        completed: step === questions.value.length,
    });
}

// Clic sur le CTA d'inscription en fin de parcours (conversion don de sang)
function onAppointment() {
    if (props.collect_id) {
        trackAppointmentClick(props.collect_id, 'game-end-cta');
    }
}

// Validation d'une réponse → enregistrement, statut du checkpoint, puis :
// vue dédiée du choix › vue générique d'inéligibilité › explication (why) › suite.
function onAnswer(choiceIds) {
    const question = activeQuestion.value;

    if (!question) {
        return;
    }

    answers.value[question.id] = choiceIds;

    const result = computeResult(question, choiceIds);

    statuses.value[activeIndex.value] = result.eligible
        ? 'eligible'
        : 'ineligible';

    trackStep(result);

    // Pochy du résultat : triste/temporaire si inéligible, sinon celui de la question
    if (!result.eligible) {
        resultPochy.value = result.days < 0 ? '0-sad' : '0-time';
    } else {
        resultPochy.value = question.pochy ?? '0';
    }

    if (result.view) {
        resultView.value = result.view;

        return;
    }

    if (!result.eligible) {
        resultView.value = ineligibleView(result.days);

        return;
    }

    if (question.why_question) {
        resultView.value = { message: question.why_question };

        return;
    }

    finishStep();
}

// « OK » du résultat termine l'étape ; « Retour » réaffiche la question
function onResultOk() {
    finishStep();
}

function onResultBack() {
    resultView.value = null;
}

// « Retour » de la question → ferme sans valider (Pochy reste sur le checkpoint)
function onBack() {
    activeIndex.value = null;
}

// ─── Hauteur exacte sous la navbar ────────────────────────────────────────────
// La navbar varie en hauteur (≈57px mobile, ≈81px desktop) ; on étire le
// conteneur jusqu'au bas du viewport pour éviter tout scroll parasite.
const containerRef = ref(null);

function fitHeight() {
    const el = containerRef.value;

    if (!el) {
        return;
    }

    el.style.height = `${window.innerHeight - el.getBoundingClientRect().top}px`;
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
    <PublicLayout :company="company" :token="token" hide-footer>
        <Head :title="t('eligibilite.title')" />

        <div ref="containerRef" class="game-container">
            <!-- Phase intro -->
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

            <!-- Carte montée dès le loading et conservée jusqu'à la fin pour conserver l'état -->
            <div
                v-if="
                    phase === 'loading' ||
                    phase === 'map' ||
                    phase === 'finished'
                "
                class="game-layer flex flex-col"
            >
                <GameProgressBar
                    :value="clearedCount"
                    :total="questions.length"
                />

                <GameMap
                    :question-count="questions.length"
                    :cleared-count="clearedCount"
                    :statuses="statuses"
                    :pochy="mapPochy"
                    class="flex-1"
                    @reach="onReach"
                    @select="onSelectCheckpoint"
                    @ready="onMapReady"
                    @finish="onMapFinish"
                />
            </div>

            <!-- Overlay de chargement (par-dessus la carte pendant l'initialisation) -->
            <Transition
                leave-to-class="opacity-0"
                leave-active-class="transition-opacity duration-300"
            >
                <GameLoading
                    v-if="phase === 'loading'"
                    class="game-layer z-30"
                />
            </Transition>

            <!-- Fond blanc persistant : évite d'apercevoir la carte pendant le
                 fondu croisé question ⇄ résultat -->
            <div
                v-if="activeIndex !== null || resultView"
                class="game-backdrop"
            />

            <!-- Overlay question -->
            <Transition
                enter-from-class="opacity-0"
                enter-active-class="transition-opacity duration-200"
                leave-to-class="opacity-0"
                leave-active-class="transition-opacity duration-150"
            >
                <GameQuestion
                    v-if="activeQuestion && !resultView"
                    :key="activeQuestion.id"
                    :question="activeQuestion"
                    :pre-selected="answers[activeQuestion.id] ?? []"
                    :answered="clearedCount"
                    :total="questions.length"
                    :icon="activeQuestion.icon"
                    class="game-layer"
                    @answer="onAnswer"
                    @back="onBack"
                />
            </Transition>

            <!-- Overlay résultat -->
            <Transition
                enter-from-class="opacity-0"
                enter-active-class="transition-opacity duration-200"
                leave-to-class="opacity-0"
                leave-active-class="transition-opacity duration-150"
            >
                <GameResult
                    v-if="resultView"
                    :view="resultView"
                    :theme="activeQuestion?.titre ?? ''"
                    :answered="clearedCount"
                    :total="questions.length"
                    :icon="activeQuestion?.icon ?? null"
                    :pochy="resultPochy"
                    class="game-layer"
                    @ok="onResultOk"
                    @back="onResultBack"
                />
            </Transition>

            <!-- Phase fin -->
            <Transition
                enter-from-class="opacity-0 translate-y-4"
                enter-active-class="transition-all duration-400"
            >
                <GameFinish
                    v-if="phase === 'finished' && verdict"
                    :verdict="verdict"
                    :total="questions.length"
                    :link-appointment="props.link_appointment"
                    class="game-layer"
                    @appointment="onAppointment"
                    @back="phase = 'map'"
                />
            </Transition>
        </div>
    </PublicLayout>
</template>

<style scoped>
.game-container {
    position: relative;
    height: calc(100vh - 64px); /* fallback avant le calcul JS de fitHeight() */
    overflow: hidden;
}

.game-layer {
    position: absolute;
    inset: 0;
}

/* Sous les overlays (z-index 50), au-dessus de la carte → masque la carte
   pendant le fondu entre les écrans question et résultat. */
.game-backdrop {
    position: absolute;
    inset: 0;
    z-index: 40;
    background: white;
}
</style>
