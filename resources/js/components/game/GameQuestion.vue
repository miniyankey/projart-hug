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

// Le choix « aucun » est exclusif : le cocher vide les autres, et cocher un
// autre choix le retire.
const NONE = 'none';

function toggle(choiceId) {
    if (isMultiple.value) {
        if (choiceId === NONE) {
            selected.value = selected.value.includes(NONE) ? [] : [NONE];

            return;
        }

        const i = selected.value.indexOf(choiceId);

        if (i >= 0) {
            selected.value.splice(i, 1);
        } else {
            selected.value.push(choiceId);
            selected.value = selected.value.filter((id) => id !== NONE);
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

            <!-- Actions : Retour à côté de Valider (choix multiples seulement,
                 affiché dès qu'un choix est sélectionné). Les choix uniques
                 valident directement au clic. -->
            <div class="mt-7 flex items-center gap-3">
                <Button
                    variant="link"
                    class="h-auto px-4 py-3 text-[1.05rem] text-[color:var(--brand,#7c3aed)]"
                    @click="emit('back')"
                >
                    {{ t('eligibilite.ui.back') }}
                </Button>
                <Button
                    v-if="isMultiple && selected.length > 0"
                    variant="pixel_violet"
                    class="h-auto px-7 py-3 text-[1.05rem]"
                    @click="validate"
                >
                    {{ t('eligibilite.ui.validate') }}
                </Button>
            </div>
        </template>
    </GameScene>
</template>
