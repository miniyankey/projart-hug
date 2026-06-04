<script setup>
// Coquille plein écran partagée par la question et le résultat :
// barre de progression, thématique + compteur, Pochy + icône, et 3 slots
// (#bubble = bulle de Pochy, #content = zone centrale, #footer = pied).
import { ref, watch } from 'vue';
import GameProgressBar from './GameProgressBar.vue';

const props = defineProps({
    theme: { type: String, default: '' },
    answered: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    icon: { type: String, default: null },
});

const iconError = ref(false);

watch(
    () => props.icon,
    () => {
        iconError.value = false;
    },
);
</script>

<template>
    <div class="scene">
        <GameProgressBar :value="answered" :total="total" />

        <div class="scene__content">
            <!-- Thématique + compteur -->
            <div class="scene__top">
                <p class="scene__theme">{{ theme }}</p>
                <p class="scene__counter">{{ answered }}/{{ total }}</p>
            </div>

            <!-- Corps : personnage à gauche, bulle + contenu à droite -->
            <div class="scene__main">
                <div class="scene__character">
                    <img
                        v-if="icon && !iconError"
                        :src="icon"
                        alt=""
                        class="scene__icon"
                        @error="iconError = true"
                    />
                    <div
                        v-else
                        class="scene__icon scene__icon--fallback"
                        aria-hidden="true"
                    >
                        ?
                    </div>

                    <img
                        src="/img/mascotte.png"
                        alt="Pochy"
                        class="scene__pochy"
                    />
                </div>

                <div class="scene__right">
                    <slot name="bubble" />
                    <div class="scene__panel">
                        <slot name="content" />
                    </div>
                </div>
            </div>

            <!-- Pied -->
            <div class="scene__footer">
                <slot name="footer" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.scene {
    position: absolute;
    inset: 0;
    z-index: 50; /* au-dessus de Pochy de la carte (pochy-anchor z-index 20) */
    display: flex;
    flex-direction: column;
    background: white;
}

.scene__content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.5rem clamp(1.5rem, 4vw, 4rem) 2rem;
    min-height: 0;
}

/* ── Thématique + compteur ─────────────────────────────────────── */
.scene__top {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 1rem;
}

.scene__theme {
    margin: 0;
    font-size: clamp(0.8rem, 1.4vw, 1.1rem);
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #6b7280;
}

.scene__counter {
    margin: 0;
    font-size: clamp(0.9rem, 1.5vw, 1.25rem);
    color: #9ca3af;
    white-space: nowrap;
}

/* ── Corps ──────────────────────────────────────────────────────── */
.scene__main {
    flex: 1;
    display: flex;
    align-items: center;
    gap: clamp(1rem, 4vw, 3rem);
    min-height: 0;
}

.scene__character {
    position: relative;
    flex-shrink: 0;
    width: clamp(200px, 26vw, 380px);
    height: clamp(240px, 32vw, 440px);
    align-self: center;
}

.scene__icon {
    position: absolute;
    top: 0;
    left: 0;
    width: 62%;
    image-rendering: pixelated;
}

.scene__icon--fallback {
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 1;
    background: #f3f4f6;
    border: 4px solid #111;
    box-shadow: 6px 6px 0 rgba(0, 0, 0, 0.85);
    font-family: 'Press Start 2P', monospace;
    font-size: clamp(2rem, 4vw, 3.5rem);
    color: #9ca3af;
}

.scene__pochy {
    position: absolute;
    left: 16%;
    bottom: 0;
    width: 66%;
    image-rendering: pixelated;
    z-index: 2;
    filter: drop-shadow(0 10px 18px rgba(0, 0, 0, 0.35));
}

.scene__right {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: clamp(1.5rem, 4vh, 3rem);
    min-height: 0;
}

.scene__panel {
    min-height: 0;
    overflow-y: auto;
}

/* ── Pied ───────────────────────────────────────────────────────── */
.scene__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 1rem;
}

</style>
