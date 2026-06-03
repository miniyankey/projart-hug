<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import SpeechBubble from '@/components/cards/SpeechBubble.vue';

const props = defineProps({
    href: { type: String, required: true },
});

const { t } = useI18n();
const visible = ref(false);

onMounted(() => {
    if (sessionStorage.getItem('mascotte-popup-seen')) {
        return;
    }

    setTimeout(() => {
        visible.value = true;
    }, 2000);
});

function close() {
    visible.value = false;
    sessionStorage.setItem('mascotte-popup-seen', '1');
}
</script>

<template>
    <Transition name="mascotte">
        <div
            v-if="visible"
            class="fixed bottom-6 left-6 z-50 w-48 border-2 border-black bg-white shadow-[4px_4px_0_0_#000]"
        >
            <div
                class="flex items-center justify-between border-b-2 border-black px-3 py-2"
            >
                <span class="font-pixel text-[0.55rem] text-black">{{
                    t('eligibilite.title')
                }}</span>
                <button
                    class="flex h-5 w-5 cursor-pointer items-center justify-center bg-black text-[0.7rem] font-bold text-white transition-opacity hover:opacity-70"
                    :aria-label="t('eligibilite.mascotte_close')"
                    @click="close"
                >
                    ×
                </button>
            </div>

            <div class="flex flex-col items-center gap-2 px-3 py-3">
                <SpeechBubble :text="t('eligibilite.mascotte_speech')" />
                <img
                    src="/img/image.png"
                    alt=""
                    class="h-24 w-auto"
                    draggable="false"
                />
                <Link
                    :href="props.href"
                    class="block w-full cursor-pointer border-2 border-black bg-[var(--brand)] py-2 text-center font-pixel text-[0.55rem] text-white shadow-[3px_3px_0_0_#000] transition-transform hover:translate-x-px hover:translate-y-px hover:shadow-[2px_2px_0_0_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[1px_1px_0_0_#000]"
                    @click="close"
                >
                    {{ t('eligibilite.mascotte_cta') }}
                </Link>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.mascotte-enter-active,
.mascotte-leave-active {
    transition:
        opacity 0.4s ease,
        transform 0.4s ease;
}

.mascotte-enter-from,
.mascotte-leave-to {
    opacity: 0;
    transform: translateY(16px);
}
</style>
