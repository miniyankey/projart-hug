<script setup>
// Vue plein écran d'une question (utilise la coquille GameScene).
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import GameChoice from './GameChoice.vue';
import GameScene from './GameScene.vue';
import GameSpeechBubble from './GameSpeechBubble.vue';
import { Button } from '@/components/ui/button';

const { t } = useI18n();

const props = defineProps({
    question: { type: Object, required: true },
    preSelected: { type: Array, default: () => [] },
    answered: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
    icon: { type: String, default: null },
});

const emit = defineEmits(['answer', 'back']);

const isMultiple = computed(() => props.question.type === 'multiple');
const selected = ref([...props.preSelected]);

watch(
    () => props.question.id,
    () => {
        selected.value = [...props.preSelected];
    },
);

function toggle(choiceId) {
    if (isMultiple.value) {
        const i = selected.value.indexOf(choiceId);

        if (i >= 0) {
            selected.value.splice(i, 1);
        } else {
            selected.value.push(choiceId);
        }

        return;
    }

    // Question à choix unique : la sélection valide directement
    selected.value = [choiceId];
    emit('answer', [choiceId]);
}

function validate() {
    emit('answer', [...selected.value]);
}
</script>

<template>
    <GameScene
        :theme="question.titre"
        :answered="answered"
        :total="total"
        :icon="icon"
        :pochy="question.pochy ?? '0'"
    >
        <template #bubble>
            <GameSpeechBubble>{{ question.question }}</GameSpeechBubble>
        </template>

        <template #content>
            <div class="flex flex-wrap gap-3">
                <GameChoice
                    v-for="choice in question.choices"
                    :key="choice.id"
                    :label="choice.text"
                    :descr="choice.descr"
                    :multiple="isMultiple"
                    :selected="selected.includes(choice.id)"
                    @toggle="toggle(choice.id)"
                />
            </div>
        </template>

        <template #footer>
            <Button variant="link" @click="emit('back')">
                {{ t('eligibilite.ui.back') }}
            </Button>
            <!-- Pas de bouton de validation pour les questions à choix unique -->
            <Button v-if="isMultiple" variant="pixel_violet" @click="validate">
                {{ t('eligibilite.ui.validate') }}
            </Button>
        </template>
    </GameScene>
</template>
