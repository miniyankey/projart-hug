<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { update as updateReminder } from '@/actions/App/Http/Controllers/EligibilityReminderController';
import GameFinish from '@/components/game/GameFinish.vue';
import GameIntro from '@/components/game/GameIntro.vue';
import GameLoading from '@/components/game/GameLoading.vue';
import GameMap from '@/components/game/GameMap.vue';
import GameProgressBar from '@/components/game/GameProgressBar.vue';
import GameQuestion from '@/components/game/GameQuestion.vue';
import GameResult from '@/components/game/GameResult.vue';
import { useEligibilityQuiz } from '@/composables/useEligibilityQuiz';
import { useSessionPersistence } from '@/composables/useSessionPersistence';
import { useTracking } from '@/composables/useTracking';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { computeResult, overallVerdict } from '@/lib/eligibility';
import { postJson } from '@/lib/http';
import { home } from '@/routes';
import { collecte as cobrandCollecte } from '@/routes/cobrand';

const props = defineProps({
    company: Object,
    // Slug de la collecte (mode co-brandé) ; null en mode public.
    collectSlug: { type: String, default: null },
    // Présent uniquement en mode co-brandé (collecte active) ; null en mode
    // public → aucun tracking KPI (pour le moment à voir dans le futur)
    collect_id: { type: Number, default: null },
    collect_is_past: { type: Boolean, default: false },
    link_appointment: { type: String, default: null },
});

const { t } = useI18n();
const { questions, ineligibleView } = useEligibilityQuiz();

// « Retour au site » : landing co-brandée de la collecte, ou accueil public.
const siteUrl = computed(() =>
    props.collectSlug && props.company?.slug
        ? cobrandCollecte.url({
              brandName: props.company.slug,
              collect: props.collectSlug,
          })
        : home.url(),
);
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
const resultDays = ref(0); // jours de rappel à proposer sur le résultat (max, 0 sinon)
const resultDonation = ref(false); // proposer le don (inéligibilité à vie) sur le résultat
const isResultIneligible = ref(false); // indique si le résultat courant est une inéligibilité
// Le joueur a déjà soumis un rappel OU cliqué sur le don → on ne le sollicite plus.
const reminderHandled = ref(false);
// Id du rappel enregistré (pour recaler sa date plus tard) + dernière durée envoyée.
const reminderId = ref(null);
const reminderDaysSent = ref(0);
// Chaque sollicitation (rappel temporaire / don à vie) n'est proposée qu'UNE fois
// sur le parcours. On retient la question déclencheuse pour autoriser la
// réaffichage si le joueur revient modifier cette même réponse, pas ailleurs.
const reminderOfferedAt = ref(null);
const donationOfferedAt = ref(null);
// Pochy de la carte : change quand la question s'ouvre (overlay couvre la carte
// → transition invisible), pas quand clearedCount change (carte visible).
const mapPochy = ref(questions.value[0]?.pochy ?? '0');
// Statut de chaque checkpoint : 'locked' | 'eligible' | 'ineligible'
const statuses = ref(questions.value.map(() => 'locked'));

// Construit le panneau de résultat d'une réponse, SANS effet de bord (pas de
// tracking ni de sollicitation). Sert à onAnswer ET à la restauration de la vue
// au retour. Retourne { pochy, view } ou null (réponse éligible sans explication
// → aucun panneau, l'étape se termine directement).
function buildResultPanel(question, choiceIds) {
    const result = computeResult(question, choiceIds);
    const pochy = result.eligible
        ? (question.pochy ?? '0')
        : result.days < 0
          ? '0-sad'
          : '0-time';

    if (result.view) {
        return { pochy, view: result.view };
    }

    if (!result.eligible) {
        return { pochy, view: ineligibleView(result.days) };
    }

    if (question.why_question) {
        return { pochy, view: { message: question.why_question } };
    }

    return null;
}

// ─── Persistance de l'état (mémoire module) ──────────────────────────────────
// Le jeu reste « stateful » à travers la navigation interne SPA : la progression,
// la position de Pochy (gérée par GameMap, cf. storage-key) et la vue en cours
// (question/résultat) sont restaurées au retour. Refresh / nouvel onglet = reset.
// Clé par collecte (ou « public ») pour ne pas mélanger plusieurs parcours.
const STORAGE_KEY = `eligibilite:game:${props.collectSlug ?? 'public'}`;
const { read: readSession, persist: persistSession } =
    useSessionPersistence(STORAGE_KEY);

// Restauration synchrone (au setup) → le premier rendu reflète déjà l'état
// sauvegardé, sans flash de l'écran d'intro.
const saved = readSession();

