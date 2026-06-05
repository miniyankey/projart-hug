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
            selected ? 'bg-[var(--brand,#7c3aed)] text-white shadow-none' : '',
            descr ? '' : 'items-center',
        ]"
        :aria-pressed="selected"
        @click="$emit('toggle')"
    >
        <!-- Case à cocher (choix multiples uniquement) -->
        <span
            v-if="multiple"
            class="flex h-[1.9rem] w-[1.9rem] shrink-0 items-center justify-center border-[3px] group-active:border-current"
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
            <span class="text-[0.95rem] leading-[1.3] font-semibold">{{
                label
            }}</span>
            <span
                v-if="descr"
                class="text-left text-[0.85rem] leading-[1.3] opacity-[0.85]"
            >
                {{ descr }}
            </span>
        </span>
    </Button>
</template>
