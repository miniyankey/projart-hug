// Structure du quiz d'éligibilité (logique uniquement, sans texte).
// Le texte est dans les locales sous `eligibilite.quiz.*` et résolu par
// le composable useEligibilityQuiz().
//
// Chaque question : { key, type, pochy, icon, choices: [{ key, eligible, days, view? }] }
//   - type  : 'unique' | 'multiple'
//   - pochy : variante de la mascotte (suffixe après "pochy-", sans ".png")
//   - icon  : clé d'icône thématique (chemin /img/game/deco/<icon>.svg)
//   - days  : durée d'inéligibilité en jours (-1 = à vie, null = aucune)
//   - view  : clé d'une vue d'explication (eligibilite.quiz.views.<key>), optionnel
//
// Les vues portent les données non traduisibles (URL de CTA). Le texte des vues
// vit aussi dans les locales (eligibilite.quiz.views.<key>).

export const QUIZ_VIEWS = {
    medication: {
        buttonUrl: 'mailto:accueil.donneurs@hug.ch',
    },
    travel: {
        integrationUrl:
            'https://integrations.spenderservice.ch/fr/travel_check/irb/',
    },
    vaccine: {
        buttonUrl: 'https://www.blutspende.ch/fr/dates-de-collecte-de-sang',
    },
};

export const QUIZ = [
    {
        key: 'lifetime',
        type: 'multiple',
        pochy: '0',
        icon: 'question-mark',
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
        pochy: '10',
        icon: 'heart',
        choices: [
            { key: 'yes', eligible: true, days: null },
            { key: 'no', eligible: false, days: 14 },
        ],
    },
    {
        key: 'medication',
        type: 'unique',
        pochy: '20',
        icon: 'pharmacy',
        choices: [
            { key: 'yes', eligible: false, days: 28, view: 'medication' },
            { key: 'no', eligible: true, days: null },
        ],
    },
    {
        key: 'travel',
        type: 'multiple',
        pochy: '20-travel',
        icon: 'airplane',
        choices: [
            { key: 'europe', eligible: true, days: null },
            { key: 'other', eligible: false, days: 180, view: 'travel' },
            { key: 'none', eligible: true, days: null },
        ],
    },
    {
        key: 'partner',
        type: 'unique',
        pochy: '30',
        icon: 'fruits',
        choices: [
            { key: 'yes', eligible: false, days: 120 },
            { key: 'no', eligible: true, days: null },
        ],
    },
    {
        key: 'hospitalization',
        type: 'unique',
        pochy: '40',
        icon: 'hospital',
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
        pochy: '50-teeth',
        icon: 'tooth-mask',
        choices: [
            { key: 'no', eligible: true, days: null },
            { key: 'scaling', eligible: false, days: 1 },
            { key: 'extraction', eligible: false, days: 7 },
            { key: 'complications', eligible: false, days: 14 },
        ],
    },
    {
        key: 'tick',
        type: 'unique',
        pochy: '60-adventurer',
        icon: 'tick-trees',
        choices: [
            { key: 'tick', eligible: false, days: 28 },
            { key: 'other_companion', eligible: true, days: null },
            { key: 'alone', eligible: true, days: null },
        ],
    },
    {
        key: 'tattoo',
        type: 'multiple',
        pochy: '80-tattooed',
        icon: 'tattoo',
        choices: [
            { key: 'tattoo', eligible: false, days: 120 },
            { key: 'piercing', eligible: false, days: 120 },
            { key: 'acupuncture', eligible: false, days: 120 },
            { key: 'none', eligible: true, days: null },
        ],
    },
    {
        key: 'vaccine',
        type: 'multiple',
        pochy: '90',
        icon: 'syringe',
        choices: [
            { key: 'live', eligible: false, days: 28 },
            { key: 'inactivated', eligible: false, days: 2 },
            { key: 'unknown', eligible: false, days: null, view: 'vaccine' },
            { key: 'none', eligible: true, days: null },
        ],
    },
];
