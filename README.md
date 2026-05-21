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

**Option B — Sans Composer local, via un container jetable :**
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Démarrer les containers

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

### 8. Builder les assets (ou lancer le HMR — voir plus bas)

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

---

## Commandes utiles

| Action | Commande |
| --- | --- |
| Voir les containers | `./vendor/bin/sail ps` |
| Logs Laravel | `./vendor/bin/sail logs -f laravel.test` |
| Shell dans le container | `./vendor/bin/sail shell` |
| Tinker | `./vendor/bin/sail artisan tinker` |
| Tests Pest | `./vendor/bin/sail artisan test --compact` |
| Lint PHP (Pint) | `./vendor/bin/sail composer exec -- pint --dirty` |
| Lint JS/Vue | `./vendor/bin/sail npm run lint` |
| Migrations fraîches | `./vendor/bin/sail artisan migrate:fresh --seed` |
| Connexion MariaDB (hôte) | `mysql -h 127.0.0.1 -P 3307 -u projart -p` |

Plus de commandes : [`docs/docker/03-commandes-courantes.md`](docs/docker/03-commandes-courantes.md)

---

## Documentation détaillée

- **Docker / Sail** → [`docs/docker/`](docs/docker/)
  - Prérequis, démarrage, commandes courantes, dépannage
- **Base de données (MariaDB)** → [`docs/database/`](docs/database/)
  - Installation par OS, configuration projet, dépannage
- **Inertia** → [`docs/inertia/`](docs/inertia/)
  - Architecture, côté serveur/client, navigation, formulaires, SSR, FAQ

---

## Dépannage rapide

| Symptôme | Solution |
| --- | --- |
| `Unable to locate file in Vite manifest` | `./vendor/bin/sail npm run build` ou lance `npm run dev` |
| Permissions refusées sur `storage/` ou `bootstrap/cache/` | Vérifie `WWWUSER`/`WWWGROUP` dans `.env`, puis `./vendor/bin/sail down && up -d` |
| Port 8000 ou 3307 déjà utilisé | Change `APP_PORT` / `FORWARD_DB_PORT` dans `.env` |
| Migrations échouent | Vérifie que `mariadb` est `healthy` : `./vendor/bin/sail ps` |

Plus de cas : [`docs/docker/04-depannage.md`](docs/docker/04-depannage.md) et [`docs/database/05-depannage.md`](docs/database/05-depannage.md)
