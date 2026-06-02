-- Sync EN/VI CMS translations (rooms, news, pages, categories)
-- Apply via: scripts/repair-vi-encoding.ps1 or docker cp + source
USE shinning_hotel;
SET NAMES utf8mb4;

-- English: mirror canonical content from base tables
UPDATE room_translations rt
JOIN rooms r ON r.id = rt.room_id
SET rt.name = r.name, rt.description = r.description
WHERE rt.locale = 'en';

UPDATE news_translations nt
JOIN news n ON n.id = nt.news_id
SET
  nt.title = n.title,
  nt.summary = n.summary,
  nt.content = n.content,
  nt.seo_title = n.seo_title,
  nt.seo_description = n.seo_description
WHERE nt.locale = 'en';

UPDATE page_translations pt
JOIN pages p ON p.id = pt.page_id
SET
  pt.title = p.title,
  pt.content = p.content,
  pt.seo_title = p.seo_title,
  pt.seo_description = p.seo_description
WHERE pt.locale = 'en';

UPDATE room_category_translations ct
JOIN room_categories c ON c.id = ct.category_id
SET ct.name = c.name, ct.description = c.description
WHERE ct.locale = 'en';

-- Vietnamese room names (descriptions set in 014_room_rich_content.sql)
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
  ELSE rt.name
END
WHERE rt.locale = 'vi';

-- Vietnamese news: title, summary, and full body
UPDATE news_translations nt
JOIN news n ON n.id = nt.news_id
SET
  nt.title = CASE n.slug
    WHEN 'grand-opening-special' THEN 'Ưu đãi khai trương'
    WHEN 'seasonal-spa-packages' THEN 'Gói spa theo mùa'
    WHEN 'rooftop-dining-experience' THEN 'Ẩm thực sân thượng'
    WHEN 'weekend-staycation-offer' THEN 'Ưu đãi cuối tuần'
    WHEN 'corporate-retreat-packages' THEN 'Gói doanh nghiệp'
    ELSE nt.title
  END,
  nt.summary = CASE n.slug
    WHEN 'grand-opening-special' THEN 'Khai trương — giảm 20% cho kỳ nghỉ đầu tiên.'
    WHEN 'seasonal-spa-packages' THEN 'Tái tạo năng lượng với liệu trình wellness mới.'
    WHEN 'rooftop-dining-experience' THEN 'Thực đơn hoàng hôn và nhạc sống mỗi thứ Sáu.'
    WHEN 'weekend-staycation-offer' THEN 'Hai đêm từ $249 bao gồm bữa sáng.'
    WHEN 'corporate-retreat-packages' THEN 'Hội nghị và sự kiện nhóm tại Shinning.'
    ELSE nt.summary
  END,
  nt.content = CASE n.slug
    WHEN 'grand-opening-special' THEN '<p>Đặt phòng trước cuối tháng và nhận mức giá ưu đãi cho mọi hạng phòng — ưu đãi khai trương dành riêng cho khách đặt trực tiếp.</p>'
    WHEN 'seasonal-spa-packages' THEN '<p>Trải nghiệm liệu trình lấy cảm hứng từ truyền thống địa phương và tiêu chuẩn spa quốc tế — tái tạo năng lượng trong không gian thư giãn tuyệt đối.</p>'
    WHEN 'rooftop-dining-experience' THEN '<p>Tham gia bữa tối trên sân thượng với thực đơn degustation do đầu bếp thiết kế, kết hợp rượu vang cao cấp và nhạc sống mỗi thứ Sáu.</p>'
    WHEN 'weekend-staycation-offer' THEN '<p>Gói hai đêm cuối tuần từ $249 bao gồm bữa sáng buffet — lý tưởng cho kỳ nghỉ ngắn ngày giữa lòng thành phố.</p>'
    WHEN 'corporate-retreat-packages' THEN '<p>Gói hội nghị và team building linh hoạt với phòng họp, coffee break và hỗ trợ sự kiện chuyên nghiệp tại Shinning Hotel.</p>'
    ELSE nt.content
  END,
  nt.seo_title = CASE n.slug
    WHEN 'grand-opening-special' THEN 'Ưu đãi khai trương | Shinning Hotel'
    WHEN 'seasonal-spa-packages' THEN 'Gói spa theo mùa | Shinning Hotel'
    WHEN 'rooftop-dining-experience' THEN 'Ẩm thực sân thượng | Shinning Hotel'
    WHEN 'weekend-staycation-offer' THEN 'Ưu đãi cuối tuần | Shinning Hotel'
    WHEN 'corporate-retreat-packages' THEN 'Gói doanh nghiệp | Shinning Hotel'
    ELSE nt.seo_title
  END
WHERE nt.locale = 'vi';

-- Ensure missing translation rows exist
INSERT INTO room_translations (room_id, locale, name, description)
SELECT r.id, 'en', r.name, r.description FROM rooms r
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description);

INSERT INTO room_translations (room_id, locale, name, description)
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
    ELSE r.name
  END,
  r.description
FROM rooms r
ON DUPLICATE KEY UPDATE name = VALUES(name);
