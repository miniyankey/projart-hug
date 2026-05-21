# 1. Prérequis — Installer Docker

## Windows

1. Télécharger **Docker Desktop** : https://www.docker.com/products/docker-desktop
2. Lancer l'installateur — il proposera d'installer **WSL2** automatiquement. **Accepte.**
3. Redémarrer Windows
4. Lancer Docker Desktop, accepter les conditions
5. Vérifier dans un terminal PowerShell ou Ubuntu (WSL) :
   ```bash
   docker --version
   docker compose version
   ```

> ⚠️ **Important** : clone le projet **dans WSL2** (pas dans `C:\Users\...`) sinon Vite sera très lent.
> Ouvre un terminal Ubuntu/WSL puis : `cd ~ && git clone <url-du-repo>`

## macOS

1. Télécharger **Docker Desktop** : https://www.docker.com/products/docker-desktop
   - Choisir la version Apple Silicon (M1/M2/M3/M4) ou Intel selon ta machine
2. Glisser l'application dans `Applications`
3. Lancer Docker Desktop, accepter les conditions
4. Vérifier dans un terminal :
   ```bash
   docker --version
   docker compose version
   ```

## Linux (Fedora / Ubuntu / Debian)

### Fedora

```bash
sudo dnf install -y docker docker-compose
sudo systemctl enable --now docker
```

### Ubuntu / Debian

```bash
sudo apt update
sudo apt install -y docker.io docker-compose-plugin
sudo systemctl enable --now docker
```

### Permettre à ton user d'utiliser Docker sans `sudo`

```bash
sudo usermod -aG docker $USER
newgrp docker
```

Vérifier :
```bash
docker ps
```

Si tu vois une liste vide (sans erreur "permission denied"), c'est bon.

## Vérification finale (tous OS)

```bash
docker run --rm hello-world
```

Si tu vois "Hello from Docker!", c'est prêt. Passe à [02-demarrage.md](02-demarrage.md).
