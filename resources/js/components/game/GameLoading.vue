<script setup>
import { useI18n } from 'vue-i18n';
import GamePochy from './GamePochy.vue';

const { t } = useI18n();
</script>

<template>
    <div class="loading">
        <GamePochy variant="0" class="loading__pochy" />
        <p class="loading__text">{{ t('eligibilite.ui.loading') }}</p>
        <div class="loading__bar" aria-hidden="true">
            <div class="loading__fill" />
        </div>
    </div>
</template>

<style scoped>
.loading {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    background: var(--brand, #7c3aed);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect x='0' y='0' width='2' height='2' fill='%23ffffff26'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%23ffffff26'/%3E%3Crect x='0' y='2' width='2' height='2' fill='%2300000026'/%3E%3Crect x='2' y='0' width='2' height='2' fill='%2300000026'/%3E%3C/svg%3E");
    background-size: 4px 4px;
    image-rendering: pixelated;
}

.loading__pochy {
    width: clamp(100px, 20vw, 180px);
    image-rendering: pixelated;
    animation: pochy-bounce 0.8s ease-in-out infinite alternate;
    filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.3));
}

.loading__text {
    margin: 0;
    font-family: 'Press Start 2P', monospace;
    font-size: clamp(0.55rem, 1.4vw, 0.75rem);
    color: white;
    letter-spacing: 0.05em;
    text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.3);
}

.loading__bar {
    width: clamp(160px, 30vw, 280px);
    height: 12px;
    border: 3px solid white;
    box-shadow: 3px 3px 0 rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.loading__fill {
    height: 100%;
    background: white;
    animation: bar-fill 0.9s ease-in-out infinite alternate;
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
