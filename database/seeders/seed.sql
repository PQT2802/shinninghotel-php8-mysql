-- Seed data for Shinning Hotel
USE shinning_hotel;

-- Admin: admin@shinning.com / password
INSERT INTO users (name, email, password_hash, role, status) VALUES
('Super Admin', 'admin@shinning.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active'),
('Editor', 'editor@shinning.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor', 'active');

INSERT INTO settings (setting_key, setting_value, setting_type, group_name) VALUES
('site_name', 'Shinning Hotel', 'text', 'general'),
('contact_email', 'reservations@shinning.com', 'text', 'contact'),
('contact_phone', '+84 28 1234 5678', 'text', 'contact'),
('address', '123 Nguyen Hue Boulevard, District 1, Ho Chi Minh City, Vietnam', 'text', 'contact'),
('facebook_url', 'https://facebook.com/shinninghotel', 'text', 'social'),
('instagram_url', 'https://instagram.com/shinninghotel', 'text', 'social'),
('seo_default_title', 'Shinning Hotel | Where Every Stay Shines', 'text', 'seo'),
('seo_default_description', 'Luxury hotel in the heart of the city. Book your stay with exclusive offers and world-class service.', 'text', 'seo');

INSERT INTO menus (name, location) VALUES
('Main Navigation', 'header'),
('Footer Links', 'footer');

INSERT INTO menu_items (menu_id, title, url, sort_order, is_active) VALUES
(1, 'Home', '/', 1, 1),
(1, 'Rooms', '/rooms', 2, 1),
(1, 'About', '/about', 3, 1),
(1, 'News', '/news', 4, 1),
(1, 'Location', '/location', 5, 1),
(1, 'Contact', '/contact', 6, 1);

INSERT INTO menu_items (menu_id, title, url, sort_order, is_active) VALUES
(2, 'Rooms', '/rooms', 1, 1),
(2, 'Book Now', '/book', 2, 1),
(2, 'About', '/about', 3, 1),
(2, 'Contact', '/contact', 4, 1);

INSERT INTO pages (title, slug, content, status, seo_title, published_at) VALUES
('About Us', 'about-us', '<p>Welcome to <strong>Shinning Hotel</strong> — where every stay shines. We blend timeless elegance with modern comfort in the heart of the city.</p><p>Our dedicated team ensures an unforgettable experience from check-in to farewell. Discover refined dining, restorative spa rituals, and suites designed for discerning travellers.</p><p><img src="/uploads/seed/about.jpg" alt="Shinning Hotel lobby" style="max-width:100%;border-radius:4px;margin:1.5rem 0;"></p>', 'published', 'About Shinning Hotel', NOW()),
('Location', 'location', '<p>Find us at 123 Nguyen Hue Boulevard, District 1. Minutes from landmarks, shopping, and the business district.</p><p>Airport transfers available upon request. Valet parking and concierge services daily.</p>', 'published', 'Location & Directions', NOW());

INSERT INTO room_categories (name, slug, description, sort_order) VALUES
('Standard', 'standard', 'Comfortable rooms for smart travelers', 1),
('Deluxe', 'deluxe', 'Spacious rooms with premium amenities', 2),
('Suite', 'suite', 'Ultimate luxury and panoramic views', 3),
('Family', 'family', 'Generous space for families and groups', 4),
('Executive', 'executive', 'Business-ready rooms with lounge access', 5);

