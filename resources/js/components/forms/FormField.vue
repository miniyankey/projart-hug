<script setup>
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const model = defineModel({
    type: String,
    default: '',
});

defineProps({
    // Identifiant du champ (lie le Label à l'Input).
    id: {
        type: String,
        required: true,
    },
    // Libellé affiché au-dessus du champ (déjà traduit).
    label: {
        type: String,
        required: true,
    },
    // Type HTML de l'input (text, email, password...).
    type: {
        type: String,
        default: 'text',
    },
    autocomplete: {
        type: String,
        default: undefined,
    },
    // Message d'erreur de validation (vide = aucune erreur).
    error: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <Label :for="id" class="font-semibold text-gray-900">{{ label }}</Label>
        <Input
            :id="id"
            v-model="model"
            :type="type"
            variant="pixel"
            :autocomplete="autocomplete"
            :aria-invalid="!!error"
        />
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    </div>
</template>
