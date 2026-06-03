// Logique d'éligibilité du jeu (pure, sans dépendance Vue ni i18n → testable).
// Le formatage des durées et les vues génériques (textes) sont gérés par le
// composable useEligibilityQuiz() car ils dépendent des traductions.

// Détermine l'éligibilité d'une réponse et le choix « le plus défavorable ».
// - à vie (ineligibility_days < 0) prime sur tout ;
// - sinon, la plus longue durée l'emporte.
export function computeResult(question, choiceIds) {
    const selected = question.choices.filter((c) => choiceIds.includes(c.id));
    const ineligible = selected.filter((c) => !c.eligible);

    if (ineligible.length === 0) {
        return { eligible: true, days: null, view: null };
    }

    const worst = ineligible.reduce((a, b) => {
        const ad = a.ineligibility_days ?? 0;
        const bd = b.ineligibility_days ?? 0;

        if (ad < 0) {
            return a;
        }

        if (bd < 0) {
            return b;
        }

        return ad >= bd ? a : b;
    });

    return {
        eligible: false,
        days: worst.ineligibility_days,
        view: worst.view,
    };
}
