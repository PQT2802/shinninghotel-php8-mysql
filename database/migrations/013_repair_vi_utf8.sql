-- Repair Vietnamese CMS strings corrupted by non-UTF-8 SQL import (e.g. PowerShell Get-Content)
-- Apply: docker cp this file into mysql container, then: source /tmp/013_repair_vi_utf8.sql
USE shinning_hotel;
SET NAMES utf8mb4;

-- Banner hero
UPDATE banner_translations SET
  title = 'Chào mừng đến Shinning',
  subtitle = 'Nơi mỗi kỳ nghỉ đều rực rỡ',
  button_text = 'Đặt phòng'
WHERE locale = 'vi' AND banner_id = (SELECT id FROM banners WHERE position = 'home_hero' ORDER BY sort_order LIMIT 1);

-- Main navigation (header menu_id = 1)
UPDATE menu_item_translations mit
JOIN menu_items mi ON mi.id = mit.menu_item_id
SET mit.title = CASE mi.title
  WHEN 'Home' THEN 'Trang chủ'
  WHEN 'Rooms' THEN 'Phòng'
  WHEN 'About' THEN 'Giới thiệu'
  WHEN 'News' THEN 'Tin tức'
  WHEN 'Location' THEN 'Vị trí'
  WHEN 'Contact' THEN 'Liên hệ'
  ELSE mit.title
END
WHERE mit.locale = 'vi' AND mi.menu_id = 1;

-- Footer navigation (menu_id = 2)
UPDATE menu_item_translations mit
JOIN menu_items mi ON mi.id = mit.menu_item_id
SET mit.title = CASE mi.title
  WHEN 'Rooms' THEN 'Phòng'
  WHEN 'Book Now' THEN 'Đặt phòng'
  WHEN 'About' THEN 'Giới thiệu'
  WHEN 'Contact' THEN 'Liên hệ'
  ELSE mit.title
END
WHERE mit.locale = 'vi' AND mi.menu_id = 2;

-- Pages
UPDATE page_translations pt
JOIN pages p ON p.id = pt.page_id
SET
  pt.title = CASE p.slug WHEN 'about-us' THEN 'Về chúng tôi' WHEN 'location' THEN 'Vị trí' ELSE pt.title END,
  pt.content = CASE p.slug
    WHEN 'about-us' THEN '<p>Chào mừng đến <strong>Shinning Hotel</strong> — nơi mỗi kỳ nghỉ đều rực rỡ. Chúng tôi kết hợp sự tinh tế vượt thời gian với tiện nghi hiện đại.</p><p>Đội ngũ tận tâm của chúng tôi mang đến trải nghiệm khó quên từ lúc nhận phòng đến khi tiễn khách.</p>'
    WHEN 'location' THEN '<p>Chúng tôi tọa lạc tại 123 Nguyễn Huệ, Quận 1. Cách các điểm tham quan, mua sắm và khu kinh doanh chỉ vài phút.</p><p>Dịch vụ đưa đón sân bay theo yêu cầu.</p>'
    ELSE pt.content END,
  pt.seo_title = CASE p.slug WHEN 'about-us' THEN 'Giới thiệu Shinning Hotel' WHEN 'location' THEN 'Vị trí & Chỉ đường' ELSE pt.seo_title END
WHERE pt.locale = 'vi';

-- News
UPDATE news_translations nt
JOIN news n ON n.id = nt.news_id
SET
  nt.title = CASE n.slug
    WHEN 'grand-opening-special' THEN 'Ưu đãi khai trương'
    WHEN 'seasonal-spa-packages' THEN 'Gói spa theo mùa'
    WHEN 'rooftop-dining-experience' THEN 'Ẩm thực sân thượng'
    WHEN 'weekend-staycation-offer' THEN 'Ưu đãi cuối tuần'
    WHEN 'corporate-retreat-packages' THEN 'Gói doanh nghiệp'
    ELSE nt.title END,
  nt.summary = CASE n.slug
    WHEN 'grand-opening-special' THEN 'Khai trương — giảm 20% cho kỳ nghỉ đầu tiên.'
    WHEN 'seasonal-spa-packages' THEN 'Tái tạo năng lượng với liệu trình wellness mới.'
    WHEN 'rooftop-dining-experience' THEN 'Thực đơn hoàng hôn và nhạc sống mỗi thứ Sáu.'
    WHEN 'weekend-staycation-offer' THEN 'Hai đêm từ $249 bao gồm bữa sáng.'
    WHEN 'corporate-retreat-packages' THEN 'Hội nghị và sự kiện nhóm tại Shinning.'
    ELSE nt.summary END
WHERE nt.locale = 'vi';

-- Rooms
UPDATE room_translations rt
JOIN rooms r ON r.id = rt.room_id
SET rt.name = CASE r.slug
  WHEN 'standard-king' THEN 'Phòng Standard King'
  WHEN 'deluxe-ocean-view' THEN 'Deluxe Hướng biển'
  WHEN 'presidential-suite' THEN 'Presidential Suite'
  WHEN 'standard-twin' THEN 'Phòng Standard Twin'
  WHEN 'deluxe-garden-view' THEN 'Deluxe Hướng vườn'
  WHEN 'family-connecting' THEN 'Suite Gia đình liền kề'
  WHEN 'executive-club-king' THEN 'Executive Club King'
  WHEN 'penthouse-sky' THEN 'Penthouse Sky Suite'
  ELSE rt.name END
WHERE rt.locale = 'vi';

-- Room categories
UPDATE room_category_translations ct
JOIN room_categories c ON c.id = ct.category_id
SET
  ct.name = CASE c.slug WHEN 'standard' THEN 'Standard' WHEN 'deluxe' THEN 'Deluxe' WHEN 'suite' THEN 'Suite' WHEN 'family' THEN 'Gia đình' WHEN 'executive' THEN 'Executive' ELSE ct.name END,
  ct.description = CASE c.slug
    WHEN 'standard' THEN 'Phòng tiện nghi cho khách thông thái'
    WHEN 'deluxe' THEN 'Phòng rộng với tiện ích cao cấp'
    WHEN 'suite' THEN 'Sang trọng tối đa và tầm nhìn panorama'
    WHEN 'family' THEN 'Không gian rộng cho gia đình và nhóm'
    WHEN 'executive' THEN 'Phòng dành cho khách công tác'
    ELSE ct.description END
WHERE ct.locale = 'vi';

INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, group_name) VALUES
('seo_default_title_vi', 'Shinning Hotel | Nơi mỗi kỳ nghỉ đều rực rỡ', 'text', 'seo'),
('seo_default_description_vi', 'Khách sạn sang trọng giữa lòng thành phố. Đặt phòng trực tiếp để nhận ưu đãi độc quyền.', 'text', 'seo');

UPDATE settings SET setting_value = 'Shinning Hotel | Nơi mỗi kỳ nghỉ đều rực rỡ' WHERE setting_key = 'seo_default_title_vi';
UPDATE settings SET setting_value = 'Khách sạn sang trọng giữa lòng thành phố. Đặt phòng trực tiếp để nhận ưu đãi độc quyền.' WHERE setting_key = 'seo_default_description_vi';
