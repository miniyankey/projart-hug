<script setup>
// Coquille plein écran partagée par la question et le résultat.
// Slots : #bubble (bulle de Pochy), #content (zone centrale), #footer (pied).
// Layout mobile : vertical (personnage → bulle → contenu → pied).
// Layout desktop (md+) : horizontal (colonne personnage | colonne contenu).
import { ref, watch } from 'vue';
import GamePochy from './GamePochy.vue';
import GameProgressBar from './GameProgressBar.vue';

const props = defineProps({
    theme: { type: String, default: '' },
    answered: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    icon: { type: String, default: null },
    pochy: { type: String, default: '0' },
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
    <div class="absolute inset-0 z-50 flex flex-col bg-white">
        <GameProgressBar :value="answered" :total="total" />

        <!-- Header : thématique + compteur (plus gros, plus d'air sous la barre) -->
        <div
            class="flex shrink-0 items-baseline justify-between gap-4 px-4 pt-6 sm:px-6 md:px-10 md:pt-8"
        >
            <p
                class="m-0 text-[clamp(1rem,2.2vw,1.6rem)] font-bold tracking-wide text-gray-700 uppercase"
            >
                {{ theme }}
            </p>
            <p
                class="m-0 text-[clamp(0.95rem,1.8vw,1.35rem)] font-semibold whitespace-nowrap text-gray-400"
            >
                {{ answered }}/{{ total }}
            </p>
        </div>

        <!-- Corps principal (scrollable), descendu de ~10vh pour mieux centrer.
             Pas de `flex` ici : sinon l'enfant est étiré à la hauteur visible et
             son padding-bas est « avalé » par le contenu qui déborde. -->
        <div class="min-h-0 flex-1 overflow-y-auto">
            <div
                class="flex w-full flex-col gap-4 px-4 pt-[10vh] pb-12 sm:px-5 sm:pb-12 md:flex-row md:items-start md:gap-[clamp(1rem,4vw,3rem)] md:px-[clamp(1.5rem,4vw,4rem)] md:pt-[10vh] md:pb-[clamp(3rem,5vw,5rem)]"
            >
                <!-- Zone personnage : 1/3 sur desktop ; icône + Pochy centrés.
                     Mobile : empilés et centrés, plus gros. -->
                <div
                    class="flex shrink-0 flex-col items-center md:relative md:block md:h-[clamp(260px,32vw,460px)] md:basis-1/3 md:self-center"
                >
                    <!-- Icône thématique -->
                    <img
                        v-if="icon && !iconError"
                        :src="icon"
                        alt=""
                        class="h-28 w-28 shrink-0 [image-rendering:pixelated] md:absolute md:top-0 md:left-1/2 md:h-auto md:w-[62%] md:-translate-x-1/2"
                        @error="iconError = true"
                    />
                    <div
                        v-else
                        class="flex h-28 w-28 shrink-0 items-center justify-center border-4 border-gray-800 bg-gray-100 font-pixel text-2xl text-gray-400 shadow-[4px_4px_0_rgba(0,0,0,0.7)] md:absolute md:top-0 md:left-1/2 md:aspect-square md:h-auto md:w-[62%] md:-translate-x-1/2"
                        aria-hidden="true"
                    >
                        ?
                    </div>

                    <!-- Pochy : sur mobile, remonte (marge négative) pour chevaucher
                         l'icône ; sur desktop, positionné en absolu. -->
                    <GamePochy
                        :variant="pochy"
                        class="relative z-10 -mt-20 h-40 w-40 shrink-0 drop-shadow-[0_6px_12px_rgba(0,0,0,0.3)] [image-rendering:pixelated] md:absolute md:bottom-0 md:left-1/2 md:z-[2] md:mt-0 md:h-auto md:w-[86%] md:-translate-x-1/2 md:drop-shadow-[0_10px_18px_rgba(0,0,0,0.35)]"
                    />
                </div>

                <!-- Colonne contenu : 2/3 sur desktop (boutons d'action inclus) -->
                <div class="flex min-w-0 flex-col gap-7 md:basis-2/3">
                    <slot name="bubble" />
                    <slot name="content" />
                </div>
            </div>
        </div>
    </div>
</template>
