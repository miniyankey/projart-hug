<script setup>
import { Button } from '@/components/ui/button';

defineProps({
    label: { type: String, required: true },
    descr: { type: String, default: null },
    selected: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
});

defineEmits(['toggle']);
</script>

<template>
    <Button
        variant="quiz"
        size="quiz"
        :class="[
            // max-w-full : le bouton ne dépasse jamais son conteneur → le label
            // passe sur plusieurs lignes au lieu de casser le responsive.
            // Compact sur mobile, plus généreux dès le desktop (md).
            'max-w-full gap-2 px-3 py-2 md:gap-3 md:px-5 md:py-3.5',
            selected ? 'bg-[var(--brand,#7c3aed)] text-white shadow-none' : '',
            descr ? '' : 'items-center',
        ]"
        :aria-pressed="selected"
        @click="$emit('toggle')"
    >
        <!-- Case à cocher (choix multiples uniquement) -->
        <span
            v-if="multiple"
            class="flex h-6 w-6 shrink-0 items-center justify-center border-[3px] group-active:border-current md:h-7 md:w-7"
            :class="
                selected ? 'border-current' : 'border-[var(--brand,#7c3aed)]'
            "
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

        <span class="flex min-w-0 flex-col gap-[0.15rem] text-left">
            <span
                class="text-[0.85rem] leading-[1.25] font-semibold break-words hyphens-auto md:text-[1.05rem]"
            >
                {{ label }}
            </span>
            <span
                v-if="descr"
                class="text-left text-[0.78rem] leading-[1.25] break-words opacity-[0.85] md:text-[0.95rem]"
            >
                {{ descr }}
            </span>
        </span>
    </Button>
</template>
