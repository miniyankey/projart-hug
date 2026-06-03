// Structure du quiz d'éligibilité (logique uniquement, sans texte).
// Le texte est dans les locales sous `eligibilite.quiz.*` et résolu par
// le composable useEligibilityQuiz().
//
// Chaque question : { key, type, choices: [{ key, eligible, days, view? }] }
//   - type  : 'unique' | 'multiple'
//   - days  : durée d'inéligibilité en jours (-1 = à vie, null = aucune)
//   - view  : clé d'une vue d'explication (eligibilite.quiz.views.<key>), optionnel
//
// Les vues portent les données non traduisibles (URL de CTA). Le texte des vues
// vit aussi dans les locales (eligibilite.quiz.views.<key>).

export const QUIZ_VIEWS = {
    medication: {},
    travel: {},
    vaccine: {
        buttonUrl: 'https://www.blutspende.ch/fr/dates-de-collecte-de-sang',
    },
};

export const QUIZ = [
    {
        key: 'lifetime',
        type: 'multiple',
        choices: [
            { key: 'hiv', eligible: false, days: -1 },
            { key: 'diabetes', eligible: false, days: -1 },
            { key: 'injection_drugs', eligible: false, days: -1 },
            { key: 'heart', eligible: false, days: -1 },
            { key: 'blood_diseases', eligible: false, days: -1 },
            { key: 'hepatitis', eligible: false, days: -1 },
            { key: 'none', eligible: true, days: null },
        ],
    },
    {
        key: 'health',
        type: 'unique',
        choices: [
            { key: 'yes', eligible: true, days: null },
            { key: 'no', eligible: false, days: 14 },
        ],
    },
    {
        key: 'medication',
        type: 'unique',
        choices: [
            { key: 'yes', eligible: false, days: 28, view: 'medication' },
            { key: 'no', eligible: true, days: null },
        ],
    },
    {
        key: 'travel',
        type: 'unique',
        choices: [
            { key: 'europe', eligible: true, days: null },
            { key: 'other', eligible: false, days: 180, view: 'travel' },
            { key: 'none', eligible: true, days: null },
        ],
    },
    {
        key: 'partner',
        type: 'unique',
        choices: [
            { key: 'yes', eligible: false, days: 120 },
            { key: 'no', eligible: true, days: null },
        ],
    },
    {
        key: 'hospitalization',
        type: 'unique',
        choices: [
            { key: 'none', eligible: true, days: null },
            { key: 'old', eligible: true, days: null },
            { key: 'recent', eligible: false, days: 28 },
            { key: 'admission', eligible: false, days: 365 },
        ],
    },
    {
        key: 'dentist',
        type: 'unique',
        choices: [
            { key: 'no', eligible: true, days: null },
            { key: 'scaling', eligible: false, days: 1 },
            { key: 'extraction', eligible: false, days: 7 },
            { key: 'complications', eligible: false, days: 14 },
        ],
    },
    {
        key: 'tattoo',
        type: 'unique',
        choices: [
            { key: 'tattoo', eligible: false, days: 120 },
            { key: 'piercing', eligible: false, days: 120 },
            { key: 'acupuncture', eligible: false, days: 120 },
            { key: 'none', eligible: true, days: null },
        ],
    },
    {
        key: 'tick',
        type: 'unique',
        choices: [
            { key: 'tick', eligible: false, days: 28 },
            { key: 'other_companion', eligible: true, days: null },
            { key: 'alone', eligible: true, days: null },
        ],
    },
    {
        key: 'vaccine',
        type: 'unique',
        choices: [
            { key: 'none', eligible: true, days: null },
            { key: 'live', eligible: false, days: 28 },
            { key: 'inactivated', eligible: false, days: 2 },
            { key: 'unknown', eligible: false, days: null, view: 'vaccine' },
        ],
    },
];
