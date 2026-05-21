# Configurer le projet avec Docker (Laravel Sail)

Ce guide est la **méthode recommandée** pour installer et lancer le projet, peu importe ton OS.

Avec Docker, tu n'as **pas besoin d'installer** PHP, Composer, Node.js, npm ou MariaDB sur ta machine. Un seul prérequis : Docker Desktop.

## Sommaire

| # | Fichier | Contenu |
|---|---------|---------|
| 1 | [01-prerequis.md](01-prerequis.md) | Installer Docker selon ton OS (Windows / macOS / Linux) |
| 2 | [02-demarrage.md](02-demarrage.md) | Cloner, configurer et lancer le projet |
| 3 | [03-commandes-courantes.md](03-commandes-courantes.md) | Cheat sheet des commandes Sail |
| 4 | [04-depannage.md](04-depannage.md) | Erreurs fréquentes |

## Ordre à suivre

1. Faire [01-prerequis.md](01-prerequis.md) pour ton OS
2. Suivre [02-demarrage.md](02-demarrage.md) pour démarrer le projet
3. Garder [03-commandes-courantes.md](03-commandes-courantes.md) sous la main pour le quotidien

## Pourquoi Docker plutôt qu'une installation native ?

| Sans Docker | Avec Docker |
|---|---|
| Installer PHP 8.5 + extensions | ❌ inutile |
| Installer Composer | ❌ inutile |
| Installer Node.js + npm | ❌ inutile |
| Installer MariaDB + créer la DB | ❌ inutile |
| Procédures différentes par OS | ✅ même procédure partout |
| Risque de "ça marche chez moi seulement" | ✅ environnement identique pour toute l'équipe |

> 💡 Si tu veux quand même installer sans Docker, voir [docs/database](../database/README.md). Mais Docker est plus simple.
