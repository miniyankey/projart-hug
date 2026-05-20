# 5. Côté client (Vue)

Côté Vue, on écrit des **pages** (composants Vue standards) que Laravel demande de rendre via `Inertia::render('NomDeLaPage')`.

## 5.1 Anatomie d'une page Inertia

Une **page** est juste un fichier `.vue` situé dans `resources/js/pages/`. Pas de magie particulière : c'est un composant Vue normal.

```vue
<!-- resources/js/pages/Users/Show.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    user: Object,
});
</script>

<template>
    <Head title="Profil utilisateur" />

    <div class="p-6">
        <h1 class="text-2xl font-bold">{{ user.name }}</h1>
        <p class="text-gray-600">{{ user.email }}</p>
    </div>
</template>
```

Quand le contrôleur Laravel appelle :

```php
return Inertia::render('Users/Show', ['user' => $user]);
```

…Inertia :
1. Charge `resources/js/pages/Users/Show.vue`
2. Le monte avec `props = { user: {...} }`
3. Affiche la page

## 5.2 Les props

Les props envoyées par Laravel se reçoivent comme des **props Vue standards** :

```vue
<script setup>
defineProps({
    user: Object,
    posts: Array,
    canEdit: Boolean,
});
</script>
```

> 💡 Dans ce projet (JavaScript, pas TypeScript), on utilise `defineProps({ user: Object })`. Si vous étiez en TypeScript, ce serait `defineProps<{ user: User }>()`.

### Accéder aux props partagées (shared data)

Les props définies dans `HandleInertiaRequests::share()` (chapitre 8) ne sont **pas** passées via `defineProps`. On y accède via `usePage()` :

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const appName = computed(() => page.props.name); // défini dans share()
</script>
```

## 5.3 Le composant `<Head>`

Permet de manipuler le `<head>` du document (titre, méta, etc.) depuis une page :

```vue
<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head>
        <title>Tableau de bord</title>
        <meta name="description" content="Vue d'ensemble de votre activité">
        <link rel="canonical" href="https://app.test/dashboard">
    </Head>

    <!-- contenu -->
</template>
```

Forme courte pour juste le titre :

```vue
<Head title="Tableau de bord" />
```

Le suffixe `- Laravel` est automatiquement ajouté grâce à `app.js` :

```js
createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
});
```

## 5.4 Convention de nommage des pages

| Nom passé à `Inertia::render` | Fichier attendu |
|---|---|
| `'Welcome'` | `resources/js/pages/Welcome.vue` |
| `'Dashboard'` | `resources/js/pages/Dashboard.vue` |
| `'Users/Index'` | `resources/js/pages/Users/Index.vue` |
| `'Settings/Profile'` | `resources/js/pages/Settings/Profile.vue` |

> Utilisez le **PascalCase** pour les noms de fichiers (`Users/Show.vue`, pas `users/show.vue`).

## 5.5 Layouts persistants

Par défaut, chaque navigation **démonte** la page précédente et **monte** la nouvelle. Cela signifie que si vous avez un header ou une sidebar, **ils sont remontés à chaque visite** (perte d'état local, des animations qui reset, etc.).

Pour éviter ça, on utilise des **layouts persistants**.

### Approche 1 : Layout par page

```vue
<!-- resources/js/layouts/AppLayout.vue -->
<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen">
        <header class="bg-gray-100 p-4">
            <Link href="/" class="font-bold">Mon App</Link>
        </header>

        <main class="p-6">
            <slot />
        </main>
    </div>
</template>
```

```vue
<!-- resources/js/pages/Dashboard.vue -->
<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout>
        <Head title="Dashboard" />
        <h1>Tableau de bord</h1>
    </AppLayout>
</template>
```

✅ Simple, explicite
❌ Le layout est remonté à chaque navigation (mêmes inconvénients qu'avant)

### Approche 2 : Layout persistant (`defineOptions`)

```vue
<!-- resources/js/pages/Dashboard.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<script>
import AppLayout from '@/layouts/AppLayout.vue';
export default { layout: AppLayout };
</script>

