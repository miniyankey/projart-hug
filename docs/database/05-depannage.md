# Dépannage

Erreurs fréquentes rencontrées lors du setup de MariaDB.

## `could not find driver`

```
Illuminate\Database\QueryException
could not find driver (Connection: mariadb, ...)
```

**Cause** : l'extension PHP `pdo_mysql` n'est pas installée ou pas activée.

**Solution** :

- **Windows** : ouvre `php.ini`, décommente `extension=pdo_mysql` (enlève le `;`). Voir [01-windows.md](01-windows.md) étape 5.
- **macOS** : `brew install php` (réinstalle PHP via Homebrew avec `pdo_mysql` inclus).
- **Fedora** : `sudo dnf install php-mysqlnd -y`
- **Ubuntu/Debian** : `sudo apt install php-mysql -y`

Vérifie ensuite avec `php -m | grep mysql`.

## `SQLSTATE[HY000] [2002] Connection refused`

**Cause** : le serveur MariaDB n'est pas démarré.

**Solution** :

- **Windows** : ouvre `services.msc`, cherche `MariaDB`, clic droit → **Start**.
- **macOS** : `brew services start mariadb`
- **Linux** : `sudo systemctl start mariadb`

## `SQLSTATE[HY000] [1045] Access denied for user 'projart'@'localhost'`

**Cause** : le mot de passe dans `.env` ne correspond pas à celui de l'utilisateur en base.

**Solution** : reconnecte-toi en root et change le mot de passe :

```bash
# Linux
sudo mariadb -u root

# macOS
mariadb -u root

# Windows
mysql -u root -p
```

Puis :

```sql
ALTER USER 'projart'@'localhost' IDENTIFIED BY 'nouveau_mot_de_passe';
FLUSH PRIVILEGES;
EXIT;
```

Mets le **même** mot de passe dans le fichier `.env` à la ligne `DB_PASSWORD=`.

## `SQLSTATE[HY000] [1049] Unknown database 'projart_hug'`

**Cause** : la base de données n'a pas été créée.

**Solution** : refais l'étape 2 du fichier [04-configurer-projet.md](04-configurer-projet.md).

## `mysql` ou `mariadb` : commande introuvable

**Cause** : MariaDB n'est pas dans le `PATH`.

**Solutions** :

- **Windows** : ferme et rouvre PowerShell, ou utilise le chemin complet `"C:\Program Files\MariaDB 11.x\bin\mysql.exe"`.
- **macOS/Linux** : vérifie que l'installation s'est bien terminée. Tape `which mariadb`. Si vide, réinstalle.

## Les migrations passent mais DBeaver ne voit rien

**Cause** : tu regardes peut-être la mauvaise connexion ou la mauvaise DB.

**Solution** :

1. Dans DBeaver, fais clic droit sur ta connexion → **Refresh** (ou F5).
2. Vérifie que tu es bien dans la DB `projart_hug` et pas `mysql` ou `information_schema`.

## Toujours bloqué ?

Demande de l'aide sur le canal Discord/Slack du projet, en partageant :
1. Ton OS et sa version
2. La sortie complète de l'erreur
3. La sortie de `php -m | grep mysql`
4. La sortie de `php artisan about` (sans les valeurs sensibles)
