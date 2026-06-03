# Spécification du jeu d'éligibilité au don de sang

## Contexte général

Le jeu est un parcours interactif qui pose à l'utilisateur une série de questions pour déterminer s'il est éligible au don de sang. Il se présente comme une carte pixel art que l'utilisateur parcourt en scrollant. À chaque checkpoint correspond une question. À la fin du parcours, l'utilisateur est soit invité à s'inscrire à une collecte, soit informé de son inéligibilité (temporaire ou permanente). L'inéligibilité est aussi informée lors de la réponse inéligible à une question.

Le jeu est intégré dans une page Inertia Vue. Le quiz (structure + traductions) est défini **côté front** (voir §3), pas en base de données. L'état du jeu est maintenu en mémoire — si l'onglet est fermé, la progression est perdue et le jeu recommence.

---

## 1. Style global

### Identité visuelle

Le style est **pixel art néo-brutaliste** :
- Bordures noires épaisses, ombres portées solides, pas de dégradé
- Typographie Pochy (bulles de dialogue) : **Press Start 2P** (déjà ajoutée)
- Typographie UI (boutons, labels, messages) : définie par les styles globaux du site

### Couleurs dynamiques (co-branding)

Les couleurs interactives du jeu s'adaptent à l'entreprise partenaire via la CSS variable `--brand` (fournie par le `CobrandController`). Cela inclut :
- La couleur des checkpoints déverrouillés
- La couleur des boutons primaires (CTA, validation)
- La couleur de la barre de progression

Les couleurs fixes sont :
- **Gris** : checkpoint verrouillé (non encore atteint)
- **Rouge** : checkpoint inéligible, messages d'inéligibilité, titres de refus

### Carte et chemin

La carte est un **grand canvas pixel art** qui occupe plus que la hauteur de l'écran. Elle n'est jamais visible dans sa totalité — seul le point actuel et éventuellement le suivant sont visibles (gros plan permanent). La carte se déplace verticalement au scroll ; Pochy reste **toujours centré à l'écran**.

Le chemin est un tracé sinueux (gauche-droite) qui descend de haut en bas. Sa forme exacte est semi-aléatoire mais doit rester lisible. Le chemin est un **SVG** qui sert à la fois d'élément visuel et de trajectoire pour l'animation MotionPath.

### Checkpoints

Chaque checkpoint est un **cercle** positionné sur le chemin SVG. Il affiche une icône PNG **128×128** fournie par les assets du projet, représentant la thématique de la question (voyage, santé, dentiste…).

**3 états visuels :**

| État | Apparence |
|---|---|
| Verrouillé | Fond gris, icône `?` grisée, pas d'interaction |
| Déverrouillé | Fond couleur `--brand`, icône de la thématique, clickable |
| Inéligible | Fond rouge, icône barrée ou avec croix |

### Pochy

Pochy est le personnage accompagnateur — une poche de sang pixel art avec des yeux et des bras. Il est **toujours centré verticalement à l'écran**, positionné juste avant le checkpoint actif. Il ne se déplace pas : c'est la **carte qui scroll** autour de lui pour le mettre sur le bon checkpoint.

Pochy s'exprime dans des **bulles de dialogue** (style gameboy/RPG) avec la police Press Start 2P. Ses bulles contiennent les questions et les explications.

### Vues de résultat (après réponse)

Chaque réponse déclenche un **panel de résultat** qui s'ouvre en overlay ou en remplacement du contenu :

**Réponse éligible** :
- Titre positif (couleur `--brand`)
- Contenu : `why_question` de la question en cours (explication informative)
- Bouton : "OK" → ferme le panel, l'utilisateur peut scroller vers la suite

**Réponse inéligible temporaire** (avec `ineligibility_days > 0`) :
- Titre rouge : "Attends encore X jours / semaines / heure / mois"
- Message : durée d'inéligibilité calculée et formatée en langage naturel
- Formulaire email optionnel : "Recevoir un rappel par email" + case newsletter
- Bouton secondaire : "Continuer le jeu" → ferme le panel sans inscription
- Bouton retour : "Changer ma réponse" → réaffiche la question avec la réponse précédente pré-sélectionnée

**Réponse inéligible à vie** (`ineligibility_days < 0`) :
- Titre rouge : "Tu n'es pas éligible à vie" 
- Message explicatif
- CTA alternatif : "Faire un don financier" (aide les HUG autrement)
- Bouton : "Tout de même continuer le jeu"
- Bouton retour : "Changer ma réponse"

