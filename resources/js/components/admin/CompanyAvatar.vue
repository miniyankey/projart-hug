<script setup>
import { Building2 } from 'lucide-vue-next';

// Avatar d'entreprise : affiche le logo s'il existe, sinon une icône
// sur un carré teinté de la couleur de marque. La taille est pilotée
// par la classe passée (fallthrough), défaut size-9.
defineProps({
    logoUrl: {
        type: String,
        default: null,
    },
    color: {
        type: String,
        default: null,
    },
    name: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <span
        class="flex size-9 shrink-0 items-center justify-center overflow-hidden border-2 border-gray-900 bg-white"
        :style="logoUrl ? {} : { backgroundColor: color ?? '#e5e7eb' }"
    >
        <img
            v-if="logoUrl"
            :src="logoUrl"
            :alt="name"
            class="size-full object-contain p-0.5"
        />
        <Building2 v-else class="size-4 text-white" />
    </span>
</template>
