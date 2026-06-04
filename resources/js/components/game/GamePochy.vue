<script setup>
defineProps({
    variant: { type: String, required: true },
});
</script>

<template>
    <img
        :src="`/img/pochy/pochy-${variant}.png`"
        alt="Pochy"
        style="image-rendering: pixelated"
    />
</template>