INSERT INTO rooms (category_id, name, slug, description, amenities, price_per_night, max_guests, image_path, status, is_featured, sort_order) VALUES
(1, 'Shinning Standard King', 'standard-king', '<p class="room-lead">A refined <strong>25&nbsp;m²</strong> urban retreat with a king-size bed, sweeping city views, and a rejuvenating rain shower — crafted for guests who value calm, comfort, and a central address.</p><h3>Room highlights</h3><ul class="room-feature-list"><li>Floor-to-ceiling windows with skyline views</li><li>Premium king mattress with signature linens</li><li>Spa-inspired rain shower &amp; luxury toiletries</li><li>High-speed WiFi &amp; smart TV entertainment</li></ul><h3>Perfect for</h3><p>Couples, solo travellers, and short business stays who want boutique service without compromise.</p>', '["WiFi","TV","Minibar","Air conditioning"]', 89.00, 2, 'seed/room-standard.jpg', 'published', 0, 1),
(2, 'Deluxe Ocean View', 'deluxe-ocean-view', '<p>40 m² with floor-to-ceiling windows and lounge area.</p>', '["WiFi","TV","Minibar","Bathtub","Room service"]', 149.00, 2, 'seed/room-deluxe.jpg', 'published', 1, 2),
(3, 'Presidential Suite', 'presidential-suite', '<p>120 m² suite with private terrace, butler service, and dining room.</p>', '["WiFi","TV","Minibar","Jacuzzi","Butler service","Private terrace"]', 399.00, 4, 'seed/room-suite.jpg', 'published', 1, 3),
(1, 'Standard Twin Room', 'standard-twin', '<p>28 m² with two single beds, ideal for friends or colleagues.</p>', '["WiFi","TV","Minibar","Air conditioning","Safe"]', 79.00, 2, 'seed/room-twin.jpg', 'published', 0, 4),
(2, 'Deluxe Garden View', 'deluxe-garden-view', '<p>38 m² overlooking our courtyard garden with soaking tub.</p>', '["WiFi","TV","Minibar","Bathtub","Coffee maker","Balcony"]', 139.00, 2, 'seed/room-deluxe-2.jpg', 'published', 1, 5),
(4, 'Family Connecting Suite', 'family-connecting', '<p>65 m² connecting rooms with lounge — perfect for families.</p>', '["WiFi","TV","Minibar","Air conditioning","Room service","Coffee maker"]', 219.00, 5, 'seed/room-family.jpg', 'published', 1, 6),
(5, 'Executive Club King', 'executive-club-king', '<p>42 m² with club lounge access, workspace, and express check-in.</p>', '["WiFi","TV","Minibar","Coffee maker","Room service","Safe"]', 189.00, 2, 'seed/room-executive.jpg', 'published', 1, 7),
(3, 'Penthouse Sky Suite', 'penthouse-sky', '<p>150 m² top-floor retreat with panoramic skyline views.</p>', '["WiFi","TV","Minibar","Jacuzzi","Butler service","Private terrace","Ocean view"]', 599.00, 4, 'seed/room-penthouse.jpg', 'published', 1, 8);

INSERT INTO room_images (room_id, file_path, sort_order) VALUES
(1, 'seed/room-standard.jpg', 1),
(1, 'seed/room-standard-2.jpg', 2),
(2, 'seed/room-deluxe.jpg', 1),
(2, 'seed/room-deluxe-2.jpg', 2),
(3, 'seed/room-suite.jpg', 1),
(3, 'seed/room-suite-2.jpg', 2),
(4, 'seed/room-twin.jpg', 1),
(5, 'seed/room-deluxe-2.jpg', 1),
(5, 'seed/room-deluxe.jpg', 2),
(6, 'seed/room-family.jpg', 1),
(6, 'seed/room-family-2.jpg', 2),
(7, 'seed/room-executive.jpg', 1),
(7, 'seed/room-executive-2.jpg', 2),
(8, 'seed/room-penthouse.jpg', 1),
(8, 'seed/room-penthouse-2.jpg', 2);

INSERT INTO banners (title, subtitle, image_path, button_text, button_url, position, sort_order, is_active) VALUES
('Welcome to Shinning', 'Where Every Stay Shines', 'seed/hero.jpg', 'Book Now', '/book', 'home_hero', 1, 1);

INSERT INTO news (title, slug, summary, content, thumbnail_path, status, published_at) VALUES
('Grand Opening Special', 'grand-opening-special', 'Celebrate our opening with 20% off your first stay.', '<p>Book before the end of the month and enjoy exclusive rates on all room categories.</p>', 'seed/news-1.jpg', 'published', NOW()),
('Seasonal Spa Packages', 'seasonal-spa-packages', 'Rejuvenate with our new wellness offerings.', '<p>Indulge in treatments inspired by local traditions and global luxury standards.</p>', 'seed/news-2.jpg', 'published', NOW()),
('Rooftop Dining Experience', 'rooftop-dining-experience', 'Sunset menus and live music every Friday.', '<p>Join us on the terrace for chef-curated tasting menus paired with fine wines.</p>', 'seed/news-3.jpg', 'published', DATE_SUB(NOW(), INTERVAL 7 DAY)),
('Weekend Staycation Offer', 'weekend-staycation-offer', 'Two nights from $249 including breakfast.', '<p>Escape the city rush with our weekend package — late checkout and spa credit included.</p>', 'seed/news-4.jpg', 'published', DATE_SUB(NOW(), INTERVAL 14 DAY)),
('Corporate Retreat Packages', 'corporate-retreat-packages', 'Tailored meetings and team events at Shinning.', '<p>Private boardrooms, catering, and group rates for teams of 10 or more.</p>', 'seed/news-5.jpg', 'published', DATE_SUB(NOW(), INTERVAL 21 DAY));
