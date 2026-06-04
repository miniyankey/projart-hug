<script setup>
import { useI18n } from 'vue-i18n';
import GamePochy from './GamePochy.vue';

const { t } = useI18n();
</script>

<template>
    <div
        class="loading-bg absolute inset-0 flex flex-col items-center justify-center gap-6"
    >
        <GamePochy
            variant="0"
            class="[animation:pochy-bounce_0.8s_ease-in-out_infinite_alternate] drop-shadow-[0_8px_16px_rgba(0,0,0,0.3)] [image-rendering:pixelated]"
            style="width: clamp(100px, 20vw, 180px)"
        />

        <p
            class="m-0 font-pixel tracking-[0.05em] text-white [text-shadow:2px_2px_0_rgba(0,0,0,0.3)]"
            style="font-size: clamp(0.55rem, 1.4vw, 0.75rem)"
        >
            {{ t('eligibilite.ui.loading') }}
        </p>

        <div
            class="overflow-hidden border-[3px] border-white shadow-[3px_3px_0_rgba(0,0,0,0.3)]"
            style="width: clamp(160px, 30vw, 280px); height: 12px"
        >
            <div
                class="h-full [animation:bar-fill_0.9s_ease-in-out_infinite_alternate] bg-white"
            />
        </div>
    </div>
</template>

<style scoped>
/* Fond pixel-art aux couleurs de l'entreprise — irréductible en Tailwind (data URL + var CSS). */
.loading-bg {
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

@keyframes pochy-bounce {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-12px);
    }
}

@keyframes bar-fill {
    from {
        width: 20%;
    }
    to {
        width: 90%;
    }
}
</style>
