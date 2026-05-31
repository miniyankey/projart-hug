<script setup>
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// Champ de formulaire admin : libellé + contrôle + message d'erreur.
// Par défaut rend un Input carré, sans ombre ; un slot permet d'injecter
// un contrôle personnalisé (couleur, fichier, date…) tout en gardant le
// libellé et la gestion d'erreur.
defineProps({
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    maxlength: {
        type: [String, Number],
        default: null,
    },
});

const model = defineModel({ default: '' });
</script>

<template>
    <div>
        <Label class="mb-1.5 block font-semibold text-gray-900">{{ label }}</Label>
        <slot :field-class="'admin-field'">
            <Input
                v-model="model"
                :type="type"
                :placeholder="placeholder"
                :maxlength="maxlength"
                class="h-auto w-full rounded-none border-2 border-gray-900 bg-white py-2 text-gray-900 shadow-none placeholder:text-gray-400 focus-visible:border-[var(--brand)] focus-visible:ring-0 dark:bg-white dark:text-gray-900"
            />
        </slot>
        <p v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</p>
    </div>
</template>
