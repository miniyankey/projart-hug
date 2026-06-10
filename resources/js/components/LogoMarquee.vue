<script setup>
import { computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

// Une boucle fluide demande une piste plus large que l'écran : on répète la
// liste jusqu'à un minimum d'éléments (utile quand il n'y a que 3-4 logos).
const MIN_TRACK_ITEMS = 8;

const trackItems = computed(() => {
    if (props.items.length === 0) {
        return [];
    }

    const repeats = Math.ceil(MIN_TRACK_ITEMS / props.items.length);

    return Array.from({ length: repeats }, () => props.items).flat();
});
</script>

<template>
    <div class="logo-marquee group flex overflow-hidden">
        <div
            v-for="copy in 2"
            :key="copy"
            class="logo-marquee-track flex shrink-0 items-center gap-8 pr-8 pb-2"
            :aria-hidden="copy === 2 ? 'true' : undefined"
        >
            <div
                v-for="(item, i) in trackItems"
                :key="`${copy}-${i}-${item.id}`"
                class="flex w-52 items-center justify-center bg-white px-6 py-5 shadow-[8px_8px_0_0_var(--color-gray-400)]"
            >
                <img
                    v-if="item.logo_url"
                    :src="item.logo_url"
                    :alt="item.name"
                    class="h-24 w-full object-contain"
                    loading="lazy"
                />
                <span
                    v-else
                    class="font-pixel text-[0.55rem] leading-relaxed text-gray-800"
                >
                    {{ item.name }}
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.logo-marquee {
    mask-image: linear-gradient(
        to right,
        transparent,
        black 8%,
        black 92%,
        transparent
    );
}

.logo-marquee-track {
    animation: logo-marquee-scroll 28s linear infinite;
}

.group:hover .logo-marquee-track {
    animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
    .logo-marquee-track {
        animation-play-state: paused;
    }
}

@keyframes logo-marquee-scroll {
    from {
        transform: translateX(0);
    }

    to {
        transform: translateX(-100%);
    }
}
</style>
