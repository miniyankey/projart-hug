# Guide de Déploiement — Infomaniak (Hébergement mutualisé)

Ce guide détaille le déploiement complet du projet ProjArt HUG sur un hébergement mutualisé Infomaniak avec PHP 8.5+.

---

## 1. Prérequis

- ✅ Domaine enregistré (ex: `projart-hug.ch`)
- ✅ Hébergement web Infomaniak actif
- ✅ Accès administrateur au panneau de contrôle Infomaniak (Manager)
- ✅ Client SSH sur votre machine (OpenSSH, Putty, etc.) ou utiliser celui disponible sur le manager d'Infomaniak
- ✅ Brevo doit avoir validé le domaine avec les entées DNS

---

## 2. Configuration de l'hébergement Infomaniak

### 2.1 Créer un nouveau site vierge

1. **Connexion au Manager Infomaniak**
   - Accédez à [manager.infomaniak.com](https://manager.infomaniak.com)
   - Identifiez-vous avec vos identifiants

2. **Créer un nouveau site**
   - Menu : **Hébergement Web** → Cliquez sur votre produit d'hébergement
   - Onglet **Sites** → Bouton **Créer un site**
   - Sélectionnez **Installation manuelle** (pas de builder)
   - Configurez :
     - **Domaine principal** : `projart-hug.ch` (adapter selon votre domaine)
     - **Répertoire racine** : `/public` *(défini plus tard après le clone)*
     - **Version PHP** : `8.5` ou supérieure

3. **Valider** et attendre quelques minutes que le site soit créé

### 2.2 Lier le domaine

- Le domaine est automatiquement lié lors de la création du site
- Vérifiez dans **Domaines** que les DNS pointent vers Infomaniak :
  ```
  A → IP de l'hébergement (visible dans le Manager)
  MX → mail.infomaniak.com (pour la messagerie)
  ```

---

## 3. Créer la base de données et l'utilisateur

1. **Dans le Manager Infomaniak**
   - Onglet **Bases de données**
   - Bouton **Créer une base de données**

2. **Configuration**
   - **Nom de la base** : `projart_hug_prod` (ou similaire)

3. **Créer un utilisateur DB**
   - Après création de la base, onglet **Utilisateurs de base de données**
   - **Ajouter un utilisateur** :
     - **Nom d'utilisateur** : `projart_user`
     - **Mot de passe** : Générer un mot de passe fort (min 16 caractères)
     - **Droits** : Cocher toutes les permissions (SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, GRANT, REFERENCES, etc.)

4. **Noter les identifiants** :
   ```
   Host: mysql.infomaniak.com (ou similaire, fourni par Infomaniak)
   Database: projart_hug_prod
   User: projart_user
   Password: [votre_mot_de_passe_fort]
   Port: 3306
   ```

---

## 4. Configuration email et accès utilisateur

### 4.1 Créer une adresse email

1. **Dans le Manager Infomaniak**
   - Onglet **Messagerie** → **Créer une adresse email**
   - **Adresse** : `noreply@projart-hug.ch` (ou `contact@projart-hug.ch`)
   - **Mot de passe** : Générer un mot de passe fort

2. **Configurer les paramètres SMTP** (pour Brevo ou autre service)
   - **Serveur SMTP** : `smtp.infomaniak.com`
   - **Port** : `587` (TLS) ou `465` (SSL)
   - **Authentification** : Utiliser l'adresse email créée

### 4.2 Créer un accès utilisateur SSH

1. **Onglet **Accès SSH** → **Ajouter un utilisateur**
   - **Nom d'utilisateur** : `projart_deploy` (ou similaire)
   - **Mot de passe** : Générer un mot de passe fort
   - **Autoriser la connexion SSH** : ✅ Cocher

2. **Noter les identifiants de connexion** :
   ```
   Host: ssh.infomaniak.com (ou IP fournie)
   User: projart_deploy
   Password: [votre_mot_de_passe]
   Port: 22
   ```

---

## 5. Connexion SSH et préparation

### 5.1 Se connecter en SSH

```bash
ssh projart_deploy@ssh.infomaniak.com
# Entrer le mot de passe quand demandé
```

### 5.2 Naviguer vers le répertoire du site

```bash
cd sites/projart-hug.ch
# ou le chemin fourni par Infomaniak
```

### 5.3 Vérifier la version PHP

```bash
php -v
# Doit afficher PHP 8.5 ou supérieure
```

---

## 6. Cloner le projet

```bash
# Supprimer les fichiers par défaut si présents
rm -rf *
rm -rf .git

# Cloner le projet (depuis la branche main ou production)
git clone https://github.com/miniyankey/projart-hug.git .
# (le point à la fin clone dans le répertoire courant)

# Optionnel : checkout la branche production. Dans notre cas nous utiliserons le main
git checkout production
```

---

## 7. Installer Node.js et npm/pnpm

### 7.1 Vérifier Node.js disponible

```bash
node -v
npm -v
```

Si non disponible ou version < 18 :

```bash
touch ~/.bashrc
 
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
 
source ~/.bashrc
 
nvm install --lts
```

Vérifier que Node et Npm sont bien installés

```bash
node -v && npm -v
```

## 8. Installer les dépendances Laravel et npm

### 8.1 Dépendances PHP (Composer)

```bash
# Infomaniak dispose généralement de Composer
composer install --optimize-autoloader --no-dev
# --no-dev : Ne pas installer les dépendances de développement en prod
```

### 8.2 Dépendances npm/pnpm

```bash
npm ci
```

---

## 9. Builder le projet Vue/Vite

```bash
# Build pour la production
npm run build

# Vérifier la création de public/build
ls -la public/build
```

---

## 10. Configuration du fichier .env

### 10.1 Copier l'exemple

```bash
cp .env.example .env
```

### 10.2 Éditer le fichier .env

```bash
nano .env
# ou vim .env
```

### 10.3 Paramètres essentiels à modifier

```env
# Environnement
APP_NAME="Mission Donneur"
APP_ENV=prod
APP_KEY=
APP_DEBUG=false
APP_URL=https://projart-hug.ch

# Base de données
DB_CONNECTION=mysql
DB_HOST=mysql.infomaniak.com
DB_PORT=3306
DB_DATABASE=projart_hug_prod
DB_USERNAME=projart_user
DB_PASSWORD=votre_mot_de_passe_fort


# Mail - Configuration Brevo ou Infomaniak SMTP
MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_SCHEME=null
MAIL_FROM_ADDRESS="info@missiondonneur.ch"
MAIL_FROM_NAME="Mission Donneur - HUG"

# Si utilisation de Brevo (service d'emailing)
BREVO_API_KEY=
BREVO_LIST_ID=

# Localisation
APP_LOCALE=
APP_FALLBACK_LOCALE=
APP_FAKER_LOCALE=

```

**Sauvegarder** : `Ctrl+X` → `Y` → `Enter` (nano)

---

## 11. Générer la clé d'application Laravel

```bash
php artisan key:generate
# Vérifie que APP_KEY est rempli dans .env
cat .env | grep APP_KEY
```

---

## 12. Migrer la base de données et seeders

### 12.1 Exécuter les migrations

```bash
php artisan migrate --force
# --force : Obligatoire en production (demande confirmation sinon)
```

### 12.2 Exécuter les seeders (si nécessaire)

```bash
# Seeder unique
php artisan db:seed --force
```

**Note** : Les seeders ne doivent généralement s'exécuter qu'une seule fois en prod. Ne pas relancer sans vérification.

### 12.3 Créer le lien de stockage

```bash
php artisan storage:link
```

**Explication** : Cette commande crée un symlink public pour que les fichiers uploadés dans `storage/app/public` soient accessibles via `public/storage`. Essentiel pour les uploads d'images, documents, etc.

---

## 13. Mise en cache Laravel

### 13.1 Cache de configuration

```bash
php artisan config:cache
```

### 13.2 Cache des routes

```bash
php artisan route:cache
```

### 13.3 Cache des vues (Blade)

```bash
php artisan view:cache
```

### 13.4 Reset les caches

```bash
php artisan cache:clear
# (optionnel, si besoin de reset)
```

---

## 14. Configurer la racine Web (Document Root)

### 14.1 Via le Manager Infomaniak

1. **Manager → Sites → [Votre site]**
2. **Onglet Général** ou **Paramètres avancés**
3. **Répertoire racine (Document Root)** :
   - Chemin actuel : `/sites/projart-hug.ch`
   - **À changer en** : `/sites/projart-hug.ch/public`
   - Sauvegarder

2. **Attendre 5-10 minutes** que la configuration soit appliquée


---

## 15. Sécuriser avec HTTPS

### 15.1 Certificat SSL via Infomaniak

1. **Manager → Sites → [Votre site]**
2. **Onglet SSL/TLS**
3. **Certificat SSL** :
   - Option **Let's Encrypt gratuit** (recommandé) : 
     - Cliquer sur **Générer un certificat Let's Encrypt**
     - Attendre quelques minutes (Infomaniak configure automatiquement)
   - Ou **Certificat personnel** (si déjà acheté)

---

## 16. Tests post-déploiement ⚠️

### 16.1 Vérifier l'accès au site

Ouvrir son navigateur et aller sur l'URL du site.
