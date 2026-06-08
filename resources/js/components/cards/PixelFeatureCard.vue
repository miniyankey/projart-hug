<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div
        class="bg-white p-6 transition-shadow"
        :class="
            active
                ? 'border-4 border-[var(--brand)] shadow-[6px_6px_0px_0px_var(--brand-shadow)]'
                : 'border-4 border-black shadow-[6px_6px_0px_0px_#000]'
        "
    >
        <!-- Icon -->
        <div
            class="mb-6 inline-flex items-center justify-center bg-[var(--brand)] p-3 text-white"
        >
            <slot name="icon" />
        </div>

        <!-- Title -->
        <div class="border-2 border-dashed border-[var(--brand)]/40 p-3">
            <h3
                class="font-pixel text-sm leading-loose text-[var(--brand-shadow)] uppercase"
            >
                {{ title }}
            </h3>
        </div>

        <!-- Description -->
        <div class="mt-3 border-2 border-dashed border-[var(--brand)]/40 p-3">
            <p class="text-lg leading-relaxed text-gray-600">
                {{ description }}
            </p>
        </div>
    </div>
</template>
