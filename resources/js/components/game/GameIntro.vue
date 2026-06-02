<script setup>
defineProps({
    questionCount: Number,
});

const emit = defineEmits(['play']);
</script>

<template>
    <div class="intro">
        <!-- Title + badges -->
        <div class="intro__top">
            <h1 class="intro__title">
                POUVEZ-VOUS<br />
                DONNER VOTRE<br />
                SANG ?<br />
                FAITES LE CHEMIN<br />
                AVEC POCHY<br />
                POUR LE DECOUVRIR.
            </h1>
            <div class="intro__badges">
                <span class="intro__badge">2 mn</span>
                <span class="intro__sep">•</span>
                <span class="intro__badge">{{ questionCount }} questions</span>
                <span class="intro__sep">•</span>
                <span class="intro__badge">Sans engagement</span>
            </div>
        </div>

        <!-- Pochy + platform -->
        <div class="intro__character">
            <img src="/img/mascotte.png" alt="Pochy" class="intro__pochy" />
            <div class="intro__platform" />
        </div>

        <!-- CTA -->
        <button class="intro__btn" @click="emit('play')">JOUER</button>
    </div>
</template>

<style scoped>
/* ── Container + fond pixel art gris ────────────────────────────── */
.intro {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2rem 2rem 2.5rem;

    background-color: #666;
    background-image:
        radial-gradient(ellipse at 50% 45%, #999 0%, #2a2a2a 85%),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect x='0' y='0' width='2' height='2' fill='%23ffffff14'/%3E%3Crect x='2' y='2' width='2' height='2' fill='%23ffffff14'/%3E%3Crect x='0' y='2' width='2' height='2' fill='%2300000018'/%3E%3Crect x='2' y='0' width='2' height='2' fill='%2300000018'/%3E%3C/svg%3E");
    background-size:
        cover,
        4px 4px;
}

/* ── Title + badges ─────────────────────────────────────────────── */
.intro__top {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.intro__title {
    font-family: 'Press Start 2P', monospace;
    font-size: clamp(0.55rem, 1.8vw, 0.85rem);
    line-height: 2;
    color: white;
    text-shadow:
        3px 3px 0 rgba(0, 0, 0, 0.6),
        -1px -1px 0 rgba(0, 0, 0, 0.3);
}

.intro__badges {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border: 2px solid black;
    box-shadow: 3px 3px 0 black;
    padding: 0.4rem 1rem;
    border-radius: 2px;
    align-self: flex-start;
}

.intro__badge {
    font-size: 0.72rem;
    font-weight: 600;
    color: #1a1a1a;
    white-space: nowrap;
}

.intro__sep {
    color: #9ca3af;
    font-size: 0.8rem;
}

/* ── Pochy + platform ───────────────────────────────────────────── */
.intro__character {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    justify-content: center;
}

.intro__pochy {
    width: clamp(160px, 32vh, 240px);
    height: clamp(160px, 32vh, 240px);
    object-fit: contain;
    image-rendering: pixelated;
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.6));
    display: block;
}

/* Zone arrondie lumineuse sous Pochy */
.intro__platform {
    width: clamp(120px, 24vh, 180px);
    height: clamp(22px, 3.5vh, 34px);
    background: radial-gradient(
        ellipse at center,
        rgba(255, 255, 255, 0.3) 0%,
        rgba(255, 255, 255, 0.06) 65%,
        transparent 85%
    );
    border-radius: 50%;
    margin-top: -12px;
    position: relative;
    z-index: 0;
}

/* ── Bouton JOUER ───────────────────────────────────────────────── */
.intro__btn {
    align-self: center;
    font-family: 'Press Start 2P', monospace;
    font-size: 1rem;
    letter-spacing: 0.1em;
    color: white;
    background: var(--brand, #7c3aed);
    border: 3px solid black;
    box-shadow: 5px 5px 0 black;
    padding: 0.85rem 3rem;
    cursor: pointer;
    transition:
        transform 80ms ease,
        box-shadow 80ms ease;
}

.intro__btn:hover {
    transform: translate(2px, 2px);
    box-shadow: 3px 3px 0 black;
}

.intro__btn:active {
    transform: translate(5px, 5px);
    box-shadow: 0 0 0 black;
}
</style>
