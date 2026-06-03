import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { QUIZ, QUIZ_VIEWS } from '@/data/eligibilityQuiz';

// Assemble le quiz d'éligibilité : structure (data/eligibilityQuiz.js) + texte
// traduit (locales eligibilite.quiz.*). Réactif au changement de langue.
export function useEligibilityQuiz() {
    const { t, te } = useI18n();

    function maybe(key) {
        return te(key) ? t(key) : null;
    }

    function buildView(viewKey) {
        const base = `eligibilite.quiz.views.${viewKey}`;

        return {
            message: t(`${base}.message`),
            text: maybe(`${base}.text`),
            button_text: maybe(`${base}.button_text`),
            button_url: QUIZ_VIEWS[viewKey]?.buttonUrl ?? null,
        };
    }

    function buildQuestion(q) {
        const base = `eligibilite.quiz.${q.key}`;

        return {
            id: q.key,
            type: q.type,
            titre: t(`${base}.titre`),
            question: t(`${base}.question`),
            why_question: maybe(`${base}.why`),
            choices: q.choices.map((c) => ({
                id: c.key,
                eligible: c.eligible,
                ineligibility_days: c.days,
                text: t(`${base}.choices.${c.key}`),
                descr: maybe(`${base}.choices_descr.${c.key}`),
                view: c.view ? buildView(c.view) : null,
            })),
        };
    }

    const questions = computed(() => QUIZ.map(buildQuestion));

    // Durée en langage naturel (pluralisation gérée par vue-i18n)
    function formatDuration(days) {
        if (days >= 365) {
            return t('eligibilite.duration.year', Math.round(days / 365));
        }

        if (days >= 30) {
            return t('eligibilite.duration.month', Math.round(days / 30));
        }

        if (days >= 7) {
            return t('eligibilite.duration.week', Math.round(days / 7));
        }

        return t('eligibilite.duration.day', days);
    }

    // Vue générique d'inéligibilité (à vie ou temporaire)
    function ineligibleView(days) {
        if (days < 0) {
            return {
                message: t('eligibilite.ineligible.lifetime.message'),
                descr: t('eligibilite.ineligible.lifetime.descr'),
                text: t('eligibilite.ineligible.lifetime.text'),
            };
        }

        return {
            message: t('eligibilite.ineligible.temporary.message', {
                duration: formatDuration(days),
            }),
            descr: t('eligibilite.ineligible.temporary.descr'),
            text: t('eligibilite.ineligible.temporary.text'),
        };
    }

    return { questions, ineligibleView };
}
