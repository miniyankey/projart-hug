# Wayfinder — guide complet (projet Needle)

> Comprendre de A à Z comment Laravel Wayfinder relie le **back Laravel** au **front Vue/Inertia**, avec les exemples réels de ce projet.

---
                  
## 1. Le problème que Wayfinder résout

Sans Wayfinder, côté Vue tu écris tes URLs **en dur** :

```vue
<Link href="/admin/collectes/3/edit">  <!-- ❌ -->
```

Problèmes :

- Si tu renommes la route côté Laravel (`/admin/collectes` → `/admin/campagnes`), **rien** ne te prévient côté front. Lien mort silencieux.
- Aucune vérification : une faute de frappe (`/admin/colectes`) passe inaperçue jusqu'au clic.
- Construire une URL avec paramètres se fait à la main (concaténation de strings).

**Wayfinder génère, à partir de tes routes Laravel, des fonctions TypeScript typées.** Tu appelles une fonction au lieu d'écrire une string. Si la route n'existe pas ou si tu oublies un paramètre, TypeScript te le dit **avant** l'exécution.

```vue
<Link :href="routes.home.url()">                          <!-- ✅ string typée -->
<Link :href="adminCollectes.edit.url({ collecte: 3 })">   <!-- ✅ paramètre vérifié -->
```

---

## 2. Vue d'ensemble du cycle

```
┌─────────────────┐   php artisan        ┌──────────────────────┐   import      ┌─────────────┐
│  Back Laravel   │   wayfinder:generate │  Fichiers TS générés │   @/routes    │  Front Vue  │
│                 │ ───────────────────► │                      │ ────────────► │             │
│ routes/web.php  │   (ou plugin Vite    │ resources/js/routes  │   @/actions   │ <Link>,     │
│ Controllers     │    en watch)         │ resources/js/actions │               │ router.post │
└─────────────────┘                      └──────────────────────┘               └─────────────┘
```

1. Tu définis tes routes/contrôleurs côté **Laravel** (source de vérité).
2. Wayfinder **scanne** ces routes et **génère** des fichiers `.ts`.
3. Le **front** importe ces fonctions générées et les utilise pour tous ses liens et requêtes.

Point clé : **on ne modifie jamais les fichiers générés à la main.** Ils sont régénérés à chaque build et sont d'ailleurs gitignorés (voir §8).

---

## 3. Côté back : la source de vérité

### 3.1 Routes nommées (`routes/web.php`)

```php
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
```

- L'URL (`/locale`), la méthode HTTP (`POST`) et le **nom** (`locale.update`) sont définis ici.
- Le **nom** détermine le chemin d'import côté front (les `.` deviennent une arborescence : `locale.update` → `@/routes/locale`).

### 3.2 Contrôleurs

```php
// app/Http/Controllers/LocaleController.php
class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse { /* ... */ }
}
```

Wayfinder génère **deux familles de fichiers** à partir de ça (voir §5) :

- une entrée **« routes »** indexée par le nom de la route (`locale.update`),
- une entrée **« actions »** indexée par `Controller@méthode` (`LocaleController.update`).

Les deux pointent vers la même URL. Le choix de l'une ou l'autre est une question de style (voir §6).

---

## 4. La génération

Deux façons de (re)générer les fichiers TS :

| Méthode | Quand |
|---|---|
| **Plugin Vite** (automatique) | Pendant `sail npm run dev` — régénération à chaud quand tu touches une route. C'est le cas par défaut ici. |
| **Commande Artisan** (manuelle) | `php artisan wayfinder:generate` — ponctuel, ou en CI. |

Le plugin est configuré dans `vite.config.js` :

```js
import { wayfinder } from '@laravel/vite-plugin-wayfinder';

export default defineConfig({
    plugins: [
        // ...
        wayfinder({
            formVariants: true,   // génère aussi les variantes .form (voir §7.3)
        }),
    ],
});
```

