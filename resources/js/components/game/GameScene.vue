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

        <!-- Header : thématique + compteur -->
        <div
            class="flex shrink-0 items-baseline justify-between gap-4 px-4 pt-3 sm:px-6 md:px-10"
        >
            <p
                class="m-0 text-[clamp(0.75rem,1.3vw,1rem)] font-semibold tracking-wider text-gray-500 uppercase"
            >
                {{ theme }}
            </p>
            <p
                class="m-0 text-[clamp(0.8rem,1.4vw,1.1rem)] whitespace-nowrap text-gray-400"
            >
                {{ answered }}/{{ total }}
            </p>
        </div>

        <!-- Corps principal (scrollable) -->
        <div class="flex min-h-0 flex-1 overflow-y-auto">
            <div
                class="flex w-full flex-col gap-4 p-4 sm:p-5 md:flex-row md:items-start md:gap-[clamp(1rem,4vw,3rem)] md:p-[clamp(1.5rem,4vw,4rem)]"
            >
                <!-- Zone personnage
                     Mobile : rangée horizontale compacte (icône à gauche, Pochy à droite).
                     Desktop (md+) : bloc relatif avec positionnement absolu pour l'overlap. -->
                <div
                    class="flex shrink-0 flex-row items-end gap-3 md:relative md:block md:h-[clamp(220px,28vw,400px)] md:w-[clamp(160px,20vw,280px)] md:self-center"
                >
                    <!-- Icône thématique -->
                    <img
                        v-if="icon && !iconError"
                        :src="icon"
                        alt=""
                        class="h-14 w-14 shrink-0 [image-rendering:pixelated] md:absolute md:top-0 md:left-0 md:h-auto md:w-[60%]"
                        @error="iconError = true"
                    />
                    <div
                        v-else
                        class="flex h-14 w-14 shrink-0 items-center justify-center border-4 border-gray-800 bg-gray-100 font-pixel text-xl text-gray-400 shadow-[4px_4px_0_rgba(0,0,0,0.7)] md:absolute md:top-0 md:left-0 md:aspect-square md:h-auto md:w-[60%]"
                        aria-hidden="true"
                    >
                        ?
                    </div>

                    <!-- Pochy -->
                    <GamePochy
                        :variant="pochy"
                        class="h-20 w-20 shrink-0 drop-shadow-[0_6px_12px_rgba(0,0,0,0.3)] [image-rendering:pixelated] md:absolute md:bottom-0 md:left-[14%] md:z-[2] md:h-auto md:w-[64%] md:drop-shadow-[0_10px_18px_rgba(0,0,0,0.35)]"
                    />
                </div>

                <!-- Colonne contenu : bulle + contenu -->
                <div class="flex min-w-0 flex-1 flex-col gap-4">
                    <slot name="bubble" />
                    <slot name="content" />
                </div>
            </div>
        </div>

        <!-- Pied (toujours collé en bas) -->
        <div
            class="flex shrink-0 items-center justify-between gap-3 border-t border-gray-100 bg-white px-4 py-3 sm:px-6 md:px-10"
        >
            <slot name="footer" />
        </div>
    </div>
</template>
