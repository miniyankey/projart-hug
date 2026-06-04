<script setup>
// Écran de fin du jeu — affiché après la dernière question.
// Éligible → message positif + CTA inscription.
// Inéligible → verdict (durée max) + liste récap des étapes inéligibles.
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import GamePochy from './GamePochy.vue';
import GameProgressBar from './GameProgressBar.vue';
import GameSpeechBubble from './GameSpeechBubble.vue';
import { Button } from '@/components/ui/button';

const props = defineProps({
    // { status: 'eligible'|'temporary'|'lifetime', days: number|null, steps: [...] }
    verdict: { type: Object, required: true },
    total: { type: Number, default: 0 },
    // Lien d'inscription (cobrand) ou null (→ lien HUG public)
    linkAppointment: { type: String, default: null },
});

defineEmits(['back', 'appointment']);

const { t } = useI18n();

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

const appointmentUrl = computed(
    () =>
        props.linkAppointment ||
        'https://www.hug.ch/don-du-sang/rendez-vous-ligne',
);

function formatDays(days) {
    if (days === null || days < 0) {
        return null;
    }

    if (days >= 365) {
        return t('eligibilite.duration.year', Math.round(days / 365));
    }

    if (days >= 30) {
        return t('eligibilite.duration.month', Math.round(days / 30));
    }

    if (days >= 7) {
        return t('eligibilite.duration.week', Math.round(days / 7));
    }

    return t('eligibilite.duration.day', days);
}
</script>

<template>
    <div class="absolute inset-0 z-50 flex flex-col bg-white">
        <GameProgressBar :value="total" :total="total" />

        <!-- Corps -->
        <div
            class="flex min-h-0 flex-1 items-center gap-[clamp(1rem,4vw,3rem)]"
            :style="{ padding: '1.5rem clamp(1.5rem, 4vw, 4rem) 2rem' }"
        >
            <!-- Personnage -->
            <div
                class="flex shrink-0 items-end justify-center"
                style="width: clamp(160px, 22vw, 320px)"
            >
                <GamePochy
                    :variant="pochy"
                    class="w-full drop-shadow-[0_10px_18px_rgba(0,0,0,0.3)] [image-rendering:pixelated]"
                />
            </div>

            <!-- Contenu -->
            <div
                class="flex min-w-0 flex-1 flex-col gap-[clamp(1.5rem,4vh,3rem)]"
            >
                <GameSpeechBubble>
                    {{
                        isEligible
                            ? t('eligibilite.finish.eligible.message')
                            : verdict.status === 'lifetime'
                              ? t('eligibilite.finish.lifetime.message')
                              : t('eligibilite.finish.temporary.message', {
                                    duration: formatDays(verdict.days),
                                })
                    }}
                </GameSpeechBubble>

                <div class="flex min-h-0 flex-col gap-5 overflow-y-auto">
                    <!-- Éligible -->
                    <template v-if="isEligible">
                        <p
                            class="m-0 text-[1.05rem] leading-relaxed text-gray-900"
                        >
                            {{ t('eligibilite.finish.eligible.text') }}
                        </p>
                        <Button
                            variant="pixel_violet"
                            as="a"
                            class="self-start"
                            :href="appointmentUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            @click="$emit('appointment')"
                        >
                            {{ t('eligibilite.finish.eligible.cta') }}
                        </Button>
                    </template>

                    <!-- Inéligible -->
                    <template v-else>
                        <p
                            class="m-0 text-[1.05rem] leading-relaxed text-gray-900"
                        >
                            {{
                                verdict.status === 'lifetime'
                                    ? t('eligibilite.finish.lifetime.text')
                                    : t('eligibilite.finish.temporary.text')
                            }}
                        </p>

                        <!-- Récap des étapes inéligibles -->
                        <ul
                            v-if="verdict.steps.length > 0"
                            class="m-0 flex list-none flex-col gap-2 p-0"
                        >
                            <li
                                v-for="step in verdict.steps"
                                :key="step.questionKey"
                                class="flex items-baseline justify-between gap-4 border-2 border-l-4 border-gray-200 border-l-red-500 px-[0.9rem] py-[0.6rem]"
                            >
                                <span class="text-[0.95rem] font-semibold">
                                    {{ step.titre }}
                                </span>
                                <span
                                    v-if="step.days && step.days > 0"
                                    class="text-[0.85rem] whitespace-nowrap text-red-500"
                                >
                                    {{ formatDays(step.days) }}
                                </span>
                                <span
                                    v-else-if="step.days < 0"
                                    class="text-[0.85rem] font-bold whitespace-nowrap text-red-500"
                                >
                                    {{ t('eligibilite.finish.lifetime.label') }}
                                </span>
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </div>

        <!-- Pied -->
        <div
            class="flex justify-start border-t border-gray-200"
            :style="{ padding: '0.75rem clamp(1.5rem, 4vw, 4rem)' }"
        >
            <Button variant="link" @click="$emit('back')">
                {{ t('eligibilite.finish.back_to_game') }}
            </Button>
        </div>
    </div>
</template>