**Vue spécifique** (si `choice.view` est défini) :
- S'affiche à la place de la vue générique
- Contient `view.message` et `view.text`
- Si `view.button_url` est défini : affiche un bouton CTA
- Si `view.integration_url` est défini : affiche le composant intégré (iframe ou embed)
- Bouton retour : "Changer ma réponse" toujours présent

### Fin du jeu — éligible

Un écran de félicitations s'affiche après la dernière question si l'utilisateur est éligible :
- Message positif avec Pochy
- CTA principal : lien vers l'inscription à la collecte (`collect.link_appointment`)

---

## 2. Animations et mouvements

### Principe général

La carte se déplace, Pochy reste fixe au centre. Le scroll utilisateur fait défiler la carte verticalement. Le jeu est donc un **scrolljacking partiel** : le scroll est intercepté pour animer la carte plutôt que la page.

### Déplacement de la carte (MotionPath)

La bibliothèque **GSAP MotionPath plugin** est utilisée pour déplacer la carte le long du chemin SVG.

**Déclencheur du déplacement :**
1. L'utilisateur ferme le panel de résultat (bouton "OK" ou "Continuer le jeu")
2. Le jeu passe à l'état "en attente de scroll"
3. L'utilisateur scrolle vers le bas
5. Lorsque le checkpoint suivant est atteint, la question correspondante s'affiche automatiquement

**Comportement du scroll :**
- Pendant le déplacement, le scroll natif est désactivé (preventDefault)
- La vitesse de déplacement est proportionnelle au scroll de l'utilisateur
- Le scroll vers le haut peut permettre de revenir en arrière (optionnel, à confirmer)

### Apparition de la question

Lorsque Pochy atteint un checkpoint :
- Le checkpoint passe de l'état verrouillé à déverrouillé (animation de pop/scale)
- La bulle de dialogue de Pochy apparaît en **fade-in + slide-up léger**
- Les choix de réponse apparaissent en **stagger** (un par un, délai de 80ms entre chaque)

### Ouverture et fermeture des panels de résultat

- Ouverture : **slide-up** depuis le bas de l'écran, overlay sombre derrière
- Fermeture : **slide-down**, puis la carte reprend vie

### Barre de progression

En haut de l'écran, une barre de progression indique `question actuelle / total de questions`. Elle se remplit progressivement à chaque question validée. Elle représente le total d'étapes du jeu.

---

## 3. Fonctionnement technique

### Données — quiz côté front + i18n (PAS de base de données)

Le quiz n'est **plus** en base de données. Il vit côté front :

- **`resources/js/data/eligibilityQuiz.js`** — la *structure* (logique) : pour
  chaque question `{ key, type, choices: [{ key, eligible, days, view? }] }`.
  `days` = jours d'inéligibilité (`-1` à vie, `null` aucune). `view` référence
  une vue d'explication. `QUIZ_VIEWS` porte les données non traduisibles (URL de CTA).
- **`resources/js/locales/{fr,en}.json`** sous `eligibilite.quiz.*` — tout le
  *texte* (titre, question, why, libellés des choix, textes des vues), plus
  `eligibilite.ui.*` (boutons, badges) et `eligibilite.ineligible.*` /
  `eligibilite.duration.*` (vues génériques + durées avec pluralisation).
- **`resources/js/composables/useEligibilityQuiz.js`** — assemble structure +
  traductions en objets `question` (réactif au changement de langue) et fournit
  `ineligibleView(days)`.

Les contrôleurs (`CobrandController::jeu`, `EligibiliteController::index`) ne
transmettent **aucune** donnée de question ; ils rendent seulement la page
(+ `company`/`token` en mode co-brandé). Ajouter/traduire une question = éditer
le data module + les locales.

L'objet `question` assemblé garde la forme :

```js
{
  id: 'lifetime', type: 'multiple', titre, question, why_question,
  choices: [{ id: 'hiv', text, descr|null, eligible: false, ineligibility_days: -1, view: null }]
}
```

### Gestion de l'état du jeu

L'état est maintenu en mémoire avec `<KeepAlive>`. Si l'onglet est fermé, la progression est perdue.

