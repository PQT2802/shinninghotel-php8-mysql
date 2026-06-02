<?php

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s', $_ENV['DB_HOST'], $_ENV['DB_PORT'] ?? 3306, $_ENV['DB_DATABASE']),
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD']
);
$rooms = (int) $pdo->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
$users = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
echo "DB OK — rooms: {$rooms}, users: {$users}\n";
