<script setup>
import { computed } from 'vue';

const props = defineProps({
    step: { type: Number, required: true },
    title: { type: String, required: true },
    description: { type: String, required: true },
});

const palettes = [
    { bg: '#F5E07A', shadow: '#C4A800', text: '#3D3000', rivetLight: '#FBF2B8', rivetDark: '#C4A800' },
    { bg: '#B2D4A8', shadow: '#4E8A42', text: '#1A3D14', rivetLight: '#D4EAD0', rivetDark: '#4E8A42' },
    { bg: '#B5CEED', shadow: '#4A7AAD', text: '#152D45', rivetLight: '#D5E5F5', rivetDark: '#4A7AAD' },
    { bg: '#D4A0A0', shadow: '#A05050', text: '#3D1414', rivetLight: '#EAC8C8', rivetDark: '#A05050' },
];

const palette = computed(() => palettes[(props.step - 1) % palettes.length]);

const cardStyle = computed(() => ({
    backgroundColor: palette.value.bg,
    boxShadow: `8px 8px 0 ${palette.value.shadow}`,
    color: palette.value.text,
    backgroundImage:
        'repeating-linear-gradient(90deg, transparent 0px, transparent 18px, rgba(255,255,255,0.18) 18px, rgba(255,255,255,0.18) 22px)',
}));
</script>

<template>
    <div class="relative overflow-hidden p-6 transition-transform hover:-translate-y-1" :style="cardStyle">
        <!-- Rivets coins -->
        <div class="absolute top-2 left-2 h-2.5 w-2.5" :style="{ backgroundColor: palette.rivetLight }" />
        <div class="absolute top-2 right-2 h-2.5 w-2.5" :style="{ backgroundColor: palette.rivetLight }" />
        <div class="absolute bottom-2 left-2 h-2.5 w-2.5" :style="{ backgroundColor: palette.rivetDark }" />
        <div class="absolute bottom-2 right-2 h-2.5 w-2.5" :style="{ backgroundColor: palette.rivetDark }" />

        <!-- Contenu -->
        <p class="font-pixel text-[0.6rem] tracking-widest uppercase" style="opacity: 1">
            ETAPE {{ step }}
        </p>
        <h3 class="font-pixel mt-2 text-lg leading-snug uppercase">
            {{ title }}
        </h3>
        <div class="mt-3 h-px w-8" :style="{ backgroundColor: palette.text }" />
        <p class="mt-3 text-sm leading-relaxed" style="opacity: 1">
            {{ description }}
        </p>
    </div>
</template>
