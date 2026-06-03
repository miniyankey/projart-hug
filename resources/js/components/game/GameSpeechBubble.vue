<script setup>
// Bulle de dialogue de Pochy, style Game Boy / RPG (police Press Start 2P).
// La queue (losange bordé) pointe vers la gauche, vers Pochy. Contenu en slot.
</script>

<template>
    <div class="bubble">
        <div class="bubble__body">
            <slot />
        </div>
        <div class="bubble__tail" aria-hidden="true" />
    </div>
</template>

<style scoped>
.bubble {
    position: relative;
    align-self: flex-start;
}

.bubble__body {
    position: relative;
    z-index: 1;
    background: white;
    border: 4px solid #111;
    box-shadow: 6px 6px 0 rgba(0, 0, 0, 0.85);
    padding: 1.75rem 2rem;
    font-family: 'Press Start 2P', monospace;
    font-size: clamp(0.85rem, 1.6vw, 1.35rem);
    line-height: 1.9;
    color: #1a1a1a;
}

/* Losange bordé à moitié rentré sous la bulle : seules les deux faces
   gauches restent visibles (flèche), et la moitié droite — posée AU-DESSUS
   du corps — masque la bordure gauche de la bulle → raccord blanc continu. */
.bubble__tail {
    position: absolute;
    left: -11px;
    bottom: 34px;
    z-index: 2;
    width: 22px;
    height: 22px;
    background: white;
    border-left: 4px solid #111;
    border-bottom: 4px solid #111;
    transform: rotate(45deg);
}
</style>
