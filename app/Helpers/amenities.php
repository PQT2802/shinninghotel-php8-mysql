<?php

declare(strict_types=1);

function default_amenity_options(): array
{
    return [
        'WiFi',
        'TV',
        'Minibar',
        'Air conditioning',
        'Room service',
        'Bathtub',
        'Rain shower',
        'Safe',
        'Coffee maker',
        'Balcony',
        'Ocean view',
        'Butler service',
        'Jacuzzi',
        'Private terrace',
    ];
}

/** Font Awesome 6 icon class per amenity label (case-insensitive match). */
function amenity_icon_map(): array
{
    return [
        'wifi' => 'fa-wifi',
        'tv' => 'fa-tv',
        'minibar' => 'fa-wine-glass',
        'air conditioning' => 'fa-snowflake',
        'room service' => 'fa-bell-concierge',
        'bathtub' => 'fa-bath',
        'rain shower' => 'fa-shower',
        'safe' => 'fa-vault',
        'coffee maker' => 'fa-mug-hot',
        'balcony' => 'fa-door-open',
        'ocean view' => 'fa-water',
        'butler service' => 'fa-user-tie',
        'butler' => 'fa-user-tie',
        'jacuzzi' => 'fa-hot-tub',
        'private terrace' => 'fa-umbrella-beach',
    ];
}

function amenity_icon(string $amenity): string
{
    $key = strtolower(trim($amenity));
    $map = amenity_icon_map();
    if (isset($map[$key])) {
        return $map[$key];
    }
    foreach ($map as $label => $icon) {
        if (str_contains($key, $label) || str_contains($label, $key)) {
            return $icon;
        }
    }
    return 'fa-check';
}

function amenities_to_json(array $selected, string $custom = ''): string
{
    $list = array_values(array_unique(array_filter(array_map('trim', $selected))));
    foreach (preg_split('/[\r\n,]+/', $custom) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $list[] = $line;
        }
    }
    return json_encode($list, JSON_UNESCAPED_UNICODE);
}

function amenities_from_json(?string $json): array
{
    if ($json === null || $json === '') {
        return [];
    }
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}
