# Configurer la base de données (MariaDB)

Ce guide explique comment installer **MariaDB** sur ta machine et brancher le projet dessus.

À la fin, tu pourras lancer `php artisan migrate` et voir les tables apparaître dans ta DB locale.

## Sommaire

| # | Fichier | Contenu |
|---|---------|---------|
| 1 | [01-windows.md](01-windows.md) | Installation sur Windows |
| 2 | [02-macos.md](02-macos.md) | Installation sur macOS |
| 3 | [03-linux.md](03-linux.md) | Installation sur Linux (Fedora / Ubuntu / Debian) |
| 4 | [04-configurer-projet.md](04-configurer-projet.md) | Créer la DB et brancher Laravel (commun aux 3 OS) |
| 5 | [05-depannage.md](05-depannage.md) | Erreurs fréquentes |

## Ordre à suivre

1. Choisis le fichier qui correspond à ton OS (1, 2 ou 3) et fais **toutes** les étapes.
2. Continue avec le fichier 4 — **obligatoire pour tout le monde**.
3. Réfère-toi au fichier 5 seulement si tu rencontres un problème.

## Convention utilisée dans ce projet

| Paramètre | Valeur |
|-----------|--------|
| Nom de la base de données | `projart_hug` |
| Utilisateur | `projart` |
| Mot de passe | À ton choix (en dev local, n'importe quoi fait l'affaire) |
| Host | `127.0.0.1` |
| Port | `3306` (par défaut) |

Tout le monde dans l'équipe utilise les mêmes noms pour que le `.env.example` fonctionne sans modification.
