# Documentation Inertia.js

Cette documentation explique en détail comment fonctionne **Inertia.js v3** dans le contexte de ce projet (Laravel 13 + Vue 3).

Elle est rédigée pour quelqu'un qui connaît déjà les bases de Laravel et de Vue mais qui découvre Inertia.

## Sommaire

| # | Fichier | Contenu |
|---|---------|---------|
| 1 | [01-introduction.md](01-introduction.md) | Qu'est-ce qu'Inertia ? Pourquoi l'utiliser ? |
| 2 | [02-architecture.md](02-architecture.md) | Architecture globale et cycle d'une requête (avec diagrammes) |
| 3 | [03-installation-configuration.md](03-installation-configuration.md) | Comment Inertia est installé et configuré dans ce projet |
| 4 | [04-cote-serveur.md](04-cote-serveur.md) | Routes, contrôleurs, `Inertia::render`, middleware |
| 5 | [05-cote-client.md](05-cote-client.md) | Pages Vue, point d'entrée `app.js`, layouts |
| 6 | [06-navigation.md](06-navigation.md) | Composant `<Link>`, `router`, navigation programmatique |
| 7 | [07-formulaires.md](07-formulaires.md) | `useForm`, `<Form>`, validation et erreurs |
| 8 | [08-props-partagees.md](08-props-partagees.md) | Données partagées, flash messages, `usePage()` |
| 9 | [09-fonctionnalites-avancees.md](09-fonctionnalites-avancees.md) | Deferred props, polling, prefetching, optimistic updates |
| 10 | [10-ssr.md](10-ssr.md) | Server-Side Rendering avec `@inertiajs/vite` |
| 11 | [11-faq.md](11-faq.md) | Questions fréquentes et pièges courants |

## Stack du projet

- **Backend** : Laravel 13 + PHP 8.5
- **Frontend** : Vue 3 (`<script setup>`) + Tailwind CSS 4
- **Pont Inertia** : `inertiajs/inertia-laravel` v3 / `@inertiajs/vue3` v3
- **Build** : Vite + `@inertiajs/vite` + Wayfinder

## Comment lire cette documentation

1. Si vous débutez avec Inertia, lisez les fichiers **dans l'ordre** (1 → 11).
2. Si vous cherchez une fonctionnalité précise, utilisez le sommaire ci-dessus.
3. Chaque chapitre contient des exemples tirés du projet réel quand c'est possible.

## Ressources externes

- Documentation officielle : <https://inertiajs.com>
- Dépôt GitHub Inertia : <https://github.com/inertiajs/inertia>
- Documentation Laravel 13 : <https://laravel.com/docs/13.x>
