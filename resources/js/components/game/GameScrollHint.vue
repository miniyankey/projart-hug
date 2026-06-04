<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <div
        class="pointer-events-none flex [animation:hint-fade-in_0.4s_ease_both] flex-col items-center gap-1 text-white [text-shadow:1px_1px_3px_rgba(0,0,0,0.6)]"
        aria-hidden="true"
    >
        <span
            class="font-pixel tracking-[0.03em] whitespace-nowrap"
            style="font-size: clamp(0.5rem, 1.2vw, 0.65rem)"
        >
            {{ t('eligibilite.ui.scroll_hint') }}
        </span>
        <svg
            class="h-7 w-7 [animation:hint-bounce_1s_ease-in-out_infinite] [filter:drop-shadow(0_2px_4px_rgba(0,0,0,0.4))]"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <polyline points="6 9 12 15 18 9" />
        </svg>
    </div>
</template>

<style scoped>
@keyframes hint-bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(6px);
    }
}

@keyframes hint-fade-in {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
