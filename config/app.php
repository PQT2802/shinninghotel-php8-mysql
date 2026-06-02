<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Shinning Hotel',
    'env' => $_ENV['APP_ENV'] ?? 'local',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'),
    'brand' => $_ENV['HOTEL_BRAND'] ?? 'Shinning',
    'slogan' => $_ENV['HOTEL_SLOGAN'] ?? 'Where Every Stay Shines',
    'upload_max_mb' => (int) ($_ENV['UPLOAD_MAX_SIZE_MB'] ?? 5),
    'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? '_csrf_token',
    'session_name' => $_ENV['SESSION_NAME'] ?? 'shinning_hotel_session',
];
