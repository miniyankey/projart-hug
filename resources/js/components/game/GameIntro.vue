<script setup>
import { useI18n } from 'vue-i18n';
import GamePochy from './GamePochy.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

defineProps({
    questionCount: Number,
});

const emit = defineEmits(['play']);

const { t } = useI18n();
</script>

<template>
    <div
        class="intro-bg relative flex flex-col overflow-hidden px-6 py-8 sm:px-10 sm:py-12"
    >
        <!-- Titre + badge de réassurance -->
        <div class="flex flex-col items-start gap-4">
            <h1
                class="max-w-[22ch] font-pixel text-[clamp(0.8rem,2.4vw,1.5rem)] leading-[1.8] text-white uppercase [text-shadow:3px_3px_0_rgba(0,0,0,0.45)]"
            >
                {{ t('eligibilite.ui.intro_title') }}
            </h1>

            <div class="flex flex-wrap items-center gap-2">
                <Badge
                    variant="pixel"
                    class="border-black bg-white text-[var(--brand,#7c3aed)] shadow-[3px_3px_0px_0px_rgba(0,0,0,0.35)]"
                >
                    {{ t('eligibilite.ui.badge_duration') }}
                </Badge>
                <Badge
                    variant="pixel"
                    class="border-black bg-white text-[var(--brand,#7c3aed)] shadow-[3px_3px_0px_0px_rgba(0,0,0,0.35)]"
                >
                    {{
                        t('eligibilite.ui.badge_questions', {
                            count: questionCount,
                        })
                    }}
                </Badge>
                <Badge
                    variant="pixel"
                    class="border-black bg-white text-[var(--brand,#7c3aed)] shadow-[3px_3px_0px_0px_rgba(0,0,0,0.35)]"
                >
                    {{ t('eligibilite.ui.badge_no_commitment') }}
                </Badge>
            </div>
        </div>

        <!-- Pochy sur sa plateforme — remonté vers le haut de l'écran -->
        <div class="mt-2 flex items-center justify-center sm:mt-4">
            <div class="relative flex flex-col items-center">
                <GamePochy
                    variant="0"
                    class="relative z-10 w-[clamp(240px,38vh,420px)] drop-shadow-[0_14px_22px_rgba(0,0,0,0.3)]"
                />
                <!-- Zone arrondie sous Pochy : ses pieds reposent dessus -->
                <div
                    class="-mt-[7%] h-[clamp(32px,6vh,60px)] w-[clamp(270px,46vh,470px)] rounded-[50%] border-4 border-white/90 bg-[var(--brand,#7c3aed)]"
                />
            </div>
        </div>

        <!-- CTA -->
        <div class="mt-5 flex justify-center sm:mt-6">
            <Button
                variant="pixel_white"
                class="px-12 py-3.5 font-pixel text-[clamp(0.85rem,1.6vw,1.1rem)] tracking-wider"
                @click="emit('play')"
            >
                {{ t('eligibilite.ui.play') }}
            </Button>
        </div>

        <!-- Réassurance confidentialité — ancrée en bas à droite -->
        <p
            class="absolute right-4 bottom-3 max-w-[34ch] text-right text-xs leading-relaxed text-white/80 [text-shadow:1px_1px_0_rgba(0,0,0,0.35)] sm:right-6 sm:bottom-4 sm:text-sm"
        >
            {{ t('eligibilite.ui.intro_privacy') }}
        </p>
    </div>
</template>

<style scoped>
/* Fond pixel-art aux couleurs de l'entreprise : dégradé brand → brand-shadow
   (nuance plus foncée) + dithering (data-URL). Irréductible en Tailwind. */
.intro-bg {
    background-color: var(--brand, #7c3aed);
    background-image:
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect x='0' y='0' width='2' height='2' fill='%23ffffff26'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%23ffffff26'/%3E%3Crect x='0' y='2' width='2' height='2' fill='%2300000026'/%3E%3Crect x='2' y='0' width='2' height='2' fill='%2300000026'/%3E%3C/svg%3E"),
        linear-gradient(
            180deg,
            var(--brand, #7c3aed) 0%,
            var(--brand-shadow, #4c1d95) 100%
        );
    background-size:
        4px 4px,
        cover;
    image-rendering: pixelated;
}
</style>
