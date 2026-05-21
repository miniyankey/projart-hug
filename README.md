# Projet d'intégration — Groupe 7 : Needle

Application web bâtie sur **Laravel 13 + Inertia v3 + Vue 3 + MariaDB**, conteneurisée avec **Laravel Sail (Docker)**.

## Sommaire

- [Prérequis](#prérequis)
- [Installation from scratch](#installation-from-scratch)
- [Workflow de développement](#workflow-de-développement)
- [Commandes utiles](#commandes-utiles)
- [Documentation détaillée](#documentation-détaillée)

---

## Prérequis

- **Docker** + **Docker Compose** (ou Docker Desktop sur Windows/macOS)
- **Git**
- (Optionnel hors Docker) PHP 8.5, Composer 2, Node.js 20+

Détails par OS : [`docs/docker/01-prerequis.md`](docs/docker/01-prerequis.md)

---

## Installation from scratch

### 1. Cloner le dépôt

```bash
git clone <url-du-repo> projart-hug
cd projart-hug
```

### 2. Configurer le fichier `.env`

```bash
cp .env.example .env
```

Sur **Linux**, ajuste `WWWUSER` et `WWWGROUP` pour qu'ils correspondent à ton utilisateur (évite les soucis de permissions sur les fichiers générés par le container) :

```bash
sed -i "s/^WWWUSER=.*/WWWUSER=$(id -u)/" .env
sed -i "s/^WWWGROUP=.*/WWWGROUP=$(id -g)/" .env
```

Sur **macOS** / **Windows (Docker Desktop)** : laisse `1000` / `1000`.

### 3. Installer les dépendances PHP

Avant le premier `up`, il faut Composer pour installer Sail. Deux options :

**Option A — Composer installé en local :**
```bash
composer install --ignore-platform-reqs
```

### 3. Démarrer les containers

```bash
./vendor/bin/sail up -d
```

> 💡 Crée un alias pour simplifier : `alias sail='./vendor/bin/sail'`

Cela lance :
- `laravel.test` → Laravel sur **http://localhost:8000**
- `mariadb` → base de données sur le port **3307** (hôte) → **3306** (container)

### 5. Générer la clé d'application

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Lancer les migrations

```bash
./vendor/bin/sail artisan migrate
```

### 7. Installer les dépendances frontend

```bash
./vendor/bin/sail npm install
```

### 8. Builder les assets (ou lancer le HMR, voir plus bas)

```bash
./vendor/bin/sail npm run build
```

### 9. Vérifier

Ouvre **http://localhost:8000** dans ton navigateur — la page d'accueil Inertia doit s'afficher.

---

## Workflow de développement

Une fois le projet installé, le workflow quotidien :

### Démarrer

```bash
./vendor/bin/sail up -d           # containers en arrière-plan
./vendor/bin/sail npm run dev     # serveur Vite + HMR
```

Garde **http://localhost:8000** ouvert dans le navigateur — toute modification dans `resources/js/**` (Vue, TS) ou `resources/css/**` (Tailwind) se recharge automatiquement.

> ⚠️ N'utilise **pas** `composer run dev` avec Sail : il lance `php artisan serve` qui ferait doublon avec le serveur du container.

### Arrêter

```bash
./vendor/bin/sail down
```

Pour aussi supprimer les volumes (⚠️ efface la base de données) :
```bash
./vendor/bin/sail down -v
```
