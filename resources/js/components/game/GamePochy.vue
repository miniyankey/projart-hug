<script setup>
defineProps({
    variant: { type: String, required: true },
});
</script>

<template>
    <img
        :src="`/img/pochy/pochy-${variant}.webp`"
        alt="Pochy"
        class="[image-rendering:pixelated]"
    />
</template>
