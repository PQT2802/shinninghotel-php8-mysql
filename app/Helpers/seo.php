<?php

declare(strict_types=1);

function seo_absolute_url(string $path = ''): string
{
    return url($path);
}

function seo_og_image(?string $relativePath, array $settings = []): string
{
    if ($relativePath) {
        return upload_url($relativePath);
    }
    if (!empty($settings['logo_path'])) {
        return upload_url($settings['logo_path']);
    }
    return seo_absolute_url('/assets/images/og-default.svg');
}

function seo_canonical_path(): string
{
    if (function_exists('locale_url')) {
        return locale_url(current_localized_path());
    }
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    return seo_absolute_url($path);
}

function seo_json_ld_hotel(array $settings): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Hotel',
        'name' => $settings['site_name'] ?? brand_name() . ' Hotel',
        'description' => $settings['seo_default_description'] ?? brand_slogan(),
        'url' => seo_absolute_url('/'),
        'telephone' => $settings['contact_phone'] ?? null,
        'email' => $settings['contact_email'] ?? null,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $settings['address'] ?? '',
        ],
    ];
    return json_encode(array_filter($data, fn ($v) => $v !== null && $v !== ''), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function seo_json_ld_room(array $room, array $settings = []): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'HotelRoom',
        'name' => $room['name'] ?? '',
        'description' => mb_substr(strip_tags($room['description'] ?? ''), 0, 300),
        'occupancy' => ['@type' => 'QuantitativeValue', 'maxValue' => (int) ($room['max_guests'] ?? 2)],
        'offers' => [
            '@type' => 'Offer',
            'price' => (float) ($room['price_per_night'] ?? 0),
            'priceCurrency' => 'USD',
        ],
    ];
    if (!empty($room['image_path'])) {
        $data['image'] = upload_url($room['image_path']);
    }
    return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function seo_json_ld_article(array $article, array $settings = []): string
{
    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $article['title'] ?? '',
        'description' => $article['summary'] ?? '',
        'datePublished' => $article['published_at'] ?? null,
        'author' => ['@type' => 'Organization', 'name' => $settings['site_name'] ?? brand_name() . ' Hotel'],
    ];
    if (!empty($article['thumbnail_path'])) {
        $data['image'] = upload_url($article['thumbnail_path']);
    }
    return json_encode(array_filter($data, fn ($v) => $v !== null && $v !== ''), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
