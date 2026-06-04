# Marche à suivre — évolutions du jeu d'éligibilité

Document de planification. Chaque section = un lot de modifications, avec
objectif, étapes concrètes, fichiers touchés et points à trancher.

## Ordre d'implémentation conseillé

1. **Boutons shadcn** (§1) — fondation UI, indépendant.
2. **Angles du chemin** (§2) — petit fix isolé.
3. **UX scroll** (§3) — indépendant.
4. **Données Pochy/icônes + réordonnancement** (§4) — pré-requis de §5 et §6.
5. **Composant Pochy** (§5) — dépend de §4.
6. **Pochy selon le résultat** (§6) — dépend de §5.
7. **Page de fin** (§7).
8. **Écran de chargement** (§8).

---

## 1. Boutons = composants shadcn

**Objectif** : remplacer les boutons custom du jeu par le composant
`@/components/ui/button` (variantes cva existantes), et créer une variante pour
les choix de réponse (avec description + case à cocher).

**Étapes**

- [ ] Remplacer les CTA du jeu par `<Button>` :
  - `GameIntro` (JOUER) → `variant="pixel_white"` (clair sur le fond de marque).
  - `GameScene` footer (Valider / OK) → `variant="pixel_violet"` ; « Retour » → `variant="link"`.
  - `GameResult` CTA → `variant="pixel_violet"`.
  - → supprime les `:slotted(.game-cta/.game-back)` de `GameScene` et les classes Tailwind dupliquées.
- [ ] Ajouter une variante de choix dans `components/ui/button/index.ts`, ex. `quiz` :
  bordure `var(--brand)`, ombre `var(--brand-shadow)`, état sélectionné (fond brand,
  texte blanc, à plat). État « pressé/sélectionné » géré via classes (cf. GameChoice actuel).
- [ ] Refondre `GameChoice` pour **composer** `<Button variant="quiz">` :
  slot par défaut = case à cocher (si `multiple`) + bloc label/description.
- [ ] Vérifier l'alignement multi-lignes (label + description) : prévoir
  `h-auto`, `items-start`, `text-left` sur le bouton.

**Fichiers** : `components/ui/button/index.ts`, `GameChoice.vue`, `GameScene.vue`,
`GameIntro.vue`, `GameResult.vue`, `GameQuestion.vue`.

---

## 2. Parcours au scroll — supprimer l'arrondi des angles

**Objectif** : Pochy suit le chemin en angles droits nets (pas de courbe de
Bézier dans les coins H↔V).

**Étapes**

- [ ] Dans `lib/gameMap.js` : retirer `computeCorners` et la branche bezier de
  `smoothPos` → ne garder que l'interpolation linéaire le long des segments.
- [ ] Dans `GameMap.vue` : supprimer `cornerData`, la constante `CORNER_BLEND`,
  et calculer la position linéairement (`curPos = posAt(segments, progress)`).

**Fichiers** : `lib/gameMap.js`, `GameMap.vue`.

---

## 3. UX — rendre le « scroll pour avancer » évident