> ⚠️ Si tu ajoutes une route et qu'elle n'apparaît pas côté front : c'est presque toujours que la génération n'a pas tourné. Relance `sail npm run dev` ou `php artisan wayfinder:generate`.

---

## 5. Anatomie d'un fichier généré

Prenons `resources/js/routes/locale/index.ts` (route `locale.update`). Wayfinder génère un **objet-fonction** : une fonction qui porte aussi des méthodes attachées.

```ts
// Appel principal → renvoie { url, method }
export const update = (options?): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

// La définition brute
update.definition = {
    methods: ["post"],
    url: '/locale',
}

// .url() → renvoie UNIQUEMENT la string d'URL
update.url = (options?) => {
    return update.definition.url + queryParams(options)
}

// .post() → renvoie { url, method: 'post' }
update.post = (options?): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

// .form → variante pour les <Form> / <form> natifs (voir §7.3)
update.form = updateForm
```

Donc pour **une** route, tu as plusieurs points d'entrée :

| Tu appelles | Tu obtiens | Usage typique |
|---|---|---|
| `update.url()` | `"/locale"` (string) | un attribut `href`, `action`, ou 1er arg de `router.post` |
| `update()` ou `update.post()` | `{ url: "/locale", method: "post" }` | passé à `router.visit()` / `<Link :href>` (Inertia lit method+url) |
| `update.form()` | `{ action: "/locale", method: "post" }` | un `<Form>` Inertia / `<form>` HTML |
| `update.definition` | `{ methods: ["post"], url: "/locale" }` | rare, introspection |

---

## 6. Routes vs Actions : deux portes, même maison

Wayfinder génère deux arborescences. Dans ce projet on utilise **les deux** selon l'endroit :

### 6.1 Import « routes » (par nom de route)

```ts
// resources/js/components/Navbar.vue
import * as routes from '@/routes/index.ts';

routes.home.url()           // "/"
routes.trophee.url()        // "/trophee"
routes.eligibilite.url()    // ...
```

`import * as routes` importe **tout** le module dans un objet `routes`. Pratique pour une navbar qui touche plein de routes.

### 6.2 Import « action » (par contrôleur)

```ts
// resources/js/components/LanguageSwitcher.vue
import LocaleController from '../actions/App/Http/Controllers/LocaleController';

LocaleController.update.url()   // "/locale"
```

Ici on importe l'**action** d'un contrôleur précis. Plus lisible quand on cible une méthode de contrôleur spécifique.

> Les deux mènent à la même URL. Dans le doute, suis la convention du fichier voisin (cf. CLAUDE.md : « check sibling files »).

### 6.3 Arborescence réelle du projet

```
resources/js/
├── routes/
│   ├── index.ts                 # home, trophee, collecte, eligibilite, certification, login, logout
│   ├── locale/index.ts          # locale.update
│   ├── admin/
│   │   ├── collectes/index.ts   # admin.collectes.edit, .index, ...
│   │   ├── entreprises/index.ts
│   │   ├── vainqueurs/index.ts
│   │   └── kpi/index.ts
│   └── cobrand/index.ts
└── actions/
    └── App/Http/Controllers/
        ├── LocaleController.ts
        └── AdminAuthController.ts
```

L'arborescence des `routes/` reflète les **noms** de routes : `admin.collectes.edit` → `routes/admin/collectes` → export `edit`.

### 6.4 Sont-elles vraiment équivalentes ?

Pour le **résultat** : oui. `routes.locale.update.url()` et `LocaleController.update.url()` renvoient la même URL (`"/locale"`) et exposent les mêmes terminaisons (`.url()`, `.post()`, `.form()`...). Quand les deux existent, elles sont **interchangeables**.

Mais « équivalentes » avec deux réserves importantes.

**Réserve 1 — les deux n'existent pas toujours.** L'existence de chaque entrée dépend de la façon dont la route est déclarée côté Laravel :

