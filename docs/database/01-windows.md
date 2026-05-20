# Installation sur Windows

## Étape 1 — Télécharger MariaDB

1. Va sur [https://mariadb.org/download/](https://mariadb.org/download/)
2. Choisis :
   - **OS** : Windows
   - **Version** : la dernière `GA` (Generally Available)
   - **Architecture** : `x86_64`
3. Clique **Download** et récupère le fichier `.msi`.

## Étape 2 — Lancer l'installateur

1. Double-clique sur le `.msi` téléchargé.
2. Clique **Next** jusqu'à arriver à la page **User settings**.
3. **Important** — remplis cette page :
   - **New root password** : choisis un mot de passe (ex : `root`). **Note-le.**
   - **Confirm** : le même mot de passe.
   - **Enable access from remote machines for 'root' user** : laisse **décoché**.
   - **Use UTF8 as default server's character set** : **coche-le**.
4. Clique **Next**.
5. Sur la page **Default instance properties**, laisse tout par défaut :
   - **Service Name** : `MariaDB`
   - **TCP port** : `3306`
6. Clique **Next** → **Install** → attends → **Finish**.

## Étape 3 — Vérifier que le service tourne

1. Ouvre le menu Démarrer → tape `services.msc` → Entrée.
2. Cherche `MariaDB` dans la liste.
3. La colonne **State** doit afficher **Running**. Si c'est `Stopped`, fais clic droit → **Start**.

## Étape 4 — Vérifier la connexion en ligne de commande

1. Ouvre **PowerShell** (touche Windows → tape `powershell` → Entrée).
2. Tape :
   ```powershell
   mysql -u root -p
   ```
3. Entre le mot de passe `root` que tu as choisi à l'étape 2.
4. Si tu vois le prompt `MariaDB [(none)]>`, c'est bon. Tape `EXIT;` pour sortir.

> Si la commande `mysql` n'est pas reconnue, MariaDB n'a pas été ajouté au `PATH`. Ferme et rouvre PowerShell, ou utilise le chemin complet : `"C:\Program Files\MariaDB 11.x\bin\mysql.exe" -u root -p`

## Étape 5 — Installer l'extension PHP pour MySQL

Cette étape branche **PHP** à MariaDB. Sans ça, Laravel ne pourra pas se connecter.

1. Trouve où PHP est installé :
   ```powershell
   where.exe php
   ```
   Tu auras un chemin du genre `C:\php\php.exe` ou `C:\xampp\php\php.exe`.

2. Ouvre le dossier de PHP dans l'explorateur.
3. Ouvre le fichier `php.ini` avec un éditeur de texte (Notepad, VS Code).
4. Cherche ces lignes (avec Ctrl+F) :
   ```ini
   ;extension=pdo_mysql
   ;extension=mysqli
   ```
5. **Enlève le point-virgule** au début de chaque ligne :
   ```ini
   extension=pdo_mysql
   extension=mysqli
   ```
6. Sauvegarde le fichier.

## Étape 6 — Vérifier que PHP voit MariaDB

Dans PowerShell :

```powershell
php -m | Select-String -Pattern "mysql"
```

Tu dois voir au moins `pdo_mysql` apparaître. Si oui → passe au fichier **[04-configurer-projet.md](04-configurer-projet.md)**.

Si non → vérifie que tu as bien modifié le **bon** `php.ini` (`php --ini` te dit lequel est chargé).