if (saved && saved.phase && saved.phase !== 'intro') {
    // 'loading' est transitoire → on revient directement sur la carte.
    phase.value = saved.phase === 'loading' ? 'map' : saved.phase;
    answers.value = saved.answers ?? {};
    clearedCount.value = saved.clearedCount ?? 0;
    statuses.value = saved.statuses ?? statuses.value;
    reminderHandled.value = saved.reminderHandled ?? false;
    reminderOfferedAt.value = saved.reminderOfferedAt ?? null;
    donationOfferedAt.value = saved.donationOfferedAt ?? null;
    mapPochy.value = saved.mapPochy ?? mapPochy.value;

    // Vue ouverte au moment de quitter (question, ou résultat reconstruit).
    if (saved.activeIndex != null) {
        activeIndex.value = saved.activeIndex;

        if (saved.resultOpen) {
            const q = questions.value[saved.activeIndex];
            const panel = q
                ? buildResultPanel(q, answers.value[q.id] ?? [])
                : null;

            if (panel) {
                resultPochy.value = panel.pochy;
                resultView.value = panel.view;
            }
        }
    }
}

// Sauvegarde à chaque changement de l'état durable + la vue en cours.
persistSession(
    [
        phase,
        answers,
        clearedCount,
        statuses,
        activeIndex,
        () => resultView.value !== null,
        reminderHandled,
        reminderOfferedAt,
        donationOfferedAt,
        mapPochy,
    ],
    () => ({
        phase: phase.value,
        answers: answers.value,
        clearedCount: clearedCount.value,
        statuses: statuses.value,
        activeIndex: activeIndex.value,
        resultOpen: resultView.value !== null,
        reminderHandled: reminderHandled.value,
        reminderOfferedAt: reminderOfferedAt.value,
        donationOfferedAt: donationOfferedAt.value,
        mapPochy: mapPochy.value,
    }),
);

// Dès qu'une réponse rend inéligible, Pochy reste « vide » (0) sur la carte
// jusqu'à la fin du parcours.
const hasIneligible = computed(() => statuses.value.includes('ineligible'));

// Niveau de remplissage d'une étape (ex. "20-travel" → "20").
function fillFor(index) {
    return (questions.value[index]?.pochy ?? '0').split('-')[0];
}

// Étape la plus avancée atteinte (frontière) → niveau « maximal » de Pochy.
const frontierIndex = computed(() =>
    Math.min(clearedCount.value, questions.value.length - 1),
);

// Pochy de la carte : vide si inéligible, sinon le niveau maximal atteint. Si le
// joueur corrige une réponse et redevient éligible, il « récupère » son sang.
function refreshMapPochy() {
    mapPochy.value = hasIneligible.value ? '0' : fillFor(frontierIndex.value);
}

// À l'ouverture d'une question, on réaligne Pochy (changement masqué par
// l'overlay qui couvre la carte).
watch(activeIndex, (idx) => {
    if (idx !== null) {
        refreshMapPochy();
    }
});

const activeQuestion = computed(() =>
    activeIndex.value !== null ? questions.value[activeIndex.value] : null,
);

// Numéro de l'étape en cours (1-based), cohérent entre question et résultat.
const currentStep = computed(() => (activeIndex.value ?? 0) + 1);

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

// ─── Tracking KPI (fire & forget) ─────────────────────────────────────────────
// Aligné sur l'agrégation du dashboard (KpiController) : un seul `result` par
// session, porté par l'étape complétée (= verdict global), `null` ailleurs. Les
// étapes intermédiaires alimentent le drop-off (sessions par étape). L'envoi est
// idempotent (updateOrCreate côté serveur), donc « Changer ma réponse » est sûr.
// En mode public, collect_id est null → session rattachée au funnel global.
function trackStep() {
    const step = activeIndex.value + 1; // étapes 1-indexées côté KPI
    const isFinal = step === questions.value.length;

    // Sur l'étape finale uniquement : verdict global, replié sur les deux valeurs
    // comptées par le dashboard (eligible / ineligible).
    let result = null;

    if (isFinal) {
        const verdict = overallVerdict(questions.value, answers.value);
        result = verdict.status === 'eligible' ? 'eligible' : 'ineligible';
    }

    trackEligibiliteStep(props.collect_id, step, {
        result,
        completed: isFinal,
    });
}

// Clic sur le CTA d'inscription en fin de parcours (conversion don de sang).
// Source 'jeu' pour matcher l'agrégation du dashboard (KpiController).
function onAppointment() {
    trackAppointmentClick(props.collect_id, 'jeu');
}

