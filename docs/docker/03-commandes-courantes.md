# 3. Commandes courantes

## Alias pour éviter `./vendor/bin/sail` à chaque fois

Ajoute dans ton `~/.bashrc` ou `~/.zshrc` :

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Puis `source ~/.bashrc`. Tu pourras taper `sail` au lieu de `./vendor/bin/sail`. **Le reste de ce doc utilise `sail` pour la concision.**

## Démarrer / arrêter

| Action | Commande |
|---|---|
| Démarrer en arrière-plan | `sail up -d` |
| Démarrer en avant-plan (voir les logs) | `sail up` |
| Arrêter | `sail down` |
| Redémarrer | `sail restart` |
| Reset complet (⚠️ efface la DB) | `sail down -v` |

## Voir l'état

| Action | Commande |
|---|---|
| État des containers | `sail ps` |
| Logs en direct | `sail logs -f` |
| Logs d'un service | `sail logs -f mariadb` |

## Lancer du code

| Action | Commande |
|---|---|
| Artisan | `sail artisan <commande>` |
| Migrate | `sail artisan migrate` |
| Tinker | `sail tinker` |
| Composer | `sail composer <commande>` |
| npm | `sail npm <commande>` |
| Vite dev | `sail npm run dev` |
| Build prod | `sail npm run build` |

## Tests & qualité de code

| Action | Commande |
|---|---|
| Lancer Pest | `sail test` |
| Lancer un test précis | `sail test --filter=NomDuTest` |
| Lint PHP (Pint) | `sail composer lint` |
| Lint JS/TS (ESLint) | `sail npm run lint` |

## Accéder à l'intérieur du container

| Action | Commande |
|---|---|
| Shell dans le container PHP | `sail shell` |
| Shell `root` | `sail root-shell` |
| Client MariaDB | `sail mariadb` |

## Base de données

Pour brancher un client externe (DBeaver, TablePlus, MySQL Workbench) sur la DB du container :

| Paramètre | Valeur |
|---|---|
| Host | `127.0.0.1` |
| Port | `3307` (valeur de `FORWARD_DB_PORT` dans `.env`) |
| Database | `projart_hug` |
| User | `projart` |
| Password | `password` |

> ⚠️ Le port `3307` est utilisé **depuis ton hôte**. **Dans le container Laravel**, MariaDB est sur `mariadb:3306` (c'est ce qu'on trouve dans `.env`).
