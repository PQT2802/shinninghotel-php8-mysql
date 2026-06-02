#!/usr/bin/env bash
# Chạy TRÊN VPS (root hoặc sudo) — cài Docker, tạo thư mục app
# curl -sSL ... | bash   HOẶC: scp lên VPS rồi bash vps-setup.sh

set -euo pipefail

echo "==> Install packages"
apt update
apt install -y docker.io docker-compose-plugin nginx certbot python3-certbot-nginx ufw curl

echo "==> Enable Docker"
systemctl enable --now docker

echo "==> App directory"
mkdir -p /opt/apps/shinninghotel

echo "==> Firewall (SSH + app port 8081)"
ufw allow OpenSSH
ufw allow 8081/tcp
echo "y" | ufw enable || true
ufw status

echo ""
echo "Next steps (from your Windows machine):"
echo "  1. scp deploy/vps/docker-compose.yml.example user@VPS:/opt/apps/shinninghotel/docker-compose.yml"
echo "  2. scp deploy/vps/.env.example user@VPS:/opt/apps/shinninghotel/.env"
echo "  3. nano passwords on VPS, then: docker login ghcr.io"
echo "  4. cd /opt/apps/shinninghotel && docker compose pull && docker compose up -d"
echo "  5. Run deploy/scripts/import-db.ps1 from Windows"
echo ""
docker --version
docker compose version
