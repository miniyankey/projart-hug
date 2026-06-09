<script setup>
// Écran de résultat après validation, basé sur la « view » du choix.
// Bulle : message + descr de la view. Contenu : texte + CTA éventuel.
import { useI18n } from 'vue-i18n';
import GameScene from './GameScene.vue';
import GameSpeechBubble from './GameSpeechBubble.vue';
import EligibilityReminderForm from '@/components/cobrand/EligibilityReminderForm.vue';
import { Button } from '@/components/ui/button';

defineProps({
    // Vue : { message, descr?, text?, button_text?, button_url? }
    view: { type: Object, required: true },
    theme: { type: String, default: '' },
    answered: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    icon: { type: String, default: null },
    pochy: { type: String, default: '0' },
    // Inéligibilité temporaire : jours d'attente (> 0) → formulaire de rappel.
    reminderDays: { type: Number, default: 0 },
    // Inéligibilité à vie → proposition de don financier (même composant).
    donation: { type: Boolean, default: false },
    collectId: { type: Number, default: null },
    // Indique si le résultat est une inéligibilité (pour changer le texte du bouton OK)
    isIneligible: { type: Boolean, default: false },
});

// handled : lien de don cliqué → le jeu ne sollicite plus ensuite.
// reminder-submitted : rappel enregistré (porte son id pour mise à jour ultérieure).
defineEmits(['ok', 'back', 'handled', 'reminder-submitted']);

const { t } = useI18n();
</script>

<template>
    <GameScene
        :theme="theme"
        :answered="answered"
        :total="total"
        :icon="icon"
        :pochy="pochy"
    >
        <template #bubble>
            <GameSpeechBubble>
                {{ view.message }}
                <template v-if="view.descr">
                    <br />
                    <span class="text-[0.8em] opacity-[0.85]">{{
                        view.descr
                    }}</span>
                </template>
            </GameSpeechBubble>
        </template>

        <template #content>
            <div class="flex flex-col gap-7">
                <p
                    v-if="view.text"
                    class="m-0 text-[1.05rem] leading-normal text-neutral-900"
                >
                    {{ view.text }}
                </p>

                <Button
                    v-if="view.button_url"
                    variant="pixel_violet"
                    as="a"
                    class="self-start"
                    :href="view.button_url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    {{ view.button_text || t('eligibilite.ui.learn_more') }}
                </Button>

                <EligibilityReminderForm
                    v-if="donation || reminderDays > 0"
                    :mode="donation ? 'donation' : 'reminder'"
                    :days="reminderDays"
                    :collect-id="collectId"
                    @submitted="(id) => $emit('reminder-submitted', id)"
                    @donated="$emit('handled')"
                />

                <!-- Actions : Retour à côté de OK -->
                <div class="mt-7 flex items-center gap-3">
                    <Button
                        variant="link"
                        class="h-auto px-4 py-3 text-[1.05rem]"
                        @click="$emit('back')"
                    >
                        {{ t('eligibilite.ui.back') }}
                    </Button>
                    <Button
                        variant="pixel_violet"
                        class="h-auto px-7 py-3 text-[1.05rem]"
                        @click="$emit('ok')"
                    >
                        {{
                            isIneligible
                                ? t('eligibilite.ui.continue')
                                : t('eligibilite.ui.ok')
                        }}
                    </Button>
                </div>
            </div>
        </template>
    </GameScene>
</template>