```ts
interface GameState {
  currentQuestionIndex: number        // index de la question active
  answers: Record<number, number[]>   // questionId → choiceId(s) sélectionnés
  status: 'playing' | 'ineligible' | 'eligible' | 'finished'
  ineligibilityDays: number | null    // null si éligible ou à vie (-1)
  sessionId: string                   // UUID généré au chargement, stocké en sessionStorage
}
```

### Logique d'éligibilité

**Questions de type `unique`** : un seul choix possible.
- Si `choice.eligible === true` → continuer
- Si `choice.eligible === false` → afficher le panel de résultat inéligible

**Questions de type `multiple`** : plusieurs choix possibles.
- Si au moins un choix a `eligible === false` → le choix avec le `ineligibility_days` le plus prioritaire l'emporte :
  - `ineligibility_days < 0` (à vie) prime sur tout
  - Parmi les positifs, le plus grand prime
- Si tous les choix sélectionnés ont `eligible === true` → continuer

**Vue de résultat** :
- Si le choix a un `view` associé → afficher la vue spécifique
- Sinon → afficher la vue générique (durée ou message à vie)

### Tracking anonyme (analytics)

À chaque question, envoyer un événement au serveur via `POST /kpi/eligibilite-step` :

```json
{
  "session_id": "uuid-généré-au-chargement",
  "collect_id": "id de la collecte active",
  "step": 1,
  "result": "eligible | ineligible | ineligible_lifetime",
  "completed": true
}
```

L'événement est envoyé **après** que l'utilisateur a validé sa réponse, de manière non bloquante (fire & forget).

Lorsque l'utilisateur clique sur le lien d'inscription à la collecte (`link_appointment`), envoyer un événement via `POST /kpi/appointment-click` :

```json
{
  "session_id": "uuid",
  "collect_id": "id",
  "source": "game-end-cta"
}
```

### Génération de la carte

La carte est générée dynamiquement en fonction du **nombre de questions** reçues en props. Chaque question correspond à un checkpoint sur le chemin SVG. Le chemin SVG est pré-dessiné et ses points d'ancrage correspondent aux positions des checkpoints.

### Formulaire email de rappel (à implémenter)

> ⚠️ Le backend pour cette fonctionnalité n'existe pas encore. Le formulaire doit être présent dans l'UI mais peut afficher un message "Bientôt disponible" ou être désactivé le temps que le backend soit prêt.

Données à envoyer (future route) :
- `email` : adresse de rappel
- `newsletter` : boolean
- `eligible_at` : date calculée depuis aujourd'hui + `ineligibility_days`
- `session_id`

### Comportement "Changer ma réponse"

Ce bouton est visible uniquement dans les panels de résultat (éligible ou inéligible). Il ferme le panel et **réaffiche la question courante** avec la réponse précédente **pré-sélectionnée**. L'utilisateur peut modifier sa réponse et re-valider.

### Structure des fichiers (état actuel)

```
pages/CoBranded/Jeu.vue          — orchestrateur (état + phases + overlays)
components/game/
├── GameIntro.vue                — écran de lancement
├── GameMap.vue                  — carte scrollable (caméra + scroll GSAP)
│   ├── GamePath.vue             — <g> SVG du chemin
│   ├── GameDecorations.vue      — <g> SVG des décors (<image> externes)
│   └── GameCheckpoint.vue       — octogone (start / checkpoint coloré / end)
├── GameScene.vue                — coquille plein écran partagée (barre + Pochy
│                                   + icône + slots bubble/content/footer)
├── GameQuestion.vue             — question (utilise GameScene)
├── GameResult.vue               — résultat / explication (utilise GameScene)
├── GameSpeechBubble.vue         — bulle de dialogue de Pochy
├── GameChoice.vue               — bouton de réponse (unique/multiple)
└── GameProgressBar.vue          — barre de progression fine
data/eligibilityQuiz.js          — structure du quiz (logique)
composables/useEligibilityQuiz.js — assemble quiz + i18n, ineligibleView()
lib/eligibility.js               — computeResult() (pur)
lib/gameMap.js                   — génération carte/chemin, tile canvas (pur)
locales/{fr,en}.json             — textes sous eligibilite.quiz.* / .ui.* / .ineligible.*
public/img/game/                 — pochy + deco/*.svg (assets choisis par URL)
```

**Écran de fin** (`GameFinish`, éligible → CTA inscription) : pas encore
implémenté.