| Côté Laravel | Entrée `routes/` (par nom) | Entrée `actions/` (par contrôleur) |
|---|---|---|
| Route nommée **+** contrôleur | ✅ | ✅ |
| Route avec contrôleur **sans** `.name()` | ❌ (pas de nom → rien à indexer) | ✅ |
| Route nommée mais **closure** (pas de contrôleur) | ✅ | ❌ (pas de méthode à cibler) |

Exemple dans ce projet : les pages publiques (`trophee`, `eligibilite`...) passent par le **contrôleur générique d'Inertia** (`Inertia\Controller::__invoke`), pas par un contrôleur applicatif à nous → l'accès naturel est `@/routes` (par nom). À l'inverse, `LocaleController` est notre propre contrôleur → l'entrée `@/actions/.../LocaleController` existe et est naturelle. Selon la route, **une seule** des deux portes peut donc être disponible.

**Réserve 2 — conceptuellement, deux angles différents.** Ce n'est pas « la même chose en double », c'est le même pont vu sous deux angles :

- `@/routes` indexe par **nom de route** → répond à « *quelle est l'URL de la route nommée X ?* »
- `@/actions` indexe par **contrôleur/méthode** → répond à « *où mène la méthode `update` de `LocaleController` ?* »

Quand une route a un nom **et** un contrôleur, les deux angles convergent vers la même URL → interchangeables. Quand l'un manque côté Laravel, l'une des portes n'est tout simplement pas générée.

> **Règle pratique** : si les deux existent, choisis celle qui rend l'import le plus **lisible** au point d'appel et reste cohérent avec le fichier voisin. Sinon, prends celle qui est disponible. (Dans ce projet : la Navbar utilise `@/routes` car elle cible plein de pages par nom ; le LanguageSwitcher utilise `@/actions` car il cible une méthode de contrôleur précise.)

---

## 7. Les 4 façons d'utiliser une route côté front

### 7.1 Un lien simple — `<Link :href>`

```vue
<script setup>
import * as routes from '@/routes/index.ts';
</script>

<template>
  <Link :href="routes.trophee.url()">Trophée</Link>
</template>
```

`:href` (binding Vue) **évalue le JS** ; `.url()` renvoie la string. Résultat : `<a href="/trophee">`.

### 7.2 Une requête programmatique — `router.post(...)`

Exemple réel du `LanguageSwitcher` :

```js
import { router } from '@inertiajs/vue3';
import LocaleController from '../actions/App/Http/Controllers/LocaleController';

router.post(
    LocaleController.update.url(),          // "/locale"
    { locale },                            // payload
    { preserveScroll: true, preserveState: false },
);
```

`.url()` fournit la cible ; `router.post` envoie la requête sans quitter le SPA.

### 7.3 Un formulaire — la variante `.form`

Comme `formVariants: true` est activé dans `vite.config.js`, chaque route a une variante `.form` qui renvoie `{ action, method }`, taillée pour le composant `<Form>` d'Inertia :

```vue
<script setup>
import LocaleController from '@/actions/App/Http/Controllers/LocaleController';
import { Form } from '@inertiajs/vue3';
</script>

<template>
  <Form v-bind="LocaleController.update.form()">
    <input type="hidden" name="locale" value="en" />
    <button type="submit">English</button>
  </Form>
</template>
```

`v-bind="...form()"` répand `{ action: "/locale", method: "post" }` sur le `<Form>`.

### 7.4 Avec query string — l'argument `options`

Toutes les fonctions acceptent un `options` final pour les query params :

```js
routes.trophee.url({ query: { annee: 2009 } })   // "/trophee?annee=2009"
```

---

## 8. Routes avec paramètres (route model binding)

C'est là que Wayfinder brille. Pour `admin.collectes.edit` dont l'URL Laravel est `/admin/collectes/{collecte}/edit`, le fichier généré attend un argument :

```ts
export const edit = (
    args: { collecte: string | number } | [collecte: string | number] | string | number,
    options?
): RouteDefinition<'get'> => ({ url: edit.url(args, options), method: 'get' })
```

Trois façons **équivalentes** de passer le paramètre :