**Constat** : en test, les utilisateurs ne comprenaient pas qu'il faut scroller
(certains cliquaient la case suivante, d'autres utilisaient les flèches).

**Solution — accepter tous les inputs + indice visuel** :

- [ ] **Indice visuel** : flèche/chevron animé (bounce) + texte « Scrolle pour
  avancer » près de Pochy, visible tant que l'utilisateur n'a pas scrollé
  (disparaît au premier déplacement, réapparaît après inactivité).
- [ ] **Flèches clavier** : `ArrowDown`/`ArrowUp` (+ `Espace`/`PageDown`) font
  avancer/reculer le `progress` (mêmes bornes que la molette, via `cap`).
- [ ] **Clic sur le checkpoint suivant** : cliquer le prochain checkpoint
  verrouillé anime Pochy jusqu'à lui (`progress` → `cap`) puis ouvre la question.

**Fichiers** : `GameMap.vue` (clavier + clic checkpoint suivant + état « a déjà
scrollé »), `GameScrollHint.vue` (nouveau), locales (`eligibilite.ui.scroll_hint`).

---

## 4. Données : variante de Pochy + icône thématique par question (+ réordonnancement)

**Objectif** : chaque question référence (a) une variante de mascotte (thème +
remplissage) et (b) une icône thématique (affichée à côté de Pochy). Les images
existent → on **prévoit juste les clés dans le JSON**.

**Réordonnancement** : déplacer `tattoo` entre `tick` et `vaccine` (remplissage
croissant). Nouvel ordre :
`lifetime, health, medication, travel, partner, hospitalization, dentist, tick, tattoo, vaccine`.

**Mapping mascotte (clé `pochy`)** — convention `<thème>-<fill>` :

| question        | pochy           |
| --------------- | --------------- |
| lifetime        | `normal-0`      |
| health          | `normal-10`     |
| medication      | `normal-20`     |
| travel          | `travel-20`     |
| partner         | `normal-30`     |
| hospitalization | `normal-40`     |
| dentist         | `teeth-50`      |
| tick            | `adventurer-60` |
| tattoo          | `tattooed-80`   |
| vaccine         | `normal-90`     |

**Icône thématique (clé `icon`)** — proposition (à ajuster selon les assets) :
lifetime→`heart`, health→`health`, medication→`pill`, travel→`plane`,
partner→`partner`, hospitalization→`hospital`, dentist→`tooth`, tick→`tick`,
tattoo→`tattoo`, vaccine→`syringe`.

**Étapes**

- [ ] `data/eligibilityQuiz.js` : réordonner + ajouter `pochy` et `icon` à chaque entrée.
- [ ] Chemins d'assets : mascottes dans **`public/image/pochy/<clé>.png`**,
  icônes dans `public/image/icons/<clé>.svg`.
- [ ] `useEligibilityQuiz` : exposer `pochy` et `icon` (URL résolue, ex.
  `/image/pochy/${pochy}.png`) dans l'objet `question`.

**Fichiers** : `data/eligibilityQuiz.js`, `composables/useEligibilityQuiz.js`,
`public/image/pochy/`, `public/image/icons/`.

> ⚠️ Les mascottes sont dans `public/image/…` (« image »), alors que le reste du
> site utilise `public/img/`. Convention retenue : `/image/pochy/…`. Juste
> vérifier que le dossier est bien `image`. Noms de fichiers = clés ci-dessus.

---

## 5. Composant Pochy + remplissage au fil des étapes

**Objectif** : un composant unique gère l'affichage de Pochy (par variante) ;
Pochy « se remplit » au passage des étapes (image différente à chaque question).

**Étapes**

- [ ] Créer `components/game/GamePochy.vue` : prop `variant` (clé) →
  `<img :src="/image/pochy/${variant}.png" :alt>` (avec `image-rendering: pixelated`).
- [ ] Remplacer les `/img/mascotte.png` codés en dur par `<GamePochy :variant="…" />`
  dans `GameMap`, `GameScene` (question/résultat) et `GameIntro` (défaut `normal-0`).
- [ ] Carte : la variante suit l'étape courante → Pochy se remplit en avançant.
- [ ] Écran question : Pochy = variante de la question courante.

**Fichiers** : `GamePochy.vue` (nouveau), `GameMap.vue`, `GameScene.vue`,
`GameIntro.vue`, `Jeu.vue`.

---

## 6. Pochy selon le résultat de la réponse

**Règle**

- Inéligible **à vie** → `sad-0` (« pochy 0 sad »).
- Inéligible **temporaire** → `time-0` (« pochy 0 time »).
- **Éligible** → on garde le Pochy de base de la question.

**Étapes**

- [ ] L'objet résultat (`useEligibilityQuiz` / `Jeu.vue`) porte un champ `pochy` :
  `sad-0` (à vie), `time-0` (temporaire), variante de la question (éligible).
- [ ] `GameResult` : prop `pochy` transmise à `GamePochy`.
- [ ] `Jeu.vue` : alimente la bonne variante selon le branchement de `onAnswer`.

**Fichiers** : `composables/useEligibilityQuiz.js`, `GameResult.vue`,
`GameScene.vue`, `Jeu.vue`.

---

## 7. Page de fin du jeu

**Objectif** : après la dernière question, un écran de verdict.

- **Éligible** → message positif + CTA d'inscription.
- **Inéligible** → verdict basé sur **la durée la plus longue** + **résumé de
  toutes les étapes inéligibles** (durée / motif). Si temporaire, préciser quand
  le don redeviendra possible.

**Verdict global** (helper pur dans `lib/eligibility.js`, ex.
`overallVerdict(questions, answers)`) retourne :

- `status` : `lifetime` (≥1 réponse à vie) / `temporary` (≥1 temporaire sinon) / `eligible` ;
- `days` : la **plus longue** durée d'inéligibilité temporaire ;
- `steps` : **liste des étapes inéligibles** `{ questionKey, titre, days }` pour
  le résumé (à vie = `days < 0`).

**Étapes**

- [ ] `lib/eligibility.js` : ajouter `overallVerdict(questions, answers)`.
- [ ] Créer `components/game/GameFinish.vue` (réutilise `GameScene` + `GamePochy`) :
  - éligible : Pochy plein/heureux, CTA inscription.
  - inéligible : Pochy `sad-0`/`time-0`, durée la plus longue + **liste récap**
    des étapes inéligibles.
- [ ] `Jeu.vue` : phase `finished` après la dernière étape → affiche `GameFinish`.
- [ ] Textes i18n (`eligibilite.finish.*`).

**Lien d'inscription (CTA)** — tranché :

- **Cobrand** : lien de la collecte (`collect.link_appointment`). Il avait été
  retiré des props → **le re-transmettre** : `CobrandController::jeu` renvoie
  `link_appointment` et `Jeu.vue` le passe à `GameFinish`.
- **Public** (`/eligibilite`) : pas de collecte → CTA vers la prise de
  rendez-vous HUG : `https://www.hug.ch/don-du-sang/rendez-vous-ligne`.

**Fichiers** : `lib/eligibility.js`, `GameFinish.vue` (nouveau), `Jeu.vue`,
`app/Http/Controllers/CobrandController.php`, `locales/{fr,en}.json`.

---

## 8. Écran de chargement

**Objectif** : entre le clic « JOUER » et l'affichage de la carte (lourde à
construire), montrer une courte vue de chargement.

