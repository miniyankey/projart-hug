# 11. FAQ et pièges courants

Ce chapitre regroupe les questions qui reviennent souvent et les pièges classiques avec Inertia.

## 11.1 Questions fréquentes

### Q : Inertia est-il une SPA ou pas ?

**Oui et non.** Techniquement, c'est une SPA (une seule page HTML, Vue gère tout le rendu après le premier chargement), mais **vous n'avez pas à le penser comme une SPA** : pas de routeur côté client, pas d'API à concevoir, pas de gestion de tokens. Vous écrivez des contrôleurs Laravel comme d'habitude.

### Q : Faut-il quand même créer une API REST ?

**Uniquement si** vous devez exposer vos données à d'autres clients (app mobile, intégration tierce). Pour le frontend web, Inertia se suffit à lui-même.

### Q : Comment gérer le routeur côté client ?

**Il n'y a pas de routeur côté client.** Le routeur est celui de Laravel (`routes/web.php`). C'est lui qui décide quelle page afficher.

### Q : Comment partager le code entre plusieurs pages ?

Comme dans n'importe quelle app Vue :
- **Composants** dans `resources/js/components/`
- **Composables** dans `resources/js/composables/` (`useXxx()`)
- **Layouts** dans `resources/js/layouts/`

### Q : Puis-je utiliser Pinia / Vuex avec Inertia ?

