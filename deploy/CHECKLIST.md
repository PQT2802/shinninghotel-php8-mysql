# Checklist deploy — in và tick từng ô

**IP VPS:** ___________________  
**Ngày:** ___________________

## A. Máy dev / GitHub

- [ ] A1. Push `main` có `Dockerfile`, `.github/workflows/deploy.yml`, `deploy/`
- [ ] A2. Secret `VPS_HOST` đã tạo
- [ ] A3. Secret `VPS_USER` đã tạo
- [ ] A4. Secret `VPS_SSH_KEY` (private key) đã tạo
- [ ] A5. Actions workflow permissions: Read and write
- [ ] A6. Workflow build image GHCR thành công (tab Actions)

## B. VPS

- [ ] B1. `vps-setup.sh` hoặc apt install docker + compose
- [ ] B2. `/opt/apps/shinninghotel/docker-compose.yml` đã copy & sửa mật khẩu
- [ ] B3. `/opt/apps/shinninghotel/.env` đã copy & sửa `APP_URL` + `DB_*`
- [ ] B4. `docker login ghcr.io` (nếu package private)
- [ ] B5. `docker compose pull && docker compose up -d`
- [ ] B6. `ufw allow 8081/tcp`
- [ ] B7. `curl -I http://127.0.0.1:8081` OK

## C. Database

- [ ] C1. `import-db.ps1` hoặc lệnh tay — schema
- [ ] C2. seed
- [ ] C3. migration 012
- [ ] C4. migration 013
- [ ] C5. migration 014
- [ ] C6. migration 015

## D. QA

- [ ] D1. `http://IP:8081/en/` — trang chủ OK
- [ ] D2. `http://IP:8081/vi/` — tiếng Việt đúng dấu
- [ ] D3. `/admin/login` — đăng nhập OK
- [ ] D4. TinyMCE load trong admin
- [ ] D5. Đổi mật khẩu admin mặc định

## E. Lần deploy sau

- [ ] E1. Chỉ cần `git push origin main` → Actions tự deploy
