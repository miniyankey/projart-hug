<script setup>
// Carte de section de formulaire admin : cadre net, titre en bandeau.
defineProps({
    title: {
        type: String,
        required: true,
    },
    bodyClass: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <section class="border-2 border-gray-900 bg-white">
        <h2
            class="border-b-2 border-gray-900 bg-gray-50 px-5 py-3 text-sm font-bold uppercase tracking-wide text-gray-900"
        >
            {{ title }}
        </h2>
        <div class="grid grid-cols-1 gap-5 p-5" :class="bodyClass">
            <slot />
        </div>
    </section>
</template>
