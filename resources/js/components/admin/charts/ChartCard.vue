<script setup>
// Carte de section pour les graphiques : cadre net pixel, titre + sous-titre.
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <section class="border-2 border-gray-900 bg-white p-5">
        <header class="mb-4">
            <h2 class="text-base font-bold tracking-tight text-gray-900">
                {{ title }}
            </h2>
            <p v-if="subtitle" class="mt-0.5 text-sm text-gray-500">
                {{ subtitle }}
            </p>
        </header>
        <slot />
    </section>
</template>
