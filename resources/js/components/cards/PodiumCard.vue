<script setup>
import { computed } from 'vue';

const props = defineProps({
    rank: {
        type: Number,
        required: true,
        validator: (v) => [1, 2, 3].includes(v),
    },
    logoSrc: { type: String, required: true },
    logoAlt: { type: String, default: '' },
    category: { type: String, required: true },
    description: { type: String, required: true },
});

const rankLabel = computed(
    () => ({ 1: '1er', 2: '2ème', 3: '3ème' })[props.rank],
);

const palettes = {
    1: { bg: '#C8A424', border: '#8B6B14', rivet: '#E8C048' },
    2: { bg: '#7D9099', border: '#4A5A63', rivet: '#A8BEC8' },
    3: { bg: '#C07830', border: '#8B5520', rivet: '#D89850' },
};

const palette = computed(() => palettes[props.rank]);

const cardStyle = computed(() => ({
    backgroundColor: palette.value.bg,
    borderColor: palette.value.border,
    backgroundImage:
        'repeating-linear-gradient(90deg, transparent 0px, transparent 16px, rgba(255,255,255,0.1) 16px, rgba(255,255,255,0.1) 22px)',
}));

const rivetStyle = computed(() => ({ backgroundColor: palette.value.rivet }));

const rankPadding = computed(
    () => ({ 1: 'py-14', 2: 'py-9', 3: 'py-5' })[props.rank],
);
</script>

<template>
    <div class="flex flex-col items-center">
        <!-- Logo au-dessus de la carte -->
        <div class="mb-3 flex h-14 items-end justify-center">
            <img
                :src="logoSrc"
                :alt="logoAlt"
                class="max-h-14 max-w-[150px] object-contain"
            />
        </div>

        <!-- Carte -->
        <div
            class="relative w-full overflow-hidden border-[3px]"
            :style="cardStyle"
        >
            <!-- Rivets haut -->
            <div class="absolute top-2 left-2 h-3 w-3" :style="rivetStyle" />
            <div class="absolute top-2 right-2 h-3 w-3" :style="rivetStyle" />

            <!-- Zone rang avec rivets bas -->
            <div
                class="relative flex items-center justify-center"
                :class="rankPadding"
            >
                <div
                    class="absolute bottom-2 left-2 h-3 w-3"
                    :style="rivetStyle"
                />
                <div
                    class="absolute right-2 bottom-2 h-3 w-3"
                    :style="rivetStyle"
                />

                <span
                    class="font-pixel text-2xl text-white select-none"
                    style="text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.35)"
                    >{{ rankLabel }}</span
                >
            </div>

            <!-- Footer sombre -->
            <div class="bg-[#1b2735] px-4 py-4 font-mono text-white">
                <p class="mb-2 text-center text-xs font-bold tracking-wider">
                    {{ category }}
                </p>
                <p class="text-center text-xs leading-relaxed text-gray-300">
                    {{ description }}
                </p>
            </div>
        </div>
    </div>
</template>
