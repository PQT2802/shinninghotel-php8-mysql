# Shinning Hotel — Production deploy (GHCR + VPS)

**Hướng dẫn tiếng Việt từng bước (copy-paste):** [HUONG-DAN-TUNG-BUOC.md](HUONG-DAN-TUNG-BUOC.md)  
**Checklist in tay:** [CHECKLIST.md](CHECKLIST.md)  
**Scripts:** [scripts/](scripts/) (`import-db.ps1`, `copy-config-to-vps.ps1`, `vps-setup.sh`)

Pipeline: push `main` → GitHub Actions build image → GHCR → VPS `docker compose pull && up -d`.

VPS **không** chứa source git — chỉ `docker-compose.yml`, `.env`, và Docker volumes.

| Item | Value |
|------|--------|
| Image | `ghcr.io/pqt2802/shinninghotel-php8-mysql:latest` |
| VPS path | `/opt/apps/shinninghotel` |
| App container | `shinninghotel_app` |
| MySQL container | `shinninghotel_mysql` |
| Public port | `8081` → app `:80` |

---

## 1. GitHub (one-time)

### Secrets (repo Settings → Secrets → Actions)

| Secret | Description |
|--------|-------------|
| `VPS_HOST` | VPS IP or hostname |
| `VPS_USER` | SSH user with `docker` group |
| `VPS_SSH_KEY` | Private key (PEM) |

### Workflow permissions

Settings → Actions → General → Workflow permissions: **Read and write**.

Package visibility: GHCR package linked to this repo; if **private**, VPS must `docker login ghcr.io` (see below).

---

## 2. VPS setup (one-time)

```bash
apt update
apt install -y docker.io docker-compose-plugin nginx certbot python3-certbot-nginx ufw
systemctl enable --now docker
usermod -aG docker $USER   # re-login after this

mkdir -p /opt/apps/shinninghotel
cd /opt/apps/shinninghotel
```

Copy templates from this repo:

```bash
# On your machine (from repo root)
scp deploy/vps/docker-compose.yml.example user@VPS:/opt/apps/shinninghotel/docker-compose.yml
scp deploy/vps/.env.example user@VPS:/opt/apps/shinninghotel/.env
```

On VPS: edit `docker-compose.yml` and `.env` — replace all `CHANGE_ME_*` and `YOUR_VPS_IP`.  
`DB_PASSWORD` in `.env` must equal `MYSQL_PASSWORD` in compose.  
Update the MySQL healthcheck root password in compose to match `MYSQL_ROOT_PASSWORD`.

### Firewall (IP-only access on port 8081)

```bash
ufw allow OpenSSH
ufw allow 8081/tcp
ufw enable
```

### GHCR login (if package is private)

```bash
docker login ghcr.io -u PQT2802
# Password: GitHub PAT with read:packages
```

---

## 3. First deploy

After the first successful GitHub Actions run (or manual pull):

```bash
cd /opt/apps/shinninghotel
docker compose pull
docker compose up -d
```

Verify:

```bash
docker ps
docker logs shinninghotel_app --tail 50
docker logs shinninghotel_mysql --tail 50
curl -I http://127.0.0.1:8081
curl -I http://YOUR_VPS_IP:8081/en/
curl -I http://YOUR_VPS_IP:8081/vi/
```

---

## 4. Database import (one-time)

MySQL volume is empty on first run. Import from a machine that has this repo (replace `PASS` and `user@VPS`).

Production DB name: `shinninghotel_db` (dev SQL files use `shinning_hotel`).

### PowerShell (Windows)

```powershell
$ssh = "user@VPS"
$pass = "YOUR_DB_PASSWORD"

# 1. Schema (skip CREATE DATABASE / USE)
Get-Content database\schema.sql | Where-Object { $_ -notmatch '^(CREATE DATABASE|USE )' } |
  ssh $ssh "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p$pass shinninghotel_db"

# 2. Seed
(Get-Content database\seeders\seed.sql) -replace 'USE shinning_hotel','USE shinninghotel_db' |
  ssh $ssh "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p$pass shinninghotel_db"

# 3. i18n migrations (required for /vi/)
foreach ($f in @('012_translations.sql','013_repair_vi_utf8.sql','014_room_rich_content.sql','015_sync_en_vi_translations.sql')) {
  Get-Content "database\migrations\$f" |
    ssh $ssh "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p$pass shinninghotel_db"
}
```

### Bash (Linux/macOS)

```bash
SSH=user@VPS
PASS=YOUR_DB_PASSWORD

grep -v -E '^(CREATE DATABASE|USE )' database/schema.sql | \
  ssh $SSH "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p${PASS} shinninghotel_db"

sed 's/USE shinning_hotel/USE shinninghotel_db/' database/seeders/seed.sql | \
  ssh $SSH "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p${PASS} shinninghotel_db"

for f in 012_translations.sql 013_repair_vi_utf8.sql 014_room_rich_content.sql 015_sync_en_vi_translations.sql; do
  ssh $SSH "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p${PASS} shinninghotel_db" < "database/migrations/$f"
done
```

Default admin: `admin@shinning.com` / `password` — change immediately.

Optional VI content sync:

```bash
docker exec shinninghotel_app php scripts/seed_vi_from_en.php --only-missing
```

---

## 5. QA checklist

- [ ] `http://VPS_IP:8081/en/` — homepage, menu, booking bar
- [ ] `http://VPS_IP:8081/vi/` — Vietnamese diacritics correct
- [ ] `http://VPS_IP:8081/admin/login` — CMS loads
- [ ] Admin → edit page/news/room — TinyMCE loads (`/assets/vendor/tinymce/`)
- [ ] Upload media — survives `docker compose pull && up -d` (volumes mounted)

---

## 6. Nginx + SSL (optional, when you have a domain)

`/etc/nginx/sites-available/shinninghotel.conf`:

```nginx
server {
    listen 80;
    server_name hotel.example.com;

    location / {
        proxy_pass http://127.0.0.1:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
ln -s /etc/nginx/sites-available/shinninghotel.conf /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
certbot --nginx -d hotel.example.com
```

Update `APP_URL` in `.env` to `https://hotel.example.com`.  
For Nginx-only public access, change compose ports to `127.0.0.1:8081:80` and close `8081` in UFW.

---

## 7. Reuse for other projects

1. Copy `Dockerfile`, `.dockerignore`, `.github/workflows/deploy.yml`
2. Change `IMAGE_NAME`, `APP_DIR`, container names in workflow and `deploy/vps/*`
3. Adjust Dockerfile for Laravel (`public/`, `artisan`, `APP_KEY`) if needed
4. Document DB import steps in `deploy/README.md`

---

## Notes

- **Uploads** persist via named volumes in `docker-compose.yml.example`.
- **CDN** (Bootstrap, Google Fonts): VPS needs outbound HTTPS.
- **TinyMCE** is bundled in the image under `public/assets/vendor/tinymce/`.
- **Logs**: `storage/logs/app.log` inside container; not persisted by default.
