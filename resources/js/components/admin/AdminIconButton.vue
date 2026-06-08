<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
    href: { type: String, default: null },
    title: { type: String, default: null },
    destructive: { type: Boolean, default: false },
});

const classes = computed(() =>
    cn(
        'flex size-8 items-center justify-center border-2 border-gray-900 bg-white transition-colors',
        props.destructive
            ? 'text-red-600 hover:bg-red-50'
            : 'text-gray-700 hover:bg-gray-100',
    ),
);
</script>

<template>
    <Link v-if="href" :href="href" :title="title" :class="classes">
        <slot />
    </Link>
    <button v-else type="button" :title="title" :class="classes">
        <slot />
    </button>
</template>
