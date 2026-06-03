<script setup>
// Écran de résultat après validation, basé sur la « view » du choix.
// Bulle : message + descr de la view. Contenu : texte + CTA éventuel.
import { useI18n } from 'vue-i18n';
import GameScene from './GameScene.vue';
import GameSpeechBubble from './GameSpeechBubble.vue';

defineProps({
    // Vue : { message, descr?, text?, button_text?, button_url? }
    view: { type: Object, required: true },
    theme: { type: String, default: '' },
    answered: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    icon: { type: String, default: null },
});

defineEmits(['ok', 'back']);

const { t } = useI18n();
</script>

<template>
    <GameScene :theme="theme" :answered="answered" :total="total" :icon="icon">
        <template #bubble>
            <GameSpeechBubble>
                {{ view.message }}
                <template v-if="view.descr">
                    <br />
                    <span class="text-[0.8em] opacity-[0.85]">{{ view.descr }}</span>
                </template>
            </GameSpeechBubble>
        </template>

        <template #content>
            <div class="flex flex-col gap-5">
                <p
                    v-if="view.text"
                    class="m-0 text-[1.05rem] leading-normal text-neutral-900"
                >
                    {{ view.text }}
                </p>

                <!-- CTA stylé en bouton primaire (autonome : ce lien est imbriqué,
                     donc hors de portée du :slotted de GameScene) -->
                <a
                    v-if="view.button_url"
                    class="self-start border-[3px] border-black bg-[var(--brand,#7c3aed)] px-8 py-[0.8rem] text-base text-white no-underline shadow-[5px_5px_0_#000] transition-[transform,box-shadow] duration-[80ms] active:translate-x-[5px] active:translate-y-[5px] active:shadow-none"
                    :href="view.button_url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    {{ view.button_text || t('eligibilite.ui.learn_more') }}
                </a>
            </div>
        </template>

        <template #footer>
            <button type="button" class="game-back" @click="$emit('back')">
                {{ t('eligibilite.ui.back') }}
            </button>
            <button type="button" class="game-cta" @click="$emit('ok')">
                {{ t('eligibilite.ui.ok') }}
            </button>
        </template>
    </GameScene>
</template>
