<script setup>
import { useI18n } from 'vue-i18n';
import GamePochy from './GamePochy.vue';
import { Button } from '@/components/ui/button';

defineProps({
    questionCount: Number,
});

const emit = defineEmits(['play']);

const { t } = useI18n();
</script>

<template>
    <div
        class="intro-bg flex flex-col justify-between overflow-hidden px-6 py-8 sm:px-10 sm:py-12"
    >
        <!-- Titre + badge de réassurance -->
        <div class="flex flex-col items-start gap-4">
            <h1
                class="max-w-[22ch] font-pixel text-[clamp(0.8rem,2.4vw,1.5rem)] leading-[1.8] text-white uppercase [text-shadow:3px_3px_0_rgba(0,0,0,0.45)]"
            >
                {{ t('eligibilite.ui.intro_title') }}
            </h1>

            <div
                class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-medium text-neutral-800 shadow-[3px_3px_0_rgba(0,0,0,0.25)]"
            >
                <span>{{ t('eligibilite.ui.badge_duration') }}</span>
                <span class="text-neutral-400">•</span>
                <span>{{
                    t('eligibilite.ui.badge_questions', { count: questionCount })
                }}</span>
                <span class="text-neutral-400">•</span>
                <span>{{ t('eligibilite.ui.badge_no_commitment') }}</span>
            </div>
        </div>

        <!-- Pochy sur sa plateforme -->
        <div class="flex flex-1 items-center justify-center">
            <div class="relative flex flex-col items-center">
                <GamePochy
                    variant="0"
                    class="relative z-10 w-[clamp(180px,30vh,300px)] drop-shadow-[0_14px_22px_rgba(0,0,0,0.3)]"
                />
                <!-- Zone arrondie sous Pochy : ses pieds reposent dessus -->
                <div
                    class="-mt-[7%] h-[clamp(26px,5vh,50px)] w-[clamp(210px,34vh,340px)] rounded-[50%] border-4 border-white/90 bg-[var(--brand,#7c3aed)]"
                />
            </div>
        </div>

        <!-- CTA — blanc sur fond coloré pour rester lisible -->
        <Button
            variant="pixel_white"
            class="mx-auto font-pixel text-[clamp(0.85rem,1.6vw,1.1rem)] tracking-wider px-12 py-3.5"
            @click="emit('play')"
        >
            {{ t('eligibilite.ui.play') }}
        </Button>
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
