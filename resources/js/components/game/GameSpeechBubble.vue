<script setup>
// Bulle de dialogue de Pochy, style Game Boy / RPG (police Press Start 2P).
// La queue (losange bordé) pointe vers la gauche, vers Pochy. Contenu en slot.
</script>

<template>
    <div class="relative self-start">
        <div
            class="relative z-[1] border-4 border-[#111] bg-white px-8 py-7 font-pixel text-[clamp(0.85rem,1.6vw,1.35rem)] leading-[1.9] text-[#1a1a1a] shadow-[6px_6px_0_rgba(0,0,0,0.85)]"
        >
            <slot />
        </div>
        <!-- Losange bordé à moitié rentré sous la bulle : seules les deux faces
             gauches restent visibles (flèche), la moitié droite masque la bordure. -->
        <div
            class="absolute bottom-[34px] -left-[11px] z-[2] h-[22px] w-[22px] rotate-45 border-b-4 border-l-4 border-[#111] bg-white"
            aria-hidden="true"
        />
    </div>
</template>