<template>
    <Head title="Dashboard" />
    <h1>Tableau de bord</h1>
</template>
```

✅ Le layout n'est **pas remonté** quand on navigue entre deux pages qui partagent le même layout.

### Approche 3 : Layout global par défaut

Dans `resources/js/app.js`, vous pouvez attacher un layout par défaut à toutes les pages qui n'en spécifient pas :

```js
import { createInertiaApp } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
        const page = pages[`./pages/${name}.vue`];
        page.default.layout = page.default.layout || AppLayout;
        return page;
    },
    // ...
});
```

> Dans ce projet, le plugin `@inertiajs/vite` gère la résolution. Pour personnaliser, il faudra étendre la config.

## 5.6 Props de layout (`useLayoutProps`) — v3

Inertia v3 permet à un layout de **déclarer ses propres props** indépendamment de la page :

```vue
<!-- resources/js/layouts/AppLayout.vue -->
<script setup>
import { useLayoutProps } from '@inertiajs/vue3';

const props = useLayoutProps({
    notifications: Array,
    unreadCount: Number,
});
</script>

<template>
    <header>
        Notifications : {{ props.unreadCount }}
    </header>
    <slot />
</template>
```

Côté Laravel, on définit ces props avec `setLayoutProps()` :

```php
Inertia::setLayoutProps([
    'notifications' => Notification::query()->latest()->limit(5)->get(),
    'unreadCount' => Notification::unread()->count(),
]);

return Inertia::render('Dashboard', [/* props de la page */]);
```

Avantage : ces props ne sont chargées qu'au premier rendu du layout, pas à chaque navigation.

## 5.7 Le composant `<Deferred>`

Pour afficher un placeholder pendant qu'une prop différée se charge (voir chapitre 9) :

```vue
<script setup>
import { Deferred } from '@inertiajs/vue3';

defineProps({ stats: Object });
</script>

<template>
    <Deferred data="stats">
        <template #fallback>
            <div class="animate-pulse h-32 bg-gray-200 rounded"></div>
        </template>

        <div>
            Utilisateurs : {{ stats.users }}
            Commandes : {{ stats.orders }}
        </div>
    </Deferred>
</template>
```

## 5.8 Cycle de vie d'une page

```mermaid
flowchart TD
    Visit["L'utilisateur visite ou clique"] --> Resolve["Inertia résout le composant<br/>(import dynamique du .vue)"]
    Resolve --> Mount["Vue monte le composant<br/>avec les props"]
    Mount --> Lifecycle["onMounted, watchers, etc.<br/>fonctionnent normalement"]
    Lifecycle --> Navigate{Navigation ?}
    Navigate -->|Vers une autre page| Unmount["onBeforeUnmount → onUnmounted"]
    Navigate -->|Vers la même page<br/>(rechargement partiel)| Update["Props mises à jour<br/>watchers déclenchés"]
    Unmount --> Visit
    Update --> Lifecycle
```

Tout le cycle de vie Vue (`onMounted`, `onUnmounted`, `watch`, `computed`, etc.) **fonctionne normalement** à l'intérieur d'une page Inertia.

## 5.9 Récapitulatif

| Concept | Comment l'utiliser |
|---|---|
| Page | Fichier `.vue` dans `resources/js/pages/` |
| Props | `defineProps({ ... })` reçoit ce que Laravel a envoyé |
| Titre du document | `<Head title="..." />` |
| Données partagées | `usePage().props` |
| Layout | `export default { layout: AppLayout }` dans un bloc `<script>` séparé |
| Props de layout | `useLayoutProps()` + `Inertia::setLayoutProps()` côté Laravel |

## Étape suivante

➡️ [06-navigation.md](06-navigation.md) — Comment naviguer entre les pages.
