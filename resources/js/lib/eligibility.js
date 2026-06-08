// Logique d'éligibilité du jeu (pure, sans dépendance Vue ni i18n → testable).
// Le formatage des durées et les vues génériques (textes) sont gérés par le
// composable useEligibilityQuiz() car ils dépendent des traductions.
//
// overallVerdict() prend les questions assemblées (avec texte traduit) et les
// réponses de la session, et retourne le verdict global de fin de parcours.

// Détermine l'éligibilité d'une réponse et le choix « le plus défavorable ».
// - à vie (ineligibility_days < 0) prime sur tout ;
// - sinon, la plus longue durée l'emporte.
// La `view` est indépendante de la durée : dès qu'un choix sélectionné porte une
// vue spécifique, c'est elle qui prime (peu importe quel choix donne la pire
// durée). En choix multiple, ça évite qu'un choix « plus long » sans vue masque
// la vue d'un autre choix coché (ex. vaccin « inconnu » vs « vivant atténué »).
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

    const withView = ineligible.find((c) => c.view);

    return {
        eligible: false,
        days: worst.ineligibility_days,
        view: withView?.view ?? null,
    };
}

// Verdict global à la fin du parcours.
// Retourne { status, days, steps } où :
//   status : 'eligible' | 'temporary' | 'lifetime'
//   days   : durée la plus longue des inéligibilités temporaires (null sinon)
//   steps  : liste des étapes inéligibles { questionKey, titre, days }
export function overallVerdict(questions, answers) {
    const steps = [];

    for (const q of questions) {
        const choiceIds = answers[q.id];

        if (!choiceIds || choiceIds.length === 0) {
            continue;
        }

        const result = computeResult(q, choiceIds);

        if (!result.eligible) {
            // Libellés des réponses inéligibles choisies par l'utilisateur
            const answers = q.choices
                .filter((c) => choiceIds.includes(c.id) && !c.eligible)
                .map((c) => c.text);

            steps.push({
                questionKey: q.id,
                titre: q.titre,
                answers,
                days: result.days,
            });
        }
    }

    if (steps.some((s) => s.days < 0)) {
        return { status: 'lifetime', days: null, steps };
    }

    if (steps.length > 0) {
        // Durées connues uniquement. Si aucune (ex. vaccin « je ne sais pas »,
        // days null), la durée globale reste indéterminée (null) plutôt que
        // -Infinity (Math.max() sur tableau vide).
        const knownDays = steps
            .filter((s) => s.days !== null)
            .map((s) => s.days);

        return {
            status: 'temporary',
            days: knownDays.length > 0 ? Math.max(...knownDays) : null,
            steps,
        };
    }

    return { status: 'eligible', days: null, steps: [] };
}
