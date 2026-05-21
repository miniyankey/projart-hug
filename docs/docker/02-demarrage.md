# 2. Démarrer le projet

À partir d'ici on suppose que Docker est installé et fonctionne. Sinon voir [01-prerequis.md](01-prerequis.md).

## 1. Cloner le projet

```bash
git clone <url-du-repo>
cd projart-hug
```

> 💡 Sur **Windows**, clone dans WSL2 (`~`) et non dans `C:\Users\...`.

## 2. Copier le fichier d'environnement

```bash
cp .env.example .env
```

### Adapter `WWWUSER` / `WWWGROUP` (Linux uniquement)

Récupère ton UID/GID :
```bash
id -u   # WWWUSER
id -g   # WWWGROUP
```

Si les valeurs ne sont pas `1000`, mets-les à jour dans `.env`. Sur macOS/Windows, laisse `1000` (Docker Desktop gère la traduction).

## 3. Installer les dépendances PHP (Composer)

Une seule fois, en utilisant un container Composer temporaire (parce que PHP n'est pas encore dispo dans Sail tant que le vendor n'existe pas) :

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs
```

> Si tu as déjà fait `composer install` en local, cette étape n'est pas nécessaire.

## 4. Démarrer les containers

```bash
./vendor/bin/sail up -d
```

⏱️ **Premier démarrage** : 5-10 minutes (Docker doit télécharger l'image MariaDB et builder l'image PHP 8.5 avec Node.js).

Démarrages suivants : 5-10 secondes.

## 5. Générer la clé Laravel

```bash
./vendor/bin/sail artisan key:generate
```

## 6. Migrer la base de données

```bash
./vendor/bin/sail artisan migrate
```

## 7. Installer les dépendances Node

```bash
./vendor/bin/sail npm install
```

## 8. Lancer Vite

```bash
./vendor/bin/sail npm run dev
```

Cette commande reste active — garde ce terminal ouvert pendant que tu codes.

## 9. Tester dans le navigateur

- **App Laravel** → http://localhost:8000
- **Vite dev server** → http://localhost:5173

Tu dois voir la page d'accueil avec les composants Vue chargés et le hot reload qui fonctionne.

## Voilà 🎉

Pour la suite, voir [03-commandes-courantes.md](03-commandes-courantes.md).
