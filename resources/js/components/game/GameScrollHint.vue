<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <div class="scroll-hint" aria-hidden="true">
        <span class="scroll-hint__text">{{ t('eligibilite.ui.scroll_hint') }}</span>
        <svg class="scroll-hint__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9" />
        </svg>
    </div>
</template>

<style scoped>
.scroll-hint {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    color: white;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
    pointer-events: none;
    animation: hint-fade-in 0.4s ease both;
}

.scroll-hint__text {
    font-family: 'Press Start 2P', monospace;
    font-size: clamp(0.5rem, 1.2vw, 0.65rem);
    letter-spacing: 0.03em;
    white-space: nowrap;
}

.scroll-hint__arrow {
    width: 28px;
    height: 28px;
    animation: hint-bounce 1s ease-in-out infinite;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.4));
}

@keyframes hint-bounce {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(6px); }
}

@keyframes hint-fade-in {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
