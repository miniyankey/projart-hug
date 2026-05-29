<script setup>
import { computed } from 'vue'

const props = defineProps({
    step: { type: Number, required: true },
    label: { type: String, required: true },
})

const colors = [
    { bg: '#52C05A', shadow: '#2E8A35', rivetTop: '#7AE082', rivetBottom: '#2E8A35' },
    { bg: '#BB44DD', shadow: '#8A20AC', rivetTop: '#D470EE', rivetBottom: '#8A20AC' },
    { bg: '#5599EE', shadow: '#2A5FAA', rivetTop: '#88BBFF', rivetBottom: '#2A5FAA' },
    { bg: '#D45030', shadow: '#9E3418', rivetTop: '#E87858', rivetBottom: '#9E3418' },
]

const palette = computed(() => colors[(props.step - 1) % colors.length])

const cardStyle = computed(() => ({
    backgroundColor: palette.value.bg,
    boxShadow: `4px 4px 0 ${palette.value.shadow}`,
    backgroundImage:
        'repeating-linear-gradient(90deg, transparent 0px, transparent 16px, rgba(255,255,255,0.1) 16px, rgba(255,255,255,0.1) 22px)',
}))
</script>

<template>
    <div class="flex h-full flex-col">
        <!-- Carte paysage -->
        <div class="relative flex flex-1 items-center justify-center overflow-hidden" :style="cardStyle">
            <!-- Rivets haut -->
            <div
                class="absolute top-2 left-2 h-2.5 w-2.5"
                :style="{ backgroundColor: palette.rivetTop }"
            />
            <div
                class="absolute top-2 right-2 h-2.5 w-2.5"
                :style="{ backgroundColor: palette.rivetTop }"
            />
            <!-- Rivets bas -->
            <div
                class="absolute bottom-2 left-2 h-2.5 w-2.5"
                :style="{ backgroundColor: palette.rivetBottom }"
            />
            <div
                class="absolute bottom-2 right-2 h-2.5 w-2.5"
                :style="{ backgroundColor: palette.rivetBottom }"
            />

            <!-- Numéro -->
            <div class="flex items-center justify-center px-10 py-10">
                <span
                    class="font-pixel select-none text-7xl text-black"
                    style="text-shadow: 3px 3px 0 rgba(0,0,0,0.15)"
                >{{ step }}</span>
            </div>
        </div>

        <!-- Label -->
        <div class="mt-2 bg-black px-4 py-2 text-center">
            <p class="font-mono text-sm font-bold text-white">{{ label }}</p>
        </div>
    </div>
</template>
