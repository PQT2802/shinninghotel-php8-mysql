# Hướng dẫn deploy production — từng bước (Shinning Hotel)

Làm **theo thứ tự**. Điền trước [`config.example.env`](config.example.env) → lưu thành `my.env.local` (không commit).

---

## Bước 0 — Chuẩn bị trên máy Windows (một lần)

- [ ] Có tài khoản GitHub `PQT2802`, quyền push repo `shinninghotel-php8-mysql`
- [ ] Có VPS Ubuntu (root hoặc user + sudo), biết IP
- [ ] Cài Git, Docker Desktop (để test build local nếu muốn), OpenSSH client

---

## Bước 1 — Push code deploy lên GitHub

Trên máy dev, trong thư mục project:

```powershell
cd d:\Project\php8-hotel-landing

git add Dockerfile .dockerignore .github deploy README.md
git status
git commit -m "Add production Docker deploy (GHCR + GitHub Actions + VPS compose)"
git push origin main
```

- [ ] Push thành công lên `main`

---

## Bước 2 — Cấu hình GitHub (một lần)

### 2.1 Secrets

Vào: `https://github.com/PQT2802/shinninghotel-php8-mysql/settings/secrets/actions`

| Secret | Giá trị |
|--------|---------|
| `VPS_HOST` | IP VPS (vd `123.456.789.0`) |
| `VPS_USER` | `root` hoặc user SSH |
| `VPS_SSH_KEY` | **Toàn bộ** nội dung file private key `.pem` |

Tạo SSH key (nếu chưa có), trên Windows PowerShell:

```powershell
ssh-keygen -t ed25519 -C "deploy-shinninghotel" -f "$env:USERPROFILE\.ssh\shinninghotel_deploy"
# Copy public key lên VPS:
type $env:USERPROFILE\.ssh\shinninghotel_deploy.pub
# Dán vào VPS: ~/.ssh/authorized_keys
# Copy private key vào GitHub Secret VPS_SSH_KEY:
type $env:USERPROFILE\.ssh\shinninghotel_deploy
```

### 2.2 Quyền Actions

`Settings` → `Actions` → `General` → **Workflow permissions**: *Read and write permissions*.

### 2.3 Chạy workflow lần đầu

- [ ] Sau push Bước 1, mở tab **Actions** → workflow **Build and Deploy Shinning Hotel** → đợi job **build** xong (push image GHCR)
- [ ] Nếu job **Deploy to VPS** fail vì VPS chưa setup — bình thường, làm Bước 3–4 rồi chạy lại workflow

---

## Bước 3 — Cài Docker trên VPS (một lần)

SSH vào VPS:

```powershell
ssh root@123.456.789.0
```

Trên VPS, chạy (copy nguyên khối):

```bash
apt update
apt install -y docker.io docker-compose-plugin nginx certbot python3-certbot-nginx ufw curl
systemctl enable --now docker

mkdir -p /opt/apps/shinninghotel
cd /opt/apps/shinninghotel
```

- [ ] `docker --version` và `docker compose version` chạy được

---

## Bước 4 — Copy file cấu hình lên VPS

**Trên máy Windows** (đổi IP/user cho đúng):

```powershell
cd d:\Project\php8-hotel-landing

$VPS = "root@123.456.789.0"

scp deploy/vps/docker-compose.yml.example "${VPS}:/opt/apps/shinninghotel/docker-compose.yml"
scp deploy/vps/.env.example "${VPS}:/opt/apps/shinninghotel/.env"
```

**Trên VPS**, sửa mật khẩu và URL:

```bash
nano /opt/apps/shinninghotel/docker-compose.yml
nano /opt/apps/shinninghotel/.env
```

Thay **tất cả**:

- `CHANGE_ME_SHINNINGHOTEL_PASSWORD` → mật khẩu DB app (giống nhau ở compose + `.env` `DB_PASSWORD`)
- `CHANGE_ME_SHINNINGHOTEL_ROOT_PASSWORD` → mật khẩu root MySQL (cả trong healthcheck dòng `mysqladmin`)
- `YOUR_VPS_IP` trong `.env` → IP thật (vd `http://123.456.789.0:8081`)

- [ ] `DB_PASSWORD` trong `.env` = `MYSQL_PASSWORD` trong compose

### Firewall (truy cập bằng IP:8081)

```bash
ufw allow OpenSSH
ufw allow 8081/tcp
ufw enable
ufw status
```

---

## Bước 5 — Đăng nhập GHCR trên VPS (nếu package private)

Trên VPS:

```bash
docker login ghcr.io -u PQT2802
```

