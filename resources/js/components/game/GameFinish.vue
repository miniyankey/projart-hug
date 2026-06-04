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
    <div class="finish">
        <GameProgressBar :value="total" :total="total" />

        <div class="finish__content">
            <div class="finish__character">
                <GamePochy :variant="pochy" class="finish__pochy" />
            </div>

            <div class="finish__right">
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

                <div class="finish__panel">
                    <!-- Éligible -->
                    <template v-if="isEligible">
                        <p class="finish__text">
                            {{ t('eligibilite.finish.eligible.text') }}
                        </p>
                        <Button
                            variant="pixel_violet"
                            as="a"
                            :href="appointmentUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            {{ t('eligibilite.finish.eligible.cta') }}
                        </Button>
                    </template>

                    <!-- Inéligible -->
                    <template v-else>
                        <p class="finish__text">
                            {{
                                verdict.status === 'lifetime'
                                    ? t('eligibilite.finish.lifetime.text')
                                    : t('eligibilite.finish.temporary.text')
                            }}
                        </p>

                        <!-- Récap des étapes inéligibles -->
                        <ul
                            v-if="verdict.steps.length > 0"
                            class="finish__steps"
                        >
                            <li
                                v-for="step in verdict.steps"
                                :key="step.questionKey"
                                class="finish__step"
                            >
                                <span class="finish__step-title">{{
                                    step.titre
                                }}</span>
                                <span
                                    v-if="step.days && step.days > 0"
                                    class="finish__step-days"
                                >
                                    {{ formatDays(step.days) }}
                                </span>
                                <span
                                    v-else-if="step.days < 0"
                                    class="finish__step-days finish__step-days--lifetime"
                                >
                                    {{ t('eligibilite.finish.lifetime.label') }}
                                </span>
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.finish {
    position: absolute;
    inset: 0;
    z-index: 50;
    display: flex;
    flex-direction: column;
    background: white;
}

.finish__content {
    flex: 1;
    display: flex;
    align-items: center;
    gap: clamp(1rem, 4vw, 3rem);
    padding: 1.5rem clamp(1.5rem, 4vw, 4rem) 2rem;
    min-height: 0;
}

.finish__character {
    flex-shrink: 0;
    width: clamp(160px, 22vw, 320px);
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.finish__pochy {
    width: 100%;
    image-rendering: pixelated;
    filter: drop-shadow(0 10px 18px rgba(0, 0, 0, 0.3));
}

.finish__right {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: clamp(1.5rem, 4vh, 3rem);
}

.finish__panel {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    overflow-y: auto;
}

.finish__text {
    margin: 0;
    font-size: 1.05rem;
    line-height: 1.6;
    color: #1f2937;
}

.finish__steps {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.finish__step {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 1rem;
    padding: 0.6rem 0.9rem;
    border: 2px solid #e5e7eb;
    border-left: 4px solid #ef4444;
}

.finish__step-title {
    font-weight: 600;
    font-size: 0.95rem;
}

.finish__step-days {
    font-size: 0.85rem;
    color: #ef4444;
    white-space: nowrap;
}

.finish__step-days--lifetime {
    font-weight: 700;
}
</style>
