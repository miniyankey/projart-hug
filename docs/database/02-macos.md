# Installation sur macOS

Ce guide utilise **Homebrew**. Si tu ne l'as pas, installe-le d'abord :

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

## Étape 1 — Installer MariaDB

Ouvre **Terminal** et tape :

```bash
brew install mariadb
```

Attends que ça finisse (1-2 minutes).

## Étape 2 — Démarrer le service

```bash
brew services start mariadb
```

Tu devrais voir un message du genre `Successfully started 'mariadb'`.

Pour vérifier qu'il tourne bien :

```bash
brew services list
```

La ligne `mariadb` doit afficher le statut **started**.

## Étape 3 — Vérifier la connexion

```bash
mariadb -u root
```

Tu devrais arriver dans un prompt `MariaDB [(none)]>`. Tape `EXIT;` pour sortir.

> Par défaut sur macOS via Homebrew, le user `root` n'a **pas** de mot de passe (auth par socket Unix). C'est normal pour du dev local.

## Étape 4 — Vérifier que PHP a l'extension MySQL

```bash
php -m | grep mysql
```

Tu dois voir `pdo_mysql` et `mysqli` apparaître.

### Si tu ne vois rien

Cela veut dire que ton PHP n'a pas été compilé avec le support MySQL. Réinstalle PHP via Homebrew :

```bash
brew install php
```

Homebrew installe PHP avec `pdo_mysql` activé par défaut. Refais ensuite :

```bash
php -m | grep mysql
```

> Si tu utilises le PHP livré par macOS (`/usr/bin/php`), je recommande de passer au PHP de Homebrew — il est plus récent et inclut `pdo_mysql` par défaut. Vérifie quel PHP est utilisé avec `which php` : ça doit pointer vers `/opt/homebrew/bin/php` (Mac M1/M2/M3) ou `/usr/local/bin/php` (Mac Intel).

## Étape 5 — Continuer

Passe au fichier **[04-configurer-projet.md](04-configurer-projet.md)** pour créer la DB et brancher le projet.
