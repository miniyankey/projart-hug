<script setup>
// Écran de fin du jeu — affiché après la dernière question.
// Éligible → message positif + CTA inscription.
// Inéligible → verdict + récap pixel des étapes inéligibles (titre, réponse, durée).
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import GamePochy from './GamePochy.vue';
import GameProgressBar from './GameProgressBar.vue';
import GameSpeechBubble from './GameSpeechBubble.vue';
import { Button } from '@/components/ui/button';
import { useEligibilityQuiz } from '@/composables/useEligibilityQuiz';

const props = defineProps({
    // { status: 'eligible'|'temporary'|'lifetime', days, steps: [{ titre, answers, days }] }
    verdict: { type: Object, required: true },
    total: { type: Number, default: 0 },
    // Lien d'inscription (cobrand) ou null (→ lien HUG public)
    linkAppointment: { type: String, default: null },
    // URL de retour vers le site (landing co-brandée ou accueil public)
    siteUrl: { type: String, required: true },
});

defineEmits(['back', 'appointment']);

const { t } = useI18n();
const { formatDuration } = useEligibilityQuiz();

const isEligible = computed(() => props.verdict.status === 'eligible');

const pochy = computed(() => {
    if (props.verdict.status === 'lifetime') {
        return '0-sad';
    }

    if (props.verdict.status === 'temporary') {
        return '0-time';
    }

    return '100-hand-in-air';
});

const title = computed(() =>
    t(`eligibilite.finish.${props.verdict.status}.title`),
);

const message = computed(() => {
    if (isEligible.value) {
        return t('eligibilite.finish.eligible.message');
    }

    if (props.verdict.status === 'lifetime') {
        return t('eligibilite.finish.lifetime.message');
    }

    // Durée globale indéterminée (ex. vaccin « je ne sais pas ») → message
    // sans durée, qui invite à vérifier.
    if (props.verdict.days === null) {
        return t('eligibilite.finish.temporary.message_unknown');
    }

    return t('eligibilite.finish.temporary.message', {
        duration: formatDuration(props.verdict.days),
    });
});

const text = computed(() =>
    t(
        `eligibilite.finish.${isEligible.value ? 'eligible' : props.verdict.status}.text`,
    ),
);

const appointmentUrl = computed(
    () =>
        props.linkAppointment ||
        'https://www.hug.ch/don-du-sang/rendez-vous-ligne',
);

// Durée d'une étape : « à vie » si days < 0, « à vérifier » si durée
// indéterminée (null), sinon durée formatée.
function stepDuration(step) {
    if (step.days === null) {
        return t('eligibilite.finish.unknown_label');
    }

    return step.days < 0
        ? t('eligibilite.finish.lifetime.label')
        : formatDuration(step.days);
}

// ─── Partage ───────────────────────────────────────────────────────────────────
const shareCopied = ref(false);

async function share() {
    const url = window.location.href;
    const data = {
        title: t('eligibilite.title'),
        text: t('eligibilite.finish.share.text'),
        url,
    };

    if (navigator.share) {
        try {
            await navigator.share(data);
        } catch {
            /* partage annulé par l'utilisateur */
        }

        return;
    }

    try {
        await navigator.clipboard.writeText(url);
        shareCopied.value = true;
        setTimeout(() => {
            shareCopied.value = false;
        }, 2500);
    } catch {
        /* presse-papiers indisponible */
    }
}
</script>

<template>
    <div class="absolute inset-0 z-50 flex flex-col bg-white">
        <GameProgressBar :value="total" :total="total" />

        <div class="flex min-h-0 flex-1 overflow-y-auto">
            <div
                class="flex w-full flex-col gap-5 px-4 pt-[6vh] pb-8 sm:px-6 md:flex-row md:items-start md:gap-[clamp(1rem,4vw,3rem)] md:px-10 md:pt-[8vh]"
            >
                <!-- Pochy + actions -->
                <div
                    class="flex shrink-0 flex-col items-center gap-6 md:basis-1/3"
                >
                    <GamePochy
                        :variant="pochy"
                        class="w-40 drop-shadow-[0_10px_18px_rgba(0,0,0,0.3)] [image-rendering:pixelated] md:w-[80%]"
                    />

                    <!-- Actions -->
                    <div
                        class="flex w-full flex-col items-stretch gap-3 px-2 md:max-w-xs"
                    >
                        <Button
                            v-if="isEligible"
                            variant="pixel_violet"
                            as="a"
                            class="h-auto px-7 py-3 text-[1.05rem]"
                            :href="appointmentUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            @click="$emit('appointment')"
                        >
                            {{ t('eligibilite.finish.eligible.cta') }}
                        </Button>
                        <Button
                            variant="pixel_white"
                            class="h-auto border-[3px] border-black px-7 py-3 text-[1.05rem] text-black"
                            @click="share"
                        >
                            {{
                                shareCopied
                                    ? t('eligibilite.finish.share.copied')
                                    : t('eligibilite.finish.share.cta')
                            }}
                        </Button>
                        <Button
                            as-child
                            variant="pixel_white"
                            class="h-auto border-[3px] border-black px-7 py-3 text-[1.05rem] text-black"
                        >
                            <Link :href="siteUrl">
                                {{ t('eligibilite.finish.back_to_site') }}
                            </Link>
                        </Button>
                        <Button
                            variant="link"
                            class="h-auto px-4 py-3 text-[1.05rem]"
                            @click="$emit('back')"
                        >
                            {{ t('eligibilite.finish.back_to_game') }}
                        </Button>
                    </div>
                </div>

                <!-- Contenu -->
                <div class="flex min-w-0 flex-col gap-5 md:basis-2/3">
                    <!-- Titre pixel -->
                    <h2
                        class="m-0 font-pixel text-[clamp(1.1rem,3vw,2rem)] leading-[1.4] text-[var(--brand,#7c3aed)] uppercase [text-shadow:3px_3px_0_rgba(0,0,0,0.18)]"
                    >
                        {{ title }}
                    </h2>

                    <GameSpeechBubble>{{ message }}</GameSpeechBubble>

                    <p class="m-0 text-[1rem] leading-relaxed text-neutral-800">
                        {{ text }}
                    </p>

                    <!-- Récap des étapes inéligibles (cartes pixel) -->
                    <div
                        v-if="!isEligible && verdict.steps.length > 0"
                        class="flex flex-col gap-3"
                    >
                        <p
                            class="m-0 font-pixel text-[0.7rem] tracking-wide text-neutral-500 uppercase"
                        >
                            {{ t('eligibilite.finish.recap_title') }}
                        </p>
                        <div
                            v-for="step in verdict.steps"
                            :key="step.questionKey"
                            class="border-[3px] border-black bg-white p-3 shadow-[5px_5px_0_rgba(0,0,0,0.85)]"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <span
                                    class="font-pixel text-[0.72rem] leading-[1.5] text-neutral-900 uppercase"
                                >
                                    {{ step.titre }}
                                </span>
                                <span
                                    class="shrink-0 border-2 border-black bg-[var(--brand,#7c3aed)] px-2 py-1 text-[0.7rem] font-bold whitespace-nowrap text-white"
                                >
                                    {{ stepDuration(step) }}
                                </span>
                            </div>
                            <p
                                v-if="step.answers.length"
                                class="mt-2 mb-0 text-left text-[0.9rem] text-neutral-600"
                            >
                                {{ step.answers.join(', ') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
