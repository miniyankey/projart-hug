# 9. Fonctionnalités avancées

Ce chapitre couvre les fonctionnalités d'Inertia v3 qui le rapprochent des frameworks SPA modernes : polling, prefetching, infinite scroll, optimistic updates, etc.

## 9.1 Polling (rafraîchissement périodique)

Pour rafraîchir une page à intervalles réguliers (ex. : notifications, statut de job) :

```vue
<script setup>
import { usePoll } from '@inertiajs/vue3';

// Toutes les 5 secondes, recharger uniquement les notifications
usePoll(5000, {
    only: ['notifications'],
});
</script>
```

Le polling est :
- ✅ Mis en pause quand l'onglet n'est **pas visible** (économie de batterie)
- ✅ Reprend quand l'onglet redevient visible
- ✅ Stoppé automatiquement quand le composant est démonté

### Contrôler le polling

```js
const poll = usePoll(5000, { only: ['notifications'] }, {
    autoStart: false,  // ne démarre pas tout seul
    keepAlive: true,   // continue même si l'onglet est caché
});

poll.start();
poll.stop();
```

## 9.2 Prefetching (préchargement)

Précharger une page **avant** que l'utilisateur clique dessus, pour qu'elle s'affiche instantanément.

### Prefetch au survol

```vue
<Link :href="show(user.id)" prefetch>Profil</Link>
```

Quand la souris passe sur le lien, Inertia fait une requête XHR en avance et la met en cache. Au clic, la navigation est **instantanée**.

### Prefetch immédiat

```vue
<Link :href="show(user.id)" prefetch="mount">Profil</Link>
```

La requête est lancée dès que le composant est monté. À utiliser avec parcimonie (charge réseau).

### Durée de cache

```vue
<Link :href="show(user.id)" prefetch cache-for="30s">Profil</Link>
```

Le cache est valable 30 secondes. Au-delà, une nouvelle requête est faite.

## 9.3 Instant visits (v3)

Les **visites instantanées** rendent la navigation en arrière/avant (boutons du navigateur) ultra-rapide en utilisant les pages précédemment visitées en cache.

```js
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    // ... activé par défaut dans v3
});
```

Pas de configuration : ça marche tout seul. Le `back/forward` du navigateur affiche la page précédente immédiatement, puis met à jour silencieusement si nécessaire.

## 9.4 Infinite scroll

Combinaison de `Inertia::merge()` côté serveur + visites partielles côté client.

### Côté serveur

```php
public function index(Request $request): Response
{
    return Inertia::render('Posts/Index', [
        'posts' => Inertia::merge(fn () => Post::paginate(20)),
    ]);
}
```

### Côté client

```vue
<script setup>
import { router } from '@inertiajs/vue3';
import { useIntersectionObserver } from '@vueuse/core';
import { ref } from 'vue';

const props = defineProps({ posts: Object });
const sentinel = ref(null);

useIntersectionObserver(sentinel, ([entry]) => {
    if (entry.isIntersecting && props.posts.next_page_url) {
        router.visit(props.posts.next_page_url, {
            only: ['posts'],
            preserveScroll: true,
            preserveState: true,
        });
    }
});
</script>

<template>
    <div>
        <article v-for="post in posts.data" :key="post.id">
            {{ post.title }}
        </article>

        <div ref="sentinel" v-if="posts.next_page_url">
            Chargement…
        </div>
    </div>
</template>
```

Grâce à `Inertia::merge()`, les nouveaux `posts.data` sont **concaténés** à l'existant, pas remplacés.

## 9.5 Optimistic updates (v3)

Mettre à jour l'UI **immédiatement** sans attendre la réponse du serveur, et **revenir en arrière** si le serveur échoue.

```vue
<script setup>
import { router } from '@inertiajs/vue3';

function toggleLike(post) {
    router.post(`/posts/${post.id}/like`, {}, {
        optimistic: (page) => {
            // Modifier les props comme si le serveur avait répondu
            const target = page.props.posts.find(p => p.id === post.id);
            if (target) target.liked = !target.liked;
        },
        // Si le serveur répond OK : on garde l'état
        // Si le serveur répond KO : Inertia restaure automatiquement l'ancien état
    });
}
</script>
```

Le rollback en cas d'erreur est **automatique**.

## 9.6 Standalone HTTP requests (`useHttp`) — v3

Avant v3, pour faire une requête XHR qui **ne** modifie **pas** la page Inertia, il fallait utiliser axios ou fetch. v3 introduit `useHttp()` qui partage l'instance HTTP d'Inertia (CSRF, interceptors, etc.) :

```vue
<script setup>
import { useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const http = useHttp();
const suggestions = ref([]);

async function searchUsers(q) {
    const { data } = await http.get('/api/users/search', { params: { q } });
    suggestions.value = data;
}
</script>
```

Avantages :
- Pas besoin d'installer axios séparément
- Le token CSRF est géré automatiquement
- Les interceptors Inertia s'appliquent

> ℹ️ Inertia v3 a **supprimé axios** des dépendances. Vous pouvez l'installer si vous le voulez, mais `useHttp()` couvre 90 % des cas.

## 9.7 Exception handling personnalisé

Pour gérer les erreurs HTTP (404, 500, 403, etc.) avec une page Inertia :

```php
// bootstrap/app.php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
        if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403])) {
            return inertia('Errors/' . $response->getStatusCode())
                ->toResponse($request)
                ->setStatusCode($response->getStatusCode());
        }

        return $response;
    });
})
```

Puis créer `resources/js/pages/Errors/404.vue`, `500.vue`, etc.

## 9.8 Once props (à chaud, jamais re-rendues)

Pour des données qui ne doivent **jamais** être actualisées, même lors d'un `router.reload()` :

```php
return Inertia::render('Editor', [
    'config' => Inertia::once(fn () => Editor::config()),
]);
```

La prop est envoyée au premier rendu et **ignorée** lors des visites partielles ultérieures.

## 9.9 Barre de progression

Configurée dans `app.js` :

```js
createInertiaApp({
    progress: {
        color: '#4B5563',     // couleur de la barre
        delay: 250,           // ne pas afficher si la requête < 250ms
        includeCSS: true,
        showSpinner: false,
    },
});
```

Affiche automatiquement une barre en haut de la page pendant les navigations Inertia. Pour la désactiver :

```js
createInertiaApp({ progress: false });
```

## 9.10 Évènements v3 : renommages importants

| Avant v2 | v3 |
|---|---|
| `router.on('invalid', ...)` | `router.on('httpException', ...)` |
| `router.on('exception', ...)` | `router.on('networkError', ...)` |
| `router.cancel()` | `router.cancelAll()` |
| `Inertia::lazy(...)` | `Inertia::optional(...)` |

## 9.11 Récapitulatif

| Fonctionnalité | Utilité |
|---|---|
| `usePoll()` | Rafraîchir périodiquement |
| `prefetch` sur `<Link>` | Charger en avance, navigation instantanée |
| Instant visits | Back/forward instantané (auto) |
| `Inertia::merge()` | Scroll infini |
| `optimistic` dans `router.post()` | UI réactive avant la réponse serveur |
| `useHttp()` | Requêtes XHR custom partageant la conf Inertia |
| `Inertia::once()` | Prop figée pour toute la session |

## Étape suivante

➡️ [10-ssr.md](10-ssr.md) — Server-Side Rendering avec Inertia.
