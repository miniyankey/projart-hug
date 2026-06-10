# Projet d'intégration - Groupe 7 : Needle

**Mission Donneur** - site web pour dynamiser les collectes de sang en entreprise (HUG / Centre de transfusion sanguine).

Stack : **Laravel 13** (PHP) · **Inertia v3 + Vue 3** · **Tailwind v4** · **MariaDB**.

## Sommaire

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancer le projet](#lancer-le-projet)
- [Compte de démo](#compte-de-démo)
- [Commandes utiles](#commandes-utiles)

---

## Prérequis

À installer sur ta machine :

- **PHP 8.3+** avec les extensions usuelles Laravel (`mbstring`, `xml`, `ctype`, `curl`, `tokenizer`, `bcmath`, `fileinfo`) et `pdo_mysql`
- **Composer 2**
- **Node.js 20+** (22 recommandé) et **npm**
- **MariaDB 11**

Vérifie tes versions :

```bash
php -v
composer -V
node -v
```

---

## Installation

### 1. Cloner le dépôt

```bash
git clone <url-du-repo> projart-hug
cd projart-hug
```

### 2. Créer le fichier `.env`

```bash
cp .env.example .env        # Windows PowerShell : Copy-Item .env.example .env
```

### 3. Installer les dépendances

```bash
composer install
npm install
```

### 4. Configurer la base de données

Crée une base et un utilisateur dans ton serveur MariaDB :

```sql
CREATE DATABASE projart_hug;
CREATE USER 'projart'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON projart_hug.* TO 'projart'@'localhost';
```

Puis dans `.env` :

```dotenv
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projart_hug
DB_USERNAME=projart
DB_PASSWORD=password
```

> ⚠️ La valeur `DB_HOST=mariadb` du `.env.example` ne fonctionne pas en local : remplace-la bien par `127.0.0.1`.

### 5. Configurer les e-mails en local

L'application envoie des e-mails (notification de collecte, rappel d'éligibilité). En local, sans identifiants SMTP, écris-les simplement dans les logs :

```dotenv
MAIL_MAILER=log
```

Les e-mails apparaîtront dans `storage/logs/laravel.log` au lieu d'être envoyés.

### 6. Finaliser

```bash
php artisan key:generate        # clé d'application
php artisan migrate --seed      # schéma + données de démo
php artisan storage:link        # rend les logos uploadés accessibles publiquement
```

---

## Lancer le projet

Une seule commande lance tout (serveur PHP + worker de queue + Vite) :

```bash
composer run dev
```

Puis ouvre **http://localhost:8000**.

> Le worker de queue est nécessaire : les e-mails sont mis en file d'attente (`QUEUE_CONNECTION=database`) et ne partent que si un worker tourne. `composer run dev` s'en occupe.

Alternative manuelle, si tu préfères des terminaux séparés :

```bash
php artisan serve               # backend -> http://localhost:8000
npm run dev                     # Vite + rechargement à chaud
php artisan queue:listen        # worker d'e-mails (optionnel si tu ne testes pas les mails)
```

Toute modification dans `resources/js/**` (Vue) ou `resources/css/**` (Tailwind) se recharge automatiquement dans le navigateur.

---

## Compte de démo

Le seeder crée un compte administrateur :

| Champ | Valeur |
| --- | --- |
| URL | http://localhost:8000/admin/login |
| E-mail | `admin@hug.ch` |
| Mot de passe | `password` |

Le seeder crée aussi des entreprises, collectes et KPIs de démonstration : les liens co-brandés sont visibles dans l'admin (section Collectes).

---

## Commandes utiles

| Action | Commande |
| --- | --- |
| Recréer la base à neuf + re-seed | `php artisan migrate:fresh --seed` |
| Lister les routes | `php artisan route:list` |
| Console interactive | `php artisan tinker` |
| Formater le PHP (Pint) | `vendor/bin/pint` |
| Lint + format JS/Vue | `npm run fix` |
| Build de production des assets | `npm run build` |
