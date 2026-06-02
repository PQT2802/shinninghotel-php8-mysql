-- Multilingual content (EN/VI)
USE shinning_hotel;

CREATE TABLE IF NOT EXISTS page_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id BIGINT UNSIGNED NOT NULL,
    locale ENUM('en', 'vi') NOT NULL,
    title VARCHAR(190) NOT NULL,
    content MEDIUMTEXT NULL,
    seo_title VARCHAR(190) NULL,
    seo_description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_page_locale (page_id, locale),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS news_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    news_id BIGINT UNSIGNED NOT NULL,
    locale ENUM('en', 'vi') NOT NULL,
    title VARCHAR(190) NOT NULL,
    summary TEXT NULL,
    content MEDIUMTEXT NULL,
    seo_title VARCHAR(190) NULL,
    seo_description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_news_locale (news_id, locale),
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS room_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    locale ENUM('en', 'vi') NOT NULL,
    name VARCHAR(190) NOT NULL,
    description MEDIUMTEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_room_locale (room_id, locale),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS room_category_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    locale ENUM('en', 'vi') NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_cat_locale (category_id, locale),
    FOREIGN KEY (category_id) REFERENCES room_categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS banner_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    banner_id BIGINT UNSIGNED NOT NULL,
    locale ENUM('en', 'vi') NOT NULL,
    title VARCHAR(190) NOT NULL,
    subtitle VARCHAR(255) NULL,
    button_text VARCHAR(100) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_banner_locale (banner_id, locale),
    FOREIGN KEY (banner_id) REFERENCES banners(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS menu_item_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_item_id BIGINT UNSIGNED NOT NULL,
    locale ENUM('en', 'vi') NOT NULL,
    title VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_menu_item_locale (menu_item_id, locale),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Run once; ignore error if column already exists
ALTER TABLE bookings ADD COLUMN locale VARCHAR(5) NOT NULL DEFAULT 'en' AFTER notes;

-- Migrate existing EN content
INSERT IGNORE INTO page_translations (page_id, locale, title, content, seo_title, seo_description)
SELECT id, 'en', title, content, seo_title, seo_description FROM pages;

INSERT IGNORE INTO news_translations (news_id, locale, title, summary, content, seo_title, seo_description)
SELECT id, 'en', title, summary, content, seo_title, seo_description FROM news;

INSERT IGNORE INTO room_translations (room_id, locale, name, description)
SELECT id, 'en', name, description FROM rooms;

INSERT IGNORE INTO room_category_translations (category_id, locale, name, description)
SELECT id, 'en', name, description FROM room_categories;

INSERT IGNORE INTO banner_translations (banner_id, locale, title, subtitle, button_text)
SELECT id, 'en', title, subtitle, button_text FROM banners;

INSERT IGNORE INTO menu_item_translations (menu_item_id, locale, title)
SELECT id, 'en', title FROM menu_items;

-- Vietnamese pages
INSERT IGNORE INTO page_translations (page_id, locale, title, content, seo_title, seo_description)
SELECT p.id, 'vi',
    CASE p.slug WHEN 'about-us' THEN 'Về chúng tôi' WHEN 'location' THEN 'Vị trí' ELSE p.title END,
    CASE p.slug
        WHEN 'about-us' THEN '<p>Chào mừng đến <strong>Shinning Hotel</strong> — nơi mỗi kỳ nghỉ đều rực rỡ. Chúng tôi kết hợp sự tinh tế vượt thời gian với tiện nghi hiện đại.</p><p>Đội ngũ tận tâm của chúng tôi mang đến trải nghiệm khó quên từ lúc nhận phòng đến khi tiễn khách.</p>'
        WHEN 'location' THEN '<p>Chúng tôi tọa lạc tại 123 Nguyễn Huệ, Quận 1. Cách các điểm tham quan, mua sắm và khu kinh doanh chỉ vài phút.</p><p>Dịch vụ đưa đón sân bay theo yêu cầu.</p>'
        ELSE p.content END,
    CASE p.slug WHEN 'about-us' THEN 'Giới thiệu Shinning Hotel' WHEN 'location' THEN 'Vị trí & Chỉ đường' ELSE p.seo_title END,
    p.seo_description
FROM pages p;

-- Vietnamese news
INSERT IGNORE INTO news_translations (news_id, locale, title, summary, content, seo_title, seo_description)
SELECT n.id, 'vi',
    CASE n.slug
        WHEN 'grand-opening-special' THEN 'Ưu đãi khai trương'
        WHEN 'seasonal-spa-packages' THEN 'Gói spa theo mùa'
        WHEN 'rooftop-dining-experience' THEN 'Ẩm thực sân thượng'
        WHEN 'weekend-staycation-offer' THEN 'Ưu đãi cuối tuần'
        WHEN 'corporate-retreat-packages' THEN 'Gói doanh nghiệp'
        ELSE n.title END,
    CASE n.slug
        WHEN 'grand-opening-special' THEN 'Khai trương — giảm 20% cho kỳ nghỉ đầu tiên.'
        WHEN 'seasonal-spa-packages' THEN 'Tái tạo năng lượng với liệu trình wellness mới.'
        WHEN 'rooftop-dining-experience' THEN 'Thực đơn hoàng hôn và nhạc sống mỗi thứ Sáu.'
        WHEN 'weekend-staycation-offer' THEN 'Hai đêm từ $249 bao gồm bữa sáng.'
        WHEN 'corporate-retreat-packages' THEN 'Hội nghị và sự kiện nhóm tại Shinning.'
        ELSE n.summary END,
    n.content, n.seo_title, n.seo_description
FROM news n;

-- Vietnamese rooms
INSERT IGNORE INTO room_translations (room_id, locale, name, description)
SELECT r.id, 'vi',
    CASE r.slug
        WHEN 'standard-king' THEN 'Phòng Standard King'
        WHEN 'deluxe-ocean-view' THEN 'Deluxe Hướng biển'
        WHEN 'presidential-suite' THEN 'Presidential Suite'
        WHEN 'standard-twin' THEN 'Phòng Standard Twin'
        WHEN 'deluxe-garden-view' THEN 'Deluxe Hướng vườn'
        WHEN 'family-connecting' THEN 'Suite Gia đình liền kề'
        WHEN 'executive-club-king' THEN 'Executive Club King'
        WHEN 'penthouse-sky' THEN 'Penthouse Sky Suite'
        ELSE r.name END,
    r.description
FROM rooms r;

INSERT IGNORE INTO room_category_translations (category_id, locale, name, description)
SELECT c.id, 'vi',
    CASE c.slug WHEN 'standard' THEN 'Standard' WHEN 'deluxe' THEN 'Deluxe' WHEN 'suite' THEN 'Suite' WHEN 'family' THEN 'Gia đình' WHEN 'executive' THEN 'Executive' ELSE c.name END,
    CASE c.slug
        WHEN 'standard' THEN 'Phòng tiện nghi cho khách thông thái'
        WHEN 'deluxe' THEN 'Phòng rộng với tiện ích cao cấp'
        WHEN 'suite' THEN 'Sang trọng tối đa và tầm nhìn panorama'
        WHEN 'family' THEN 'Không gian rộng cho gia đình và nhóm'
        WHEN 'executive' THEN 'Phòng dành cho khách công tác'
        ELSE c.description END
FROM room_categories c;

INSERT IGNORE INTO banner_translations (banner_id, locale, title, subtitle, button_text)
SELECT b.id, 'vi', 'Chào mừng đến Shinning', 'Nơi mỗi kỳ nghỉ đều rực rỡ', 'Đặt phòng'
FROM banners b WHERE b.position = 'home_hero' LIMIT 1;

INSERT IGNORE INTO menu_item_translations (menu_item_id, locale, title)
SELECT mi.id, 'vi',
    CASE mi.title
        WHEN 'Home' THEN 'Trang chủ'
        WHEN 'Rooms' THEN 'Phòng'
        WHEN 'About' THEN 'Giới thiệu'
        WHEN 'News' THEN 'Tin tức'
        WHEN 'Location' THEN 'Vị trí'
        WHEN 'Contact' THEN 'Liên hệ'
        WHEN 'Book Now' THEN 'Đặt phòng'
        ELSE mi.title END
FROM menu_items mi;

INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, group_name) VALUES
('seo_default_description_vi', 'Khách sạn sang trọng giữa lòng thành phố. Đặt phòng trực tiếp để nhận ưu đãi độc quyền.', 'text', 'seo'),
('seo_default_title_vi', 'Shinning Hotel | Nơi mỗi kỳ nghỉ đều rực rỡ', 'text', 'seo');
