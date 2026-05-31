<script setup>
// Carte de statistique du tableau de bord : valeur lisible, cadre net.
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    icon: {
        type: [Object, Function],
        default: null,
    },
});
</script>

<template>
    <div class="border-2 border-gray-900 bg-white p-5">
        <div class="flex items-start justify-between">
            <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                {{ label }}
            </p>
            <component
                :is="icon"
                v-if="icon"
                class="size-5 shrink-0 text-[var(--brand)]"
            />
        </div>
        <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900">
            {{ value }}
        </p>
    </div>
</template>