**Étapes**

- [ ] Ajouter une phase `loading` entre `intro` et `map` (`Jeu.vue`).
- [ ] Créer `components/game/GameLoading.vue` (Pochy + animation pixel /
  barre indéterminée), réutilise éventuellement `GamePochy`.
- [ ] Au clic JOUER → `phase = 'loading'`. Passer à `map` quand la carte est
  prête : `GameMap` émet `ready` (après `rebuild()` / `onMounted`), avec une
  **durée minimale** d'affichage (ex. 600 ms) pour éviter un flash.
- [ ] Texte i18n (`eligibilite.ui.loading`).

**Fichiers** : `GameLoading.vue` (nouveau), `GameMap.vue` (émet `ready`),
`Jeu.vue`, `locales/{fr,en}.json`.

---

## Récapitulatif des nouveaux fichiers / assets

- Composants : `GamePochy.vue`, `GameFinish.vue`, `GameLoading.vue`, `GameScrollHint.vue`.
- Variante Button : `quiz` dans `components/ui/button/index.ts`.
- Assets à déposer : `public/image/pochy/*.png`, `public/image/icons/*.svg`
  (dont `sad-0`, `time-0` pour les résultats).
- Clés i18n : `eligibilite.finish.*`, `eligibilite.ui.{scroll_hint,loading}`.
- Backend : `CobrandController::jeu` re-transmet `link_appointment`.

## Décisions actées

1. **Assets mascottes** : `public/image/pochy/` (vérifier `image` vs `img`).
2. **Lien d'inscription** : cobrand = lien de la collecte ; public = `https://www.hug.ch/don-du-sang/rendez-vous-ligne`.
3. **Verdict** : durée la plus longue **+ résumé de toutes les étapes inéligibles**.
