# Shinning Hotel — Kế hoạch phát triển

**Thương hiệu:** Shinning Hotel  
**Slogan:** *Where Every Stay Shines* — Nơi mỗi kỳ nghỉ đều rực rỡ

Dự án PHP 8 MVC monolith (WebUI + CMS Admin), không API-first, không SPA.

---

## Phase 1 — Nền tảng (hiện tại)

- [x] Cấu trúc thư mục theo spec MVC
- [x] Core: Router, Controller, Model, View, Database, Session, CSRF
- [x] Composer, `.env.example`, config
- [x] Migrations: users, pages, news, banners, media, menus, settings, contact
- [x] Migrations khách sạn: `room_categories`, `rooms`, `bookings`
- [x] Admin login/logout, middleware auth + role
- [x] Layout admin (sidebar) + public (header/footer)
- [x] TinyMCE trên form Pages/News (CDN)
- [x] Seed: admin user, settings Shinning, menu mặc định

**Chạy thử:** `docker compose up -d` → `composer install` → `php -S localhost:8000 -t public`  
(MySQL chỉ trong Docker; PHP chạy local — xem README.md)

---

## Phase 2 — CMS nội dung ✅

- [x] CRUD Pages — validation, slug unique, search/filter, pagination
- [x] CRUD News + thumbnail — upload, xóa ảnh khi delete
- [x] Banners — CRUD đầy đủ, bắt buộc ảnh khi tạo
- [x] Media — upload, copy URL, xóa file vật lý, pagination
- [x] Menus — editor header/footer, gắn page hoặc URL tùy chỉnh
- [x] Settings — logo, favicon, contact, social, SEO
- [x] Users — CRUD, phân quyền role, không xóa chính mình
- [x] Contact messages — inbox, filter, xem chi tiết, đọc/chưa đọc, xóa

---

## Phase 3 — Phòng & danh mục (Admin) ✅

- [x] CRUD `room_categories` — slug, sort, active, không xóa khi còn phòng
- [x] CRUD `rooms` — validation, filter, pagination, xóa, featured
- [x] Ảnh chính + gallery từ Media library (hoặc upload mới)
- [x] Tiện nghi (checkbox + custom), TinyMCE mô tả
- [x] Publish / unpublish nhanh từ danh sách
- [x] Public: gallery + amenities trên trang chi tiết phòng

---

## Phase 4 — Đặt phòng (Public + Admin) ✅

- [x] `/rooms` — filter category, ngày, số khách; chỉ hiện phòng trống khi có ngày hợp lệ
- [x] Availability — loại trừ `bookings` pending/confirmed (overlap ngày)
- [x] Wizard `/book` → Dates → Room → Guest → Review → Complete (mã **SHN-000001**)
- [x] Admin: danh sách bookings (filter status), chi tiết, đổi status
- [x] Email xác nhận khi đặt phòng (Phase 6)

---

## Phase 5 — Trang public hoàn chỉnh ✅

- [x] Homepage: hero + booking bar, intro, perks, featured rooms, news, CTA book
- [x] `/rooms` + `/rooms/{slug}` + search availability + gallery lightbox
- [x] `/book` wizard (Phase 4)
- [x] `/about`, `/location`, `/contact` — breadcrumbs, layout polish
- [x] `/news`, `/news/{slug}` — article layout, JSON-LD
- [x] SEO: canonical, Open Graph, Twitter cards, JSON-LD Hotel/Room/Article (`app/Helpers/seo.php`)
- [x] Responsive: mobile nav, sticky header, luxury typography & CTA strips

---

## Phase 6 — Polish & vận hành ✅

- [x] Seed ảnh phòng/tin tức — `php scripts/download_seed_images.php` (public + storage)
- [x] Validation & flash — contact form Validator, partial `flash.php` trên booking
- [x] Dashboard thống kê bookings (total, status, revenue, tháng này)
- [x] Email xác nhận đặt phòng — `MailService`, `MAIL_ENABLED` trong `.env`
- [x] PHPUnit smoke tests — `vendor/bin/phpunit` (helpers + Validator)
- [x] Production — `Logger` rotate `storage/logs/app.log` (5MB, giữ 5 file)

---

## Vai trò CMS

| Role | Quyền |
|------|--------|
| `super_admin` | Toàn quyền + users |
| `admin` | Nội dung, media, settings, bookings |
| `editor` | Pages, news, banners, media (không users/settings) |

---

## Công nghệ

- PHP ≥ 8.2, PDO MySQL/MariaDB
- Session auth, CSRF, `password_hash`
- TinyMCE 7 (admin forms)
- CSS thuần (web.css / admin.css)
