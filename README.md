# Projet d'intégration - Groupe 7 : Needle

Application web bâtie sur **Laravel 13 + Inertia v3 + Vue 3 + MariaDB**, conteneurisée avec **Docker** (images Laravel Sail, pilotées directement via `docker compose`).

> Les commandes de ce README utilisent **`docker compose`** plutôt que le script `./vendor/bin/sail`.
> C'est volontaire : `sail` est un script bash qui ne tourne pas dans CMD/PowerShell et peut poser problème selon l'OS. `docker compose` fonctionne de façon identique partout (Linux, macOS, Windows).

## Sommaire

- [Prérequis](#prérequis)
- [Installation from scratch](#installation-from-scratch)
- [Installation sans Docker](#installation-sans-docker)
- [Workflow de développement](#workflow-de-développement)
- [Commandes utiles](#commandes-utiles)
- [Documentation détaillée](#documentation-détaillée)

---

## Prérequis

Sur une machine neuve, il suffit de :

- **Docker** + **Docker Compose** (Docker Desktop sur Windows/macOS, `docker` + plugin `compose` sur Linux)
- **Git**

Tout le reste (PHP 8.5, Composer, Node.js, MariaDB) tourne **dans les containers** : rien à installer en local.

> **Windows** : utilise Docker Desktop avec le **backend WSL2** (réglage par défaut récent). Les commandes ci-dessous sont à lancer dans **PowerShell**. Pour de meilleures performances, tu peux cloner le projet dans le filesystem WSL plutôt que sur `C:\`, mais ce n'est pas obligatoire.

Vérifie que Docker tourne :

```bash
docker --version
docker compose version
```

Détails par OS : [`docs/docker/01-prerequis.md`](docs/docker/01-prerequis.md)

---

## Installation from scratch

Procédure complète pour partir d'une machine vierge jusqu'au site qui s'affiche.

### 1. Cloner le dépôt

```bash
git clone <url-du-repo> projart-hug
cd projart-hug
```

### 2. Créer le fichier `.env`

**Linux / macOS :**

```bash
cp .env.example .env
```

**Windows (PowerShell) :**

```powershell
Copy-Item .env.example .env
```

Sur **Linux**, aligne `WWWUSER` / `WWWGROUP` sur ton utilisateur pour éviter les soucis de permissions sur les fichiers générés par le container :

```bash
sed -i "s/^WWWUSER=.*/WWWUSER=$(id -u)/" .env
sed -i "s/^WWWGROUP=.*/WWWGROUP=$(id -g)/" .env
```

Sur **macOS** / **Windows (Docker Desktop)** : laisse `1000` / `1000`, Docker Desktop gère la traduction.

### 3. Bootstrap des dépendances PHP (Composer)

Le `compose.yaml` construit l'image à partir de `vendor/laravel/sail/`. Ce dossier n'existe pas tant que Composer n'a pas tourné : c'est le problème de l'oeuf et la poule. Comme PHP/Composer ne sont pas installés en local, on lance Composer **dans un container jetable** via l'image officielle `composer` :

**Linux / macOS :**

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/app" \
  -w /app \
  composer:latest install --ignore-platform-reqs
```

**Windows (PowerShell) :**

```powershell
docker run --rm -v "${PWD}:/app" -w /app composer:latest install --ignore-platform-reqs
```

> `--ignore-platform-reqs` est nécessaire car l'image `composer` n'a pas forcément exactement PHP 8.5 ni toutes les extensions : on installe juste les packages, l'exécution se fera ensuite dans le bon container.

### 4. Construire et démarrer les containers

```bash
docker compose up -d --build
```

Cela lance :

- `laravel.test` -> application Laravel sur **http://localhost:8000**
- `mariadb` -> base de données, exposée sur le port hôte **3307** (-> **3306** dans le container)

> Le premier build télécharge et compile l'image PHP 8.5 : compte quelques minutes. Les `up` suivants sont quasi instantanés.

Vérifie que les deux services tournent :

```bash
docker compose ps
```

### 5. Générer la clé d'application

```bash
docker compose exec laravel.test php artisan key:generate
```

### 6. Migrer et peupler la base

```bash
docker compose exec laravel.test php artisan migrate --seed
```

Cela crée le schéma et insère les données de démo (dont un compte admin `admin@hug.ch` / `password`).

### 7. Installer les dépendances frontend

```bash
docker compose exec laravel.test npm install
```

### 8. Builder les assets (ou lancer le HMR)

Pour un build de production unique :

```bash
docker compose exec laravel.test npm run build
```

Pour le développement avec rechargement à chaud (recommandé au quotidien) :

```bash
docker compose exec laravel.test npm run dev
```

### 9. Vérifier

Ouvre **http://localhost:8000** : la page d'accueil Inertia doit s'afficher.

---

## Installation sans Docker

Si tu préfères tout faire tourner **directement sur ta machine** (sans containers), voici la procédure. C'est plus léger mais tu dois gérer toi-même PHP, Node et la base de données.

> Recommandation : sur ce projet, **Docker reste la voie de référence** (environnement identique pour toute l'équipe, MariaDB préconfigurée). L'install locale est utile si Docker ne passe pas sur ton poste.

### Prérequis (à installer en local)

- **PHP 8.3+** (8.5 idéalement, pour coller à l'environnement Docker) avec les extensions usuelles Laravel : `mbstring`, `xml`, `ctype`, `curl`, `pdo`, `tokenizer`, `bcmath`, `fileinfo`, et selon la base choisie `pdo_mysql` (MariaDB) ou `pdo_sqlite`.
- **Composer 2**
- **Node.js 20+** + npm
- **(Option A)** un serveur **MariaDB 11** ou **MySQL 8** local, **OU (Option B)** rien de plus si tu choisis **SQLite**.

### 1. Cloner et préparer `.env`

```bash
git clone <url-du-repo> projart-hug
cd projart-hug
cp .env.example .env        # Windows PowerShell : Copy-Item .env.example .env
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configurer la base de données

Choisis **une** des deux options et édite ton `.env` en conséquence.

**Option A - MariaDB / MySQL local** (fidèle au projet)

Crée d'abord la base et l'utilisateur dans ton serveur, puis dans `.env` :

```dotenv
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projart_hug
DB_USERNAME=projart
DB_PASSWORD=password
```

> Attention : `DB_HOST=mariadb` et le port `3307` du `.env.example` ne valent **que** pour Docker. En local, c'est `127.0.0.1` et le port réel de ton serveur (3306 par défaut).

**Option B - SQLite** (le plus rapide à mettre en route, zéro serveur)

```bash
touch database/database.sqlite     # Windows PowerShell : New-Item database/database.sqlite
```

Puis dans `.env`, mets `DB_CONNECTION=sqlite` et **supprime / commente** les lignes `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

### 4. Générer la clé, migrer et peupler

```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Lancer l'application

Deux processus à garder ouverts (deux terminaux) :

```bash
php artisan serve     # backend sur http://localhost:8000
```

```bash
npm run dev           # serveur Vite + HMR
```

Ouvre **http://localhost:8000**.

> Le helper `composer run dev` lance backend + queue + Vite en une commande. Il est pratique **hors Docker** (c'est uniquement avec Sail qu'il fait doublon).

---

## Workflow de développement

Une fois le projet installé, le quotidien :

### Démarrer

```bash
docker compose up -d                          # containers en arrière-plan
docker compose exec laravel.test npm run dev  # serveur Vite + HMR
```

Garde **http://localhost:8000** ouvert : toute modification dans `resources/js/**` (Vue, TS) ou `resources/css/**` (Tailwind) se recharge automatiquement.

> N'utilise **pas** `composer run dev` : il lance `php artisan serve` qui ferait doublon avec le serveur du container.

### Arrêter

```bash
docker compose down
```

Pour aussi supprimer les volumes (efface la base de données) :

```bash
docker compose down -v
```

---

## Commandes utiles

Toutes les commandes s'exécutent **dans** le container `laravel.test`. Le préfixe est toujours `docker compose exec laravel.test ...`.

| Action | Commande |
| --- | --- |
| Ouvrir un shell dans le container | `docker compose exec laravel.test bash` |
| Artisan (ex. liste des routes) | `docker compose exec laravel.test php artisan route:list` |
| Recréer la base à neuf + seed | `docker compose exec laravel.test php artisan migrate:fresh --seed` |
| Tinker | `docker compose exec laravel.test php artisan tinker` |
| Composer | `docker compose exec laravel.test composer <cmd>` |
| npm | `docker compose exec laravel.test npm <cmd>` |
| Formatter PHP (Pint) | `docker compose exec laravel.test vendor/bin/pint` |
| Lint + format JS/Vue | `docker compose exec laravel.test npm run fix` |
| Voir les logs des containers | `docker compose logs -f` |

> **Astuce alias.** Pour éviter de retaper le préfixe :
>
> **Linux / macOS** (`~/.bashrc` ou `~/.zshrc`) :
> ```bash
> alias dc='docker compose exec laravel.test'
> ```
> Puis : `dc php artisan migrate`, `dc npm run dev`, etc.
>
> **Windows** (`$PROFILE` PowerShell) :
> ```powershell
> function dc { docker compose exec laravel.test $args }
> ```

---

## Documentation détaillée

- [`docs/docker/`](docs/docker/) - prérequis, démarrage, commandes, dépannage Docker
- [`docs/database/`](docs/database/) - schéma, migrations, seeders
- [`docs/inertia/`](docs/inertia/) - patterns Inertia / Vue
- `context/` - brief du mandant, UML, sitemap, maquettes
</content>
</invoke>
