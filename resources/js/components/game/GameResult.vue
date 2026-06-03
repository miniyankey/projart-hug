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
                    <span class="result__descr">{{ view.descr }}</span>
                </template>
            </GameSpeechBubble>
        </template>

        <template #content>
            <div class="result">
                <p v-if="view.text" class="result__text">{{ view.text }}</p>

                <a
                    v-if="view.button_url"
                    class="result__cta"
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

<style scoped>
.result {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.result__descr {
    font-size: 0.8em;
    opacity: 0.85;
}

.result__text {
    margin: 0;
    font-size: 1.05rem;
    line-height: 1.5;
    color: #1a1a1a;
}

/* Lien CTA stylé en bouton primaire (autonome : pas de dépendance à :slotted,
   car ce lien est imbriqué et non un nœud racine du slot). */
.result__cta {
    align-self: flex-start;
    padding: 0.8rem 2rem;
    font-size: 1rem;
    color: white;
    text-decoration: none;
    background: var(--brand, #7c3aed);
    border: 3px solid black;
    box-shadow: 5px 5px 0 black;
    transition:
        transform 80ms ease,
        box-shadow 80ms ease;
}

.result__cta:active {
    transform: translate(5px, 5px);
    box-shadow: 0 0 0 black;
}
</style>