```js
import * as adminCollectes from '@/routes/admin/collectes';

adminCollectes.edit.url({ collecte: 3 })   // objet nommé (le plus clair)
adminCollectes.edit.url([3])               // tableau positionnel
adminCollectes.edit.url(3)                 // valeur brute (si un seul param)
// → tous renvoient "/admin/collectes/3/edit"
```

En interne, la fonction normalise l'argument puis remplace le placeholder :

```ts
return edit.definition.url
    .replace('{collecte}', parsedArgs.collecte.toString())   // {collecte} → 3
    .replace(/\/+$/, '') + queryParams(options)
```

> Si tu oublies le paramètre (`edit.url()`), **TypeScript refuse de compiler** — exactement le filet de sécurité recherché.

Tu peux aussi passer directement un **modèle Eloquent** sérialisé qui contient la bonne clé : `edit.url(collecte)` où `collecte = { id, ... }` fonctionne si la clé attendue est présente (Wayfinder lit la clé de route définie côté Laravel).

---

## 9. Cas d'usage concrets de ce projet

```js
// Navbar publique
import * as routes from '@/routes/index.ts';
routes.home.url();            // "/"
routes.collecte.url();        // CTA "créer une collecte"
routes.certification.url();   // Label CTS

// Changement de langue (LanguageSwitcher)
import LocaleController from '@/actions/App/Http/Controllers/LocaleController';
router.post(LocaleController.update.url(), { locale }, { preserveState: false });

// Édition admin avec paramètre
import * as adminCollectes from '@/routes/admin/collectes';
<Link :href="adminCollectes.edit.url({ collecte: collecte.id })">Éditer</Link>
```

---

## 10. Workflow quand tu ajoutes une route

1. **Back** : définis la route + son nom dans `routes/web.php`.
   ```php
   Route::get('/inscription', InscriptionController::class)->name('inscription');
   ```
2. **Génère** : `sail npm run dev` en watch le fait automatiquement (sinon `php artisan wayfinder:generate`).
3. **Front** : importe et utilise.
   ```vue
   import * as routes from '@/routes/index.ts';
   <Link :href="routes.inscription.url()">S'inscrire</Link>
   ```

Tant que l'étape 1 n'est pas faite, `routes.inscription` est `undefined` → `routes.inscription.url()` jette `Cannot read properties of undefined`. **Pas de route Laravel = pas de fonction Wayfinder.**

---

## 11. Pièges & bonnes pratiques

- **Jamais d'URL en dur côté Vue** (règle projet). Toujours passer par Wayfinder.
- **Ne pas éditer** `resources/js/routes/**` ni `resources/js/actions/**` : régénérés à chaque build, et **gitignorés** (`.gitignore` lignes `/resources/js/actions`, `/resources/js/routes`, `/resources/js/wayfinder`). Conséquence : après un `git clone`, il faut lancer un build (`npm run dev`/`build`) pour que les fichiers existent, sinon les imports `@/routes` cassent.
- **Route renommée côté Laravel** → régénère, et l'import côté front change peut-être de chemin (l'arbo suit le nom).
- **`.url()` (string) vs `()`/`.post()` (objet)** : un `href` veut une string ; `router.visit`/`<Link>` accepte aussi l'objet `{ url, method }`.
- **Différence avec `route()` Laravel** : le helper PHP `route('locale.update')` reste pour le **back** (redirections, mails). Wayfinder, c'est l'équivalent typé pour le **front**.

---

## 12. Récap mental

```
route nommée Laravel  ──gen──►  objet-fonction TS  ──►  .url()      → string  → href / cible
(source de vérité)              (typé, gitignoré)       ()/.post()  → {url,method} → router / <Link>
                                                        .form()     → {action,method} → <Form>
                                                        (args)      → injecte les paramètres {id}
```

Wayfinder = **un pont typé et auto-généré entre tes routes Laravel et ton code Vue**. Tu décris la route une fois côté back ; le front la consomme sans jamais deviner ni recopier une URL.
```
