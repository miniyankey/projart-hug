# 1. Introduction à Inertia.js

## 1.1 Qu'est-ce qu'Inertia ?

**Inertia.js** est une bibliothèque qui permet de construire des applications **monopages (SPA)** sans avoir à créer une API séparée.

Concrètement, Inertia est un **pont** entre :

- un framework backend classique (ici **Laravel**) qui gère le routage, l'authentification, la base de données, etc.
- un framework frontend moderne (ici **Vue 3**) qui gère le rendu côté client.

> Inertia n'est ni un framework backend, ni un framework frontend. C'est de la **colle** entre les deux.

## 1.2 Le problème qu'Inertia résout

Quand on construit une application web moderne, on a généralement deux choix :

### Option A — Application multipage classique (MPA)
```
Navigateur ──GET /users──► Laravel ──► Blade ──► HTML complet
```
✅ Simple, rapide à développer
❌ Rechargement complet de la page, expérience utilisateur moins fluide

### Option B — SPA avec API séparée
```
Navigateur ──► Vue (SPA)
              │
              └──GET /api/users──► Laravel ──► JSON ──► Vue rend la page
```
✅ Expérience fluide type "app"
❌ Il faut maintenir deux applications (Laravel pour l'API, Vue pour le client), gérer le CORS, l'authentification par token, etc.

### Option C — Inertia
```
Navigateur ──► Vue (SPA)
              │
              └──GET /users──► Laravel ──► JSON (props) ──► Vue rend la page
```
✅ Expérience SPA fluide
✅ On garde les **contrôleurs**, les **routes**, l'**authentification**, les **policies** de Laravel
✅ Pas d'API à maintenir
✅ Pas de gestion de CORS, ni de tokens

## 1.3 L'idée centrale

Au lieu de renvoyer du HTML (comme Blade) ou du JSON pur (comme une API REST), un contrôleur Laravel sous Inertia renvoie :

> *« Affiche le composant Vue **`Users/Index`** avec ces **props** : `{ users: [...] }` »*

Inertia s'occupe de :
1. Charger la bonne page Vue
2. Lui injecter les props
3. Mettre à jour l'URL dans le navigateur
4. Gérer l'historique (boutons précédent/suivant)

## 1.4 Exemple minimal (issu du projet)

**Côté Laravel** (`routes/web.php`) :
```php
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
```

**Côté Vue** (`resources/js/pages/Welcome.vue`) :
```vue
<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />

    <div class="flex min-h-screen items-center justify-center">
        <h1 class="text-2xl font-medium">Hello world</h1>
    </div>
</template>
```

C'est tout. Quand l'utilisateur visite `/`, Laravel demande à Inertia de rendre le composant Vue `Welcome.vue`.

## 1.5 Ce qu'Inertia n'est pas

Il est important de comprendre ce qu'Inertia **ne fait pas** :

| Inertia n'est pas... | Pourquoi |
|---|---|
| Un framework frontend | C'est Vue (ou React, Svelte) qui fait le rendu |
| Un framework backend | C'est Laravel qui gère routes, BD, auth |
| Un remplaçant d'API | Si une app mobile doit consommer vos données, vous aurez quand même besoin d'une API |
| Un système de templating | Les pages sont des composants Vue, pas du Blade |

## 1.6 Avantages dans ce projet

- **Routes nommées Laravel** : on continue à utiliser `Route::get(...)->name(...)` comme d'habitude.
- **Authentification, sessions, CSRF** : géré par Laravel comme dans une app Blade classique.
- **Validation** : `FormRequest` Laravel, les erreurs sont automatiquement renvoyées au formulaire Vue.
- **Pas de token JWT, pas de CORS** : tout passe par les cookies de session habituels.
- **Wayfinder** : on génère des fonctions TypeScript/JS typées pour appeler nos contrôleurs depuis Vue.

## Étape suivante

➡️ [02-architecture.md](02-architecture.md) — Comprendre le cycle d'une requête Inertia avec des diagrammes.
