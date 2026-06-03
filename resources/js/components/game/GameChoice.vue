<script setup>
// Bouton de réponse néo-brutaliste.
// - Défaut : fond blanc, bordure couleur de marque, ombre portée dure.
// - Pressé (active) ou sélectionné : fond couleur de marque, texte blanc, à plat.
// - `multiple` : ajoute une case à cocher (coche affichée quand sélectionné).
// La couleur apparaît dès l'enfoncement du clic (variantes active:) et reste à
// la sélection (classes conditionnelles).
defineProps({
    label: { type: String, required: true },
    descr: { type: String, default: null },
    selected: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
});

defineEmits(['toggle']);
</script>

<template>
    <button
        type="button"
        class="group inline-flex cursor-pointer items-center gap-[0.85rem] border-[3px] border-[var(--brand,#7c3aed)] px-[1.1rem] py-[0.85rem] text-left transition-[transform,box-shadow] duration-[60ms] active:bg-[var(--brand,#7c3aed)] active:text-white active:shadow-none"
        :class="
            selected
                ? 'bg-[var(--brand,#7c3aed)] text-white shadow-none'
                : 'bg-white text-[#111] shadow-[6px_6px_0_var(--brand-shadow,#4c1d95)]'
        "
        :aria-pressed="selected"
        @click="$emit('toggle')"
    >
        <!-- Case à cocher (choix multiples uniquement) -->
        <span
            v-if="multiple"
            class="flex h-[1.9rem] w-[1.9rem] shrink-0 items-center justify-center border-[3px] group-active:border-current"
            :class="selected ? 'border-current' : 'border-[var(--brand,#7c3aed)]'"
            aria-hidden="true"
        >
            <svg v-if="selected" class="h-full w-full" viewBox="0 0 24 24">
                <path
                    d="M5 13l4 4L19 7"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </span>

        <span class="flex min-w-0 flex-col gap-[0.15rem]">
            <span class="text-[1.05rem] font-semibold leading-[1.3]">{{ label }}</span>
            <span
                v-if="descr"
                class="text-[0.95rem] leading-[1.3] opacity-[0.85]"
            >
                {{ descr }}
            </span>
        </span>
    </button>
</template>