Oui, mais c'est **rarement nécessaire**. La plupart des données viennent du serveur via les props. Utilisez Pinia pour de l'état purement client (ouverture d'un modal global, préférences utilisateur stockées localement, etc.).

### Q : Comment authentifier les utilisateurs ?

Comme dans une app Laravel classique : sessions + cookies. Le starter kit ou Laravel Breeze/Jetstream gèrent ça nativement avec Inertia.

### Q : Et les WebSockets / temps réel ?

Inertia ne gère **pas** le temps réel. Combinez-le avec **Laravel Echo + Reverb/Pusher** pour les fonctionnalités temps réel (notifications, chat). Côté Vue, vous écoutez les évènements et appelez `router.reload({ only: ['notifications'] })` quand un nouveau message arrive.

### Q : Que se passe-t-il si je redéploie pendant qu'un utilisateur a la page ouverte ?

Inertia détecte que la version des assets a changé (header `X-Inertia-Version`) et force un rechargement complet **au prochain clic**. Voir [chapitre 2, section 2.6](02-architecture.md#26-gestion-de-la-version-des-assets).

### Q : Comment afficher une page d'erreur (404, 500) ?

Voir [chapitre 9, section 9.7](09-fonctionnalites-avancees.md#97-exception-handling-personnalisé). En résumé : intercepter les exceptions dans `bootstrap/app.php` et rendre une page Inertia `Errors/404.vue`.

### Q : Inertia fonctionne-t-il avec Livewire ?

Ce sont deux approches **différentes** et **concurrentes**. Il est techniquement possible de les combiner, mais déconseillé. Choisissez l'une ou l'autre selon vos préférences :
- **Livewire** : composants stateful, plus proche du paradigme PHP, pas de JS à écrire
- **Inertia + Vue** : composants Vue modernes, JS à écrire mais réactivité fine

### Q : Mon changement Vue n'apparaît pas, pourquoi ?

Vérifiez que `npm run dev` ou `composer run dev` tourne. Sinon, faites `npm run build`. C'est rappelé dans `CLAUDE.md` du projet.

### Q : Comment passer des données dynamiques à `@vite` dans le Blade ?

Le starter kit le fait déjà :
```blade
@vite(['resources/css/app.css', 'resources/js/app.js',
       "resources/js/pages/{$page['component']}.vue"])
```
Le `$page` est injecté par Inertia dans la vue racine, ce qui permet à Vite de précharger uniquement le composant concerné.

## 11.2 Pièges courants

### 🪤 Oublier `as="button"` sur un `<Link method="delete">`

```vue
<!-- ❌ Mauvais -->
<Link href="/users/42" method="delete">Supprimer</Link>

<!-- ✅ Bon -->
<Link href="/users/42" method="delete" as="button">Supprimer</Link>
```

Sans `as="button"`, le HTML reste un `<a>`. Le clic droit → "Ouvrir dans un nouvel onglet" ferait un GET (pas un DELETE) avec des résultats imprévisibles.

### 🪤 Renvoyer un modèle Eloquent brut

```php
// ❌ Renvoie tous les champs, y compris password
return Inertia::render('Users/Show', ['user' => $user]);
```

```php
// ✅ Contrôle ce qui est exposé
return Inertia::render('Users/Show', [
    'user' => UserResource::make($user),
]);
```

Au minimum, ajoutez `protected $hidden = ['password', 'remember_token'];` sur le modèle.

### 🪤 Charger des shared data coûteuses sans closure

```php
// ❌ Évalué à CHAQUE visite Inertia, même si non utilisé
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'menu' => Menu::with('items.children')->get(), // requête coûteuse !
    ];
}
```

```php
// ✅ Évalué uniquement si la prop est demandée
'menu' => fn () => Menu::with('items.children')->get(),
```

### 🪤 Confondre `Inertia::render` et `view()`

```php
// ❌ Va chercher resources/views/dashboard.blade.php
return view('dashboard', ['users' => $users]);

// ✅ Va chercher resources/js/pages/Dashboard.vue
return Inertia::render('Dashboard', ['users' => $users]);
```

### 🪤 Utiliser `window` ou `document` en SSR

```vue
<script setup>
const w = window.innerWidth; // ❌ Plante en SSR
</script>
```

```vue
<script setup>
import { onMounted, ref } from 'vue';
const w = ref(0);
onMounted(() => (w.value = window.innerWidth));
</script>
```

### 🪤 Soumettre un formulaire avec `<form action="...">` standard

```vue
<!-- ❌ Recharge toute la page, perd l'avantage Inertia -->
<form action="/users" method="POST">
    <input name="name" />
</form>
```

```vue
<!-- ✅ Reste en SPA -->
<script setup>
import { useForm } from '@inertiajs/vue3';
const form = useForm({ name: '' });
</script>
<template>
    <form @submit.prevent="form.post('/users')">
        <input v-model="form.name" />
    </form>
</template>
```

### 🪤 Oublier la directive `@vite` ou `<x-inertia::app>` dans `app.blade.php`

Si vous modifiez `app.blade.php` et oubliez l'un des deux :
- Sans `@vite` : Vue/CSS ne se chargent pas
- Sans `<x-inertia::app>` : pas de `div#app`, Inertia ne peut pas se monter

### 🪤 Ne pas utiliser `usePage()` pour les shared data

```vue
<!-- ❌ Pas de mise à jour quand les shared data changent -->
<script setup>
defineProps({ auth: Object }); // les shared data ne sont PAS dans defineProps
</script>
```

```vue
<!-- ✅ Réactif -->
<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const user = computed(() => usePage().props.auth?.user);
</script>
```

### 🪤 Visiter une URL externe avec `<Link>`

```vue
<!-- ❌ Inertia va essayer de faire une requête XHR vers Google -->
<Link href="https://google.com">Google</Link>
```

```vue
<!-- ✅ Utiliser une balise <a> standard pour les liens externes -->
<a href="https://google.com">Google</a>
```

## 11.3 Comment déboguer une réponse Inertia

### Voir la réponse JSON

Ouvrez les **DevTools → Network**. Cliquez sur un `<Link>`. La requête doit avoir l'en-tête `X-Inertia: true` et la réponse doit être du JSON contenant `{ component, props, url, version }`.

### Voir les shared data

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
console.log(usePage().props);
</script>
```

### Dump côté Laravel

```php
return Inertia::render('Dashboard', [
    'users' => User::all(),
])->with('debug', dd($request->headers->all()));
```

### Vérifier la version des assets

```bash
php artisan tinker --execute 'echo md5_file(public_path("build/manifest.json"));'
```

## 11.4 Pour aller plus loin

- 📖 Doc officielle Inertia : <https://inertiajs.com>
- 📖 Doc Inertia Laravel : <https://github.com/inertiajs/inertia-laravel>
- 📖 Doc Inertia Vue 3 : <https://github.com/inertiajs/inertia/tree/master/packages/vue3>
- 📖 Wayfinder : <https://github.com/laravel/wayfinder>

## Retour à l'index

🔙 [README.md](README.md) — Retour au sommaire
