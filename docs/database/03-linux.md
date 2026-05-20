# Installation sur Linux

Choisis la section qui correspond à ta distribution.

## Fedora / RHEL / CentOS

### Étape 1 — Installer MariaDB

```bash
sudo dnf install mariadb-server mariadb -y
```

### Étape 2 — Démarrer et activer le service

```bash
sudo systemctl enable --now mariadb
```

Vérifie qu'il tourne :

```bash
sudo systemctl status mariadb
```

Tu dois voir **active (running)** en vert.

### Étape 3 — Installer l'extension PHP

```bash
sudo dnf install php-mysqlnd -y
```

## Ubuntu / Debian

### Étape 1 — Installer MariaDB

```bash
sudo apt update
sudo apt install mariadb-server mariadb-client -y
```

### Étape 2 — Démarrer et activer le service

```bash
sudo systemctl enable --now mariadb
sudo systemctl status mariadb
```

Tu dois voir **active (running)**.

### Étape 3 — Installer l'extension PHP

```bash
sudo apt install php-mysql -y
```

## Étape commune — Vérifier la connexion

```bash
sudo mariadb -u root
```

Tu devrais arriver dans un prompt `MariaDB [(none)]>`. Tape `EXIT;` pour sortir.

> Par défaut sur Linux, `root` utilise l'auth par socket Unix — pas de mot de passe demandé tant que tu passes par `sudo`. C'est normal et suffisant pour du dev local.

## Étape commune — Vérifier que PHP voit MariaDB

```bash
php -m | grep mysql
```

Tu dois voir `pdo_mysql` et `mysqli` apparaître. Si oui → passe au fichier **[04-configurer-projet.md](04-configurer-projet.md)**.
