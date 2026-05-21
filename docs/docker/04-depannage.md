# 4. Dépannage

## "Docker or Podman is not running"

Le daemon Docker n'est pas démarré ou ton user n'a pas les droits.

**Linux** :
```bash
sudo systemctl start docker
sudo usermod -aG docker $USER
newgrp docker
```

**macOS / Windows** : ouvrir Docker Desktop et attendre qu'il dise "Engine running".

## "address already in use" — port déjà occupé

Un autre service utilise le port que Sail veut exposer.

### Port 8000 (Laravel)

Trouve le coupable :
```bash
ss -tlnp | grep :8000     # Linux
lsof -i :8000             # macOS
```

Soit tu tues le processus (`pkill -f "artisan serve"` si c'est ça), soit tu changes `APP_PORT` dans `.env` (ex: `APP_PORT=8080`).

### Port 3306 (MariaDB)

Sûrement MariaDB installé localement.

Option A — Garder MariaDB local et changer le port Sail :
```env
FORWARD_DB_PORT=3307
```

Option B — Arrêter MariaDB local :
```bash
sudo systemctl stop mariadb       # Linux
brew services stop mariadb        # macOS
```

## "getaddrinfo for mariadb failed"

Laravel n'arrive pas à résoudre le hostname `mariadb`. C'est qu'il tourne **en dehors** du container, ou que le container `mariadb` n'est pas démarré.

Vérifie :
```bash
sail ps
```

Si tu vois un seul container ou un statut `unhealthy`, fais :
```bash
sail down
sail up -d
sleep 15
sail ps
```

Vérifie aussi que tu utilises bien `sail artisan migrate` et **pas** `php artisan migrate`.

## Fichiers en `root:root` (Linux)

Les fichiers créés dans le container t'appartiennent à `root`. C'est que `WWWUSER`/`WWWGROUP` ne sont pas bons.

```bash
id -u   # à mettre dans WWWUSER
id -g   # à mettre dans WWWGROUP
```

Puis :
```bash
sail down
sail build --no-cache
sail up -d
```

Pour récupérer les fichiers existants :
```bash
sudo chown -R $USER:$USER .
```

## Vite ne hot-reload pas (Windows WSL2)

Tu as cloné le projet dans `C:\Users\...` au lieu de WSL2.

**Fix** : déplace le projet dans WSL2 :
```bash
cd ~
git clone <url-du-repo>
```

Puis lance Sail depuis WSL2.

## "Vite manifest not found"

Vite n'a pas généré les assets.

```bash
sail npm run dev
# ou pour la prod :
sail npm run build
```

## Erreur de mémoire au build (Mac/Windows)

Augmente la RAM allouée à Docker Desktop : Preferences → Resources → 4 Go minimum.

## Reset complet

Si rien ne va plus :
```bash
sail down -v        # ⚠️ efface la DB
docker system prune -af --volumes
sail build --no-cache
sail up -d
sail artisan migrate
```