Password: GitHub → **Settings** → **Developer settings** → **Personal access tokens** → token có quyền `read:packages`.

- [ ] `Login Succeeded`

---

## Bước 6 — Pull image và chạy container

Trên VPS:

```bash
cd /opt/apps/shinninghotel
docker compose pull
docker compose up -d
docker ps
```

Kiểm tra nhanh:

```bash
curl -I http://127.0.0.1:8081
docker logs shinninghotel_app --tail 30
docker logs shinninghotel_mysql --tail 30
```

- [ ] Hai container `Up` (mysql `healthy`)
- [ ] `curl` trả HTTP (có thể 500 nếu chưa import DB — bước 7)

**Hoặc** trên GitHub: **Actions** → **Re-run all jobs** sau khi VPS đã sẵn sàng.

---

## Bước 7 — Import database (một lần)

Chạy **từ máy Windows** (đã có repo + SSH tới VPS).

### Cách A — Script tự động (khuyến nghị)

Sửa biến đầu file `deploy/scripts/import-db.ps1` rồi:

```powershell
cd d:\Project\php8-hotel-landing
powershell -ExecutionPolicy Bypass -File deploy\scripts\import-db.ps1
```

### Cách B — Lệnh tay

```powershell
$VPS = "root@123.456.789.0"
$PASS = "Mat_Khau_DB_App_Cua_Ban"

# Schema
Get-Content database\schema.sql | Where-Object { $_ -notmatch '^(CREATE DATABASE|USE )' } |
  ssh $VPS "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p$PASS shinninghotel_db"

# Seed
(Get-Content database\seeders\seed.sql) -replace 'USE shinning_hotel','USE shinninghotel_db' |
  ssh $VPS "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p$PASS shinninghotel_db"

# Migrations i18n (bắt buộc cho /vi/)
foreach ($f in @('012_translations.sql','013_repair_vi_utf8.sql','014_room_rich_content.sql','015_sync_en_vi_translations.sql')) {
  Write-Host "Import $f ..."
  Get-Content "database\migrations\$f" |
    ssh $VPS "docker exec -i shinninghotel_mysql mysql -ushinninghotel_user -p$PASS shinninghotel_db"
}
```

- [ ] Không lỗi SQL
- [ ] Mở trình duyệt: `http://IP_VPS:8081/en/` và `/vi/`

**Đăng nhập admin:** `admin@shinning.com` / `password` → **đổi mật khẩu ngay**.

---

## Bước 8 — Kiểm tra sau deploy

```powershell
# Thay IP
curl -I http://123.456.789.0:8081/en/
curl -I http://123.456.789.0:8081/vi/
curl -I http://123.456.789.0:8081/admin/login
```

Checklist:

- [ ] Trang `/vi/` hiển thị đúng dấu tiếng Việt
- [ ] `/admin/login` mở được, TinyMCE không 404
- [ ] Sửa Page/News/Room trong admin (route `{id}` OK)
- [ ] Upload ảnh Media → redeploy `docker compose pull && up -d` → ảnh vẫn còn

---

## Bước 9 — Deploy lần sau (hàng ngày)

Chỉ cần push `main`:

```powershell
git add .
git commit -m "Your message"
git push origin main
```

GitHub Actions tự: build image → push GHCR → SSH VPS → `docker compose pull && up -d`.

Theo dõi: tab **Actions** trên GitHub.

---

## Bước 10 — Domain + SSL (khi có domain, tùy chọn)

Trên VPS:

```bash
# Đổi compose ports thành 127.0.0.1:8081:80 nếu chỉ muốn Nginx ra ngoài
nano /opt/apps/shinninghotel/docker-compose.yml

cp /path/to/nginx.conf.example /etc/nginx/sites-available/shinninghotel.conf
# Sửa server_name hotel.example.com
ln -s /etc/nginx/sites-available/shinninghotel.conf /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
certbot --nginx -d hotel.example.com
```

Cập nhật `.env`: `APP_URL=https://hotel.example.com`

---

## Xử lý lỗi thường gặp

| Triệu chứng | Cách xử lý |
|-------------|------------|
| `pull access denied` | `docker login ghcr.io` trên VPS; kiểm tra package public hoặc PAT |
| App 500 / DB error | Chưa import Bước 7; kiểm tra `.env` `DB_HOST=db` |
| `/vi/` lỗi / thiếu nội dung | Chạy migrations 012–015 |
| Không vào được từ internet | `ufw allow 8081`; compose phải `8081:80` không phải `127.0.0.1:8081:80` |
| Deploy SSH fail | Kiểm tra Secrets, key, `authorized_keys` trên VPS |

Chi tiết kỹ thuật: [README.md](README.md)
