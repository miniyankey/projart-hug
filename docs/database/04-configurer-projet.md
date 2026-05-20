# Créer la DB et brancher le projet Laravel

Cette étape est **commune aux 3 OS**. Tu dois avoir terminé l'installation de MariaDB avant.

## Étape 1 — Se connecter à MariaDB

Selon ton OS :

- **Windows** : `mysql -u root -p` puis entre le mot de passe `root` choisi à l'installation.
- **macOS** : `mariadb -u root`
- **Linux** : `sudo mariadb -u root`

Tu dois arriver dans le prompt `MariaDB [(none)]>`.

## Étape 2 — Créer la base de données et l'utilisateur

Copie-colle ces 4 lignes une à une dans le prompt MariaDB. Remplace `ton_mot_de_passe` par ce que tu veux (note-le, tu en aura besoin à l'étape 4).

```sql
CREATE DATABASE projart_hug CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'projart'@'localhost' IDENTIFIED BY 'ton_mot_de_passe';
GRANT ALL PRIVILEGES ON projart_hug.* TO 'projart'@'localhost';
FLUSH PRIVILEGES;
```

Puis sors :

```sql
EXIT;
```

### Vérifier que tout est en place

```bash
mariadb -u projart -p projart_hug
```

Entre `ton_mot_de_passe`. Si tu arrives dans le prompt `MariaDB [projart_hug]>`, c'est gagné. Tape `EXIT;` pour sortir.

## Étape 3 — Copier `.env.example` vers `.env`

À la racine du projet :

```bash
cp .env.example .env
```

> Sur Windows en PowerShell : `Copy-Item .env.example .env`

## Étape 4 — Mettre ton mot de passe dans `.env`

Ouvre le fichier `.env` à la racine du projet et trouve cette section :

```ini
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projart_hug
DB_USERNAME=projart
DB_PASSWORD=
```

Mets ton mot de passe après `DB_PASSWORD=` :

```ini
DB_PASSWORD=ton_mot_de_passe
```

> Le fichier `.env` est dans `.gitignore` — il ne sera jamais pushé sur GitHub. Tes credentials restent locales.

## Étape 5 — Générer la clé d'application

```bash
php artisan key:generate
```

## Étape 6 — Lancer les migrations

```bash
php artisan migrate
```

Tu dois voir une série de lignes du genre :

```
INFO  Preparing database.
INFO  Running migrations.
2024_xx_xx_xxxxxx_create_users_table .................... DONE
2024_xx_xx_xxxxxx_create_cache_table .................... DONE
...
```

## Étape 7 — Vérifier visuellement (optionnel)

Ouvre **DBeaver** (ou un autre client), crée une connexion :

- **Connexion** : MariaDB
- **Host** : `localhost`
- **Port** : `3306`
- **Database** : `projart_hug`
- **Username** : `projart`
- **Password** : `ton_mot_de_passe`

Tu devrais voir les tables `users`, `migrations`, `cache`, `jobs`, `sessions`, etc.

---

**Fini.** Si tu as un problème, va voir [05-depannage.md](05-depannage.md).
