# Shinning Hotel — PHP 8 MVC Landing + CMS

**Brand:** Shinning Hotel  
**Slogan:** *Where Every Stay Shines*

Monolith PHP 8 MVC: public WebUI (đặt phòng, tin tức) + CMS Admin (TinyMCE, roles).

## Yêu cầu

| Thành phần | Cách cài |
|------------|----------|
| **MySQL** | Docker (`docker compose`) — chỉ DB trong container |
| **PHP ≥ 8.2** | Cài local trên Windows (xem bên dưới) |
| **Composer** | Đi kèm PHP hoặc `winget install Composer.Composer` |
| **Docker Desktop** | [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/) |

PHP extensions cần bật: `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`.

---

## Cài đặt (Docker MySQL + PHP local)

### 1. MySQL bằng Docker

```powershell
cd d:\Project\php8-hotel-landing
docker compose up -d
```

Lần đầu Docker tự import `database/schema.sql` và `database/seeders/seed.sql`.

| Thông số | Giá trị mặc định |
|----------|------------------|
| Host | `127.0.0.1` |
| Port | `3306` |
| Database | `shinning_hotel` |
| User | `shinning` |
| Password | `shinning` |
| Root password | `secret` |

Reset DB (xóa data, import lại):

```powershell
powershell -ExecutionPolicy Bypass -File scripts\db-reset.ps1
```

### 2. PHP 8 trên Windows (local)

```powershell
winget install PHP.PHP.8.3
winget install Composer.Composer
```

Mở **terminal mới**, kiểm tra:

```powershell
php -v
composer -V
```

Bật extension trong `php.ini` (đường dẫn: `php --ini`):

```ini
extension=pdo_mysql
extension=fileinfo
extension=mbstring
extension=openssl
```

### 3. App

```powershell
copy .env.example .env
composer install
php scripts\download_seed_images.php
php -S localhost:8000 -t public
```

Hoặc chạy một lệnh setup (Docker + composer + ảnh seed nếu đã có PHP):

```powershell
powershell -ExecutionPolicy Bypass -File scripts\setup.ps1
```

File `.env` mặc định đã trỏ Docker MySQL (`shinning` / `shinning`).

- **Website:** http://localhost:8000  
- **CMS:** http://localhost:8000/admin/login  

### Tài khoản mặc định

| Email | Password | Role |
|-------|----------|------|
| admin@shinning.com | password | super_admin |
| editor@shinning.com | password | editor |

## Cấu trúc chính

```
public/          # Document root
app/Core/        # Router, DB, Session, CSRF
app/Controllers/Web|Admin/
app/Models/
app/Views/web|admin/
config/
database/schema.sql
storage/uploads/
```

## Đa ngôn ngữ (EN / VI)

- URL: `http://localhost:8000/en/...` và `/vi/...` (trang gốc `/` redirect theo ngôn ngữ ưu tiên)
- UI labels: `lang/en.php`, `lang/vi.php` — helper `__('key')`
- Nội dung CMS: bảng `*_translations` — admin form tabs English / Tiếng Việt
- Chạy migration: `Get-Content database\migrations\012_translations.sql | docker exec -i shinning_mysql mysql -ushinning -pshinning shinning_hotel`

### Font tiếng Việt

Giao diện public/admin dùng **Be Vietnam Pro** (UI) và **Lora** (tiêu đề), hỗ trợ đầy đủ dấu tiếng Việt. Cấu hình trong `public/assets/css/theme-luxury.css` và Google Fonts trong `app/Views/web/layouts/main.php`.

### Seed / sửa nội dung tiếng Việt từ tiếng Anh

Nếu chữ tiếng Việt trong CMS hiển thị dạng `???` (lỗi encoding cũ), chạy script tái tạo bản dịch `vi` từ bản `en`:

```powershell
# Xem trước, không ghi DB
php scripts\seed_vi_from_en.php --dry-run

# Chỉ tạo bản vi còn thiếu
php scripts\seed_vi_from_en.php --only-missing

# Mặc định: tạo thiếu + sửa bản vi bị lỗi (??)
php scripts\seed_vi_from_en.php

# Ghi đè toàn bộ bản vi (cẩn thận nếu đã chỉnh tay)
php scripts\seed_vi_from_en.php --force

# Không gọi API dịch (chỉ từ điển / map có sẵn)
php scripts\seed_vi_from_en.php --no-api
```

Script cập nhật: `page`, `news`, `room`, `room_category`, `banner`, `menu_item` translations và SEO settings `seo_default_*_vi`.

**Checklist QA sau seed**

- [ ] `/vi/` — menu, hero banner, nút Đặt phòng hiển thị đúng dấu
- [ ] `/vi/rooms`, `/vi/rooms/{slug}` — tên phòng, mô tả
- [ ] `/vi/news`, `/vi/news/{slug}` — tiêu đề, tóm tắt
- [ ] `/vi/about`, `/vi/location`, `/vi/contact`, `/vi/book`
- [ ] Admin → Pages/News/Rooms/Banners/Menus — tab **Tiếng Việt** đã có nội dung, hiệu chỉnh tay nếu cần

## Tính năng

- Public: homepage, phòng, wizard đặt phòng, news, contact, SEO/Open Graph + hreflang
- CMS: Pages, News (TinyMCE), Banners, Media, Rooms, Bookings, Users, Settings, Menus
- Booking: availability, mã **SHN-000001**, email xác nhận (`MAIL_ENABLED=true`)
- Admin dashboard: thống kê bookings (pending/confirmed/revenue)
- Roles: `super_admin`, `admin`, `editor` · CSRF, session auth, PDO

## Tests

```powershell
php vendor\bin\phpunit
```

## Production

Deploy lên VPS qua **GitHub Actions + GHCR + Docker Compose** (không git pull trên server):

**[deploy/HUONG-DAN-TUNG-BUOC.md](deploy/HUONG-DAN-TUNG-BUOC.md)** — làm từng bước (VN, copy-paste).  
**[deploy/CHECKLIST.md](deploy/CHECKLIST.md)** · **[deploy/README.md](deploy/README.md)** — chi tiết kỹ thuật.

Trong `.env` trên VPS:

```env
APP_ENV=production
APP_DEBUG=false
MAIL_ENABLED=true
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

Log lỗi: `storage/logs/app.log` (tự rotate khi > 5MB).

## Roadmap

Xem [ROADMAP.md](ROADMAP.md) — Phase 1–6 đã hoàn thành.

## TinyMCE

Admin dùng TinyMCE 7 **self-hosted** tại `public/assets/vendor/tinymce/` — không cần API key Tiny Cloud. Cấu hình init trong `app/Views/admin/partials/tinymce.php`.

## License

Private client project.
