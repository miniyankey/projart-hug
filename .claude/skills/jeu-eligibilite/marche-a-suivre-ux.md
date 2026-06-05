# Marche à suivre — ajustements UX du jeu d'éligibilité

Document de planification (2ᵉ lot, post-tests UX). Chaque section = une
modification, avec objectif, étapes concrètes, fichiers touchés et points à
trancher. Aucune implémentation n'est faite ici : ce document sert de plan.

## Ordre d'implémentation conseillé

1. **Données : option « aucun »** (§1) — pré-requis logique, indépendant.
2. **Refonte layout des vues de question** (§2 → §7) — tout dans
   `GameScene` / `GameQuestion` / `GameChoice`, à faire d'un bloc cohérent.
3. **Carte** (§8 icônes, §9 indice scroll) — indépendants.
4. **Routing** (§10) — isolé, backend.
5. **Écran de fin** (§11 → §13) — `GameFinish` + `lib/eligibility`.

---

## 1. Option « aucun » dans les choix multiples

**Constat UX** : sans choix « aucun » explicite, l'utilisateur ne sait pas
comment exprimer « rien ne me concerne » sur une question à choix multiple
(valider à vide n'est pas évident).

**Objectif** : réintroduire un choix `none` (éligible, sans délai) sur chaque
question à choix multiple qui n'en a pas.

**Questions à choix multiple actuelles** (`data/eligibilityQuiz.js`) :
`lifetime`, `travel`, `tattoo`, `vaccine` — aucune n'a de `none`.

**Étapes**

- [ ] `data/eligibilityQuiz.js` : ajouter en **dernier** choix de chaque
  question multiple `{ key: 'none', eligible: true, days: null }`.
- [ ] `locales/fr.json` + `locales/en.json` : ajouter
  `eligibilite.quiz.<question>.choices.none.response` (objet `{ response }`,
  format actuel) pour `lifetime`, `travel`, `tattoo`, `vaccine`. Propositions FR :
  - lifetime → « Aucune de ces situations »
  - travel → « Je n'ai pas voyagé »
  - tattoo → « Aucun, je suis parfait comme ça 😎 »
  - vaccine → « Aucun vaccin récent »
- [ ] **Exclusivité** (recommandé) : dans `GameQuestion.toggle()`, si l'utilisateur
  coche `none` → vider les autres ; s'il coche un autre choix → retirer `none`.
  Sinon « aucun » + une réponse inéligible reste possible (incohérent à l'écran).

**Logique** : aucun changement requis dans `computeResult` — un choix éligible
de plus ne change pas le verdict (seuls les inéligibles comptent).

**Fichiers** : `data/eligibilityQuiz.js`, `locales/{fr,en}.json`,
`components/game/GameQuestion.vue` (exclusivité).

**À trancher** : exclusivité du choix « aucun » (recommandé : oui).

---

## 2. Bouton de validation plus gros et repositionné

**Constat** : le bouton « Continuer » (questions à choix multiple) est dans le
pied de page (`#footer`) — peu visible, mal placé.

**Objectif** : bouton plus grand, rapproché des choix (sous la liste de
réponses), pas en bas de l'écran.

**Étapes**

- [ ] `GameQuestion.vue` : déplacer le `<Button variant="pixel_violet">` du slot
  `#footer` vers le slot `#content`, **sous** la liste de `GameChoice`.
- [ ] L'agrandir : `size="lg"` (ou padding/texte accrus), éventuellement pleine
  largeur sur mobile (`w-full sm:w-auto`).
- [ ] Garder « Retour » seul dans `#footer` (ou le déplacer aussi, à trancher).
- [ ] Toujours conditionné à `isMultiple` (les choix uniques valident au clic).

**Fichiers** : `GameQuestion.vue` (+ éventuel ajustement `components/ui/button/index.ts`).

**À trancher** : largeur (pleine vs auto) et sort du bouton « Retour ».

---

## 3. Titre + numéro de question plus gros, plus d'air sous la barre

**Objectif** : header des vues question/résultat plus lisible et aéré.

**Étapes**

- [ ] `GameScene.vue` (bloc header) : augmenter la taille du titre `theme`
  (ex. `clamp(0.75rem,1.3vw,1rem)` → plus grand, voire `font-pixel` léger) et du
  compteur `answered/total`.
- [ ] Augmenter l'espace entre `GameProgressBar` et le header (`pt-3` → `pt-6`/`pt-8`,
  ou `mt` sur le header).

**Fichiers** : `GameScene.vue`.

---

## 4. Descendre le contenu de ~10 % du viewport

**Objectif** : mieux centrer verticalement le bloc (bulle, réponses, explication,
CTA) — il est actuellement trop haut.

**Étapes**

- [ ] `GameScene.vue` : ajouter un décalage vertical au corps scrollable
  (`padding-top: 10vh` ou `mt-[10vh]` sur le conteneur interne), en conservant le
  `overflow-y-auto` pour les petits écrans (le décalage ne doit pas masquer le
  footer ni empêcher le scroll).

**Fichiers** : `GameScene.vue`.

**À trancher** : 10 vh fixe vs valeur responsive (réduire sur très petits écrans).

---

## 5. Icônes des vues question : centrées + plus grosses sur mobile ; Pochy plus gros sur PC

**Objectif** : meilleure présence du personnage et de l'icône thématique.

**Étapes**

- [ ] `GameScene.vue` zone personnage :
  - **Mobile** : centrer la rangée icône + Pochy (`justify-center`) et agrandir
    (icône `h-14` → `h-20`+, Pochy `w-20` → plus grand).
  - **Desktop** : agrandir Pochy (`md:w-[64%]` dans une colonne élargie, cf. §6).

**Fichiers** : `GameScene.vue`.

> À coordonner avec §6 (répartition des colonnes) — mêmes lignes.

---

## 6. Vues question : contenu 2/3, icônes 1/3, contenu plus petit

**Objectif** : sur desktop, colonne personnage = **1/3**, colonne contenu =
**2/3** ; réduire légèrement la taille du texte de contenu.

**Étapes**

- [ ] `GameScene.vue` (layout `md:flex-row`) : colonne personnage `md:basis-1/3`
  (Pochy occupant bien ce tiers, cf. §5), colonne contenu `md:basis-2/3`.
- [ ] Réduire la taille des textes de contenu : bulle (`GameSpeechBubble`),
  libellés de choix (`GameChoice`), explication (`GameResult`).

**Fichiers** : `GameScene.vue`, `GameSpeechBubble.vue`, `GameChoice.vue`.

> §5 + §6 forment une seule refonte de la zone personnage/contenu.

---

## 7. Description des choix justifiée à gauche

**Objectif** : la 2ᵉ ligne (`descr`) des boutons de choix doit être alignée à
gauche (lisibilité), pas centrée.

**Étapes**

- [ ] `GameChoice.vue` : ajouter `text-left` au bloc label/description (et vérifier
  que la variante `quiz` n'impose pas de centrage). Le bouton est déjà
  `items-start` ; s'assurer que le texte multi-lignes reste aligné à gauche.

**Fichiers** : `GameChoice.vue` (+ variante `quiz` dans `components/ui/button/index.ts` si besoin).

---

## 8. Icônes de questions sur la carte : plus grosses, sans fond blanc

**Objectif** : retirer le panneau blanc pixel derrière chaque icône de checkpoint
et agrandir l'icône.

**Étapes**

- [ ] `GameMap.vue` (layer 5, icônes au-dessus des checkpoints) : supprimer les
  deux `<rect>` (cadre noir + fond clair) du `v-for` ; agrandir l'`<image>`
  (ex. 48 → 64) et recalculer la position `y` (l'icône doit rester juste au-dessus
  de l'octogone). Garder les grandes icônes départ/arrivée (`start-flag`,
  `grand-hospital`).

**Fichiers** : `GameMap.vue`.

**À trancher** : taille exacte (64 ? 72 ?) — vérifier qu'elles ne se chevauchent
pas entre checkpoints rapprochés.

---

## 9. Indice « Scrolle pour avancer » seulement au lancement

**Constat** : l'indice réapparaît après chaque période d'inactivité (timer 4 s).

**Objectif** : l'afficher uniquement au tout début, jusqu'au premier mouvement.

**Étapes**

- [ ] `GameMap.vue` : dans `markMoved()`, supprimer la réapparition (le
  `setTimeout` qui remet `hasMoved = false`). `hasMoved` devient définitif au 1ᵉʳ
  scroll/clavier/clic → l'indice (`v-if="!hasMoved"`) disparaît et ne revient plus.
- [ ] Nettoyer `inactivityTimer` (déclaration + `clearTimeout` dans `onUnmounted`)
  s'il n'est plus utilisé.

**Fichiers** : `GameMap.vue`.

---

## 10. URL du jeu non co-brandé → `/jeu`

**Objectif** : servir la page publique du jeu sur `/jeu` au lieu de `/eligibilite`.

**État actuel** : `routes/web.php:26`
`Route::get('/eligibilite', [EligibiliteController::class, 'index'])->name('eligibilite');`
Les liens front utilisent Wayfinder (`routes.eligibilite.url()`), pas l'URL en dur.

**Étapes**

- [ ] `routes/web.php` : changer le **path** `'/eligibilite'` → `'/jeu'`.
  ⚠️ **Garder le nom de route `eligibilite`** : le nom `jeu` est déjà pris par la
  route co-brandée (`routes/web.php:107`). Ne pas renommer.
- [ ] Régénérer Wayfinder → `routes.eligibilite.url()` renverra `/jeu`
  automatiquement (aucun lien à modifier).
- [ ] Vérifier l'absence d'URL `/eligibilite` codée en dur (sitemap, redirections,
  e-mails). Optionnel : redirection 301 `/eligibilite` → `/jeu`.

**Fichiers** : `routes/web.php`, fichiers Wayfinder régénérés
(`resources/js/routes/**`).

**À trancher** : ajouter ou non une redirection de l'ancienne URL.

---

## 11. Récap de fin (inéligible) : titre + réponse de l'utilisateur + durée

**Objectif** : pour chaque étape inéligible, afficher le **titre de la question**,
la **réponse choisie** par l'utilisateur, et la **durée d'inéligibilité**.

**Étapes**

- [ ] `lib/eligibility.js` → `overallVerdict()` : enrichir chaque `step` avec les
  libellés des choix inéligibles sélectionnés. Pour chaque question inéligible,
  filtrer `question.choices` sur les `choiceIds` sélectionnés **et** `!eligible`,
  puis récupérer leur `text`. Nouveau `step` :
  `{ questionKey, titre, answers: string[], days }`.
- [ ] `GameFinish.vue` : afficher, par étape, `titre` + liste `answers` + durée
  (`formatDuration`/`formatDays` déjà dispo via le composable).

**Fichiers** : `lib/eligibility.js`, `GameFinish.vue`.

---

## 12. Style du récap de fin plus « pixel art »

**Constat** : l'écran de fin actuel (cartes grises, bordures fines) fait
« généré par défaut », pas raccord avec l'esthétique néo-brutaliste du jeu.

**Objectif** : refondre `GameFinish` aux codes visuels du jeu : bordures noires
épaisses, ombres portées dures, police `font-pixel` pour les titres, panneaux
type `GameSpeechBubble` / `GameChoice`.

**Étapes**

- [ ] `GameFinish.vue` : refondre les blocs (verdict, liste récap, CTA) avec
  `border-[3px]/[4px] border-black`, `shadow-[6px_6px_0_...]`, fonds pleins,
  titres `font-pixel`. Réutiliser les boutons shadcn pixel (`pixel_violet`,
  `pixel_white`). S'inspirer de `GameSpeechBubble.vue` et `GameChoice.vue`.
- [ ] Garder Pochy de résultat (`0-sad` / `0-time` / `100-hand-in-air`).

**Fichiers** : `GameFinish.vue` (CSS Tailwind, pas de `<style>` custom si évitable).

---

## 13. CTA « partager le jeu à ses collègues »

**Objectif** : sur l'écran de fin, inciter au partage du jeu.

**Étapes**

- [ ] `GameFinish.vue` : ajouter un bouton « Partager » (variante pixel) qui :
  - utilise l'**API Web Share** (`navigator.share({ title, text, url })`) quand
    disponible ;
  - **fallback** : copie du lien (`navigator.clipboard.writeText`) + confirmation
    visuelle (« Lien copié ! »).
- [ ] URL partagée : l'URL courante (`window.location.href`) — fonctionne en
  co-brandé (lien de l'entreprise) comme en public (`/jeu`).
- [ ] i18n : `eligibilite.finish.share.{cta,title,text,copied}` (fr + en).
- [ ] Optionnel : tracking KPI du clic partage (cf. `useTracking` /
  `track.*`), si un endpoint est prévu.

**Fichiers** : `GameFinish.vue`, `locales/{fr,en}.json`, (option) `composables/useTracking.js`.

**À trancher** : message de partage par défaut ; tracking ou non.

---

## Récapitulatif des fichiers touchés

- **Données / i18n** : `data/eligibilityQuiz.js`, `locales/{fr,en}.json`.
- **Vues question** : `GameScene.vue`, `GameQuestion.vue`, `GameChoice.vue`,
  `GameSpeechBubble.vue`, `components/ui/button/index.ts`.
- **Carte** : `GameMap.vue`.
- **Fin** : `GameFinish.vue`, `lib/eligibility.js`.
- **Routing** : `routes/web.php` + Wayfinder régénéré.

## Décisions à trancher (récap)

1. **§1** — exclusivité du choix « aucun » (recommandé : oui).
2. **§2** — largeur du bouton Valider + emplacement de « Retour ».
3. **§4** — décalage 10 vh fixe vs responsive.
4. **§8** — taille des icônes de carte (anti-chevauchement).
5. **§10** — redirection 301 de `/eligibilite` vers `/jeu` ?
6. **§13** — message de partage + tracking KPI.
