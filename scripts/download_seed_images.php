<?php

/**
 * Download royalty-free placeholder images for seed data.
 * Copies to public/uploads/seed and storage/uploads/seed (used by DB seed paths).
 *
 * Run: php scripts/download_seed_images.php
 */

declare(strict_types=1);

$baseDir = dirname(__DIR__);
$publicSeed = $baseDir . '/public/uploads/seed';
$storageSeed = $baseDir . '/storage/uploads/seed';

foreach ([$publicSeed, $storageSeed] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$images = [
    'hero.jpg' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&q=80',
    'room-standard.jpg' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80',
    'room-deluxe.jpg' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80',
    'room-suite.jpg' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&q=80',
    'room-standard-2.jpg' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&q=80',
    'room-deluxe-2.jpg' => 'https://images.unsplash.com/photo-1560185891-a110d9665171?w=800&q=80',
    'room-suite-2.jpg' => 'https://images.unsplash.com/photo-1618773928123-c1d922f6d56d?w=800&q=80',
    'room-family.jpg' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&q=80',
    'room-family-2.jpg' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
    'room-executive.jpg' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=800&q=80',
    'room-executive-2.jpg' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&q=80',
    'room-twin.jpg' => 'https://images.unsplash.com/photo-1582719478241-831a38dd7f1e?w=800&q=80',
    'room-penthouse.jpg' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80',
    'room-penthouse-2.jpg' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80',
    'exp-dining.jpg' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80',
    'exp-spa.jpg' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=800&q=80',
    'exp-wellness.jpg' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&q=80',
    'news-1.jpg' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80',
    'news-2.jpg' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=600&q=80',
    'news-3.jpg' => 'https://images.unsplash.com/photo-1551884170-09fb70a3a2ed?w=600&q=80',
    'news-4.jpg' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80',
    'news-5.jpg' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600&q=80',
    'contact.jpg' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80',
    'about.jpg' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1200&q=80',
    'avatar-1.jpg' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80',
    'avatar-2.jpg' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=80',
    'avatar-3.jpg' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&q=80',
];

$ok = 0;
$failed = 0;

foreach ($images as $filename => $url) {
    $destPublic = $publicSeed . '/' . $filename;
    $destStorage = $storageSeed . '/' . $filename;

    if (file_exists($destPublic) && file_exists($destStorage)) {
        echo "Skip (exists): {$filename}\n";
        $ok++;
        continue;
    }

    echo "Downloading {$filename}...\n";
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 45,
            'user_agent' => 'ShinningHotel-Seed/1.0',
            'follow_location' => 1,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false || strlen($data) < 1000) {
        echo "  FAILED: {$url}\n";
        $failed++;
        continue;
    }

    file_put_contents($destPublic, $data);
    file_put_contents($destStorage, $data);
    echo '  OK (' . number_format(strlen($data)) . " bytes) → public + storage\n";
    $ok++;
}

echo "\nDone. {$ok} image(s) ready under uploads/seed/\n";
echo "Paths in DB: seed/hero.jpg, seed/room-*.jpg, seed/exp-*.jpg, etc.\n";
if ($failed > 0) {
    echo "Warning: {$failed} download(s) failed. Re-run or add images manually.\n";
    exit(1);
}
