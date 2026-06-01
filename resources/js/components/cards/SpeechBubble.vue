<script setup>
defineProps({
    text: { type: String, required: true },
});
</script>

<template>
    <div class="speech-bubble">
        <p class="font-pixel text-[0.65rem] leading-relaxed text-gray-900">
            {{ text }}
        </p>
    </div>
</template>

<style scoped>
.speech-bubble {
    position: relative;
    background: white;
    border: 3px solid black;
    box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.45);
    padding: 0.85rem 1.25rem;
}

/* Bordure noire de la queue */
.speech-bubble::after {
    content: '';
    position: absolute;
    bottom: -26px;
    left: 58%;
    border-top: 26px solid black;
    border-left: 20px solid transparent;
    border-right: 0 solid transparent;
}

/* Remplissage blanc de la queue (par-dessus) */
.speech-bubble::before {
    content: '';
    position: absolute;
    bottom: -18px;
    left: calc(58% + 4px);
    border-top: 19px solid white;
    border-left: 14px solid transparent;
    border-right: 0 solid transparent;
    z-index: 1;
}
</style>