// Rappel enregistré : on retient son id (pour recaler sa date si les réponses
// suivantes changent la durée max) et on ne sollicite plus ensuite.
function onReminderSubmitted(id) {
    reminderHandled.value = true;
    reminderId.value = id ?? null;
    reminderDaysSent.value = resultDays.value;
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

    // Pochy de la carte : vide si inéligible, sinon niveau maximal atteint. Si le
    // joueur corrige une réponse et redevient éligible, il récupère son sang ;
    // s'il devient inéligible, il se vide (changement masqué par le panneau de
    // résultat qui couvre la carte).
    refreshMapPochy();

    trackStep();

    // Sollicitation rappel/don sur le panneau de résultat. Pilotée par le verdict
    // GLOBAL (toutes les réponses), pas seulement la question courante :
    //  - inéligible à vie un jour → toujours le don (jamais le rappel) ;
    //  - sinon, inéligibilité temporaire → rappel avec la plus grande durée ;
    //  - déjà sollicité (rappel envoyé ou don cliqué) ou réponse éligible → rien.
    const overall = overallVerdict(questions.value, answers.value);

    // On ne sollicite que si la réponse courante est inéligible et que le joueur
    // n'a pas déjà donné suite (rappel envoyé ou clic sur le don).
    const canSolicit = !reminderHandled.value && !result.eligible;

    // À vie → don financier, jamais de rappel. Temporaire → rappel.
    const wantsDonation = canSolicit && overall.status === 'lifetime';
    const wantsReminder =
        canSolicit && overall.status === 'temporary' && overall.days > 0;

    // « 1 seule fois » : on n'affiche que si jamais proposé, ou si on est de
    // retour sur la question qui l'avait déclenché (« Changer ma réponse »).
    const showDonation =
        wantsDonation &&
        (donationOfferedAt.value === null ||
            donationOfferedAt.value === question.id);
    const showReminder =
        wantsReminder &&
        (reminderOfferedAt.value === null ||
            reminderOfferedAt.value === question.id);

    if (showDonation) {
        donationOfferedAt.value = question.id;
    }

    if (showReminder) {
        reminderOfferedAt.value = question.id;
    }

    resultDonation.value = showDonation;
    resultDays.value = showReminder ? overall.days : 0;

    // Rappel déjà enregistré : si une réponse ultérieure change la durée
    // d'inéligibilité max, on recale sa date côté serveur (fire & forget).
    if (
        reminderId.value !== null &&
        overall.status === 'temporary' &&
        overall.days > 0 &&
        overall.days !== reminderDaysSent.value
    ) {
        reminderDaysSent.value = overall.days;
        postJson(updateReminder.url(reminderId.value), { days: overall.days });
    }

    isResultIneligible.value = !result.eligible;

    // Vue de résultat (Pochy + panneau) ; null → étape terminée directement.
    const panel = buildResultPanel(question, choiceIds);

    if (panel) {
        resultPochy.value = panel.pochy;
        resultView.value = panel.view;

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
    <PublicLayout
        :company="company"
        :collect-slug="collectSlug"
        :collect-is-past="collect_is_past"
        :link-appointment="link_appointment"
        hide-footer
    >
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
                    :icons="questions.map((q) => q.icon)"
                    :pochy="mapPochy"
                    :storage-key="STORAGE_KEY"
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
                    :answered="currentStep"
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
                    :answered="currentStep"
                    :total="questions.length"
                    :icon="activeQuestion?.icon ?? null"
                    :pochy="resultPochy"
                    :reminder-days="resultDays"
                    :donation="resultDonation"
                    :collect-id="collect_id"
                    :is-ineligible="isResultIneligible"
                    class="game-layer"
                    @ok="onResultOk"
                    @back="onResultBack"
                    @handled="reminderHandled = true"
                    @reminder-submitted="onReminderSubmitted"
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
                    :site-url="siteUrl"
                    :reminder-scheduled="reminderId !== null"
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
    /* dvh : suit la barre d'URL mobile (100vh déborderait → bande blanche).
       Fallback avant le calcul JS de fitHeight(). */
    height: calc(100dvh - 64px);
    overflow: hidden;
}

.game-layer {
    position: absolute;
    inset: 0;
    z-index: 40;
}

/* Sous les overlays (z-index 40), au-dessus de la carte → masque la carte
   pendant le fondu entre les écrans question et résultat. */
.game-backdrop {
    position: absolute;
    inset: 0;
    z-index: 40;
    background: white;
}
</style>
