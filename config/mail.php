<?php

return [
    'enabled' => filter_var($_ENV['MAIL_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@shinning.com',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Shinning Hotel',
    'reply_to' => $_ENV['MAIL_REPLY_TO'] ?? ($_ENV['MAIL_FROM_ADDRESS'] ?? 'reservations@shinning.com'),
];
