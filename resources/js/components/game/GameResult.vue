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
    // Inéligibilité temporaire : jours d'attente (> 0) → affiche le formulaire de
    // rappel. 0 pour éligible ou inéligibilité à vie (pas de rappel possible).
    reminderDays: { type: Number, default: 0 },
    collectId: { type: Number, default: null },
});

defineEmits(['ok', 'back']);

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
            <div class="flex flex-col gap-5">
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

                <EligibilityabbiefurtReminderForm
                    v-if="reminderDays > 0"
                    :days="reminderDays"
                    :collect-id="collectId"
                />
            </div>
        </template>

        <template #footer>
            <Button variant="link" @click="$emit('back')">
                {{ t('eligibilite.ui.back') }}
            </Button>
            <Button variant="pixel_violet" @click="$emit('ok')">
                {{ t('eligibilite.ui.ok') }}
            </Button>
        </template>
    </GameScene>
</template>
