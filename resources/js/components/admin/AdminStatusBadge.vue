<script setup>

defineProps({
    tone: {
        type: String,
        default: 'neutral',
        validator: (value) =>
            ['success', 'ongoing', 'warning', 'neutral', 'rank'].includes(
                value,
            ),
    },
});

const tones = {
    success: 'border-green-600 bg-green-50 text-green-700 font-bold',
    ongoing: 'border-emerald-500 bg-emerald-50 text-emerald-700 font-bold',
    warning: 'border-amber-500 bg-amber-50 text-amber-700 font-bold',
    neutral: 'border-gray-300 bg-gray-50 text-gray-500 font-semibold',
    rank: 'border-gray-900 bg-amber-50 text-amber-700 font-bold',
};
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 border-2 px-2.5 py-1 text-xs"
        :class="tones[tone]"
    >
        <slot />
    </span>
</template>
