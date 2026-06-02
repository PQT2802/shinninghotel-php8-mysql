-- Run if DB already exists: mysql -u shinning -pshinning shinning_hotel < database/migrations/011_room_images.sql
USE shinning_hotel;

CREATE TABLE IF NOT EXISTS room_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    media_id BIGINT UNSIGNED NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_room_images_room_id (room_id)
);
