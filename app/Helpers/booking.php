<?php

declare(strict_types=1);

use App\Core\Session;

function booking_wizard(): array
{
    return Session::get('booking_wizard') ?? [];
}

function booking_wizard_set(array $data): void
{
    Session::set('booking_wizard', array_merge(booking_wizard(), $data));
}

function booking_wizard_clear(): void
{
    Session::forget('booking_wizard');
}

function booking_reference(int $id): string
{
    return 'SHN-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
}

function booking_validate_dates(string $checkIn, string $checkOut): ?string
{
    if ($checkIn === '' || $checkOut === '') {
        return 'Please select check-in and check-out dates.';
    }
    $in = strtotime($checkIn);
    $out = strtotime($checkOut);
    $today = strtotime(date('Y-m-d'));
    if ($in === false || $out === false) {
        return 'Invalid date format.';
    }
    if ($in < $today) {
        return 'Check-in cannot be in the past.';
    }
    if ($out <= $in) {
        return 'Check-out must be after check-in.';
    }
    return null;
}

function booking_nights(string $checkIn, string $checkOut): int
{
    return max(1, (int) ((strtotime($checkOut) - strtotime($checkIn)) / 86400));
}

function booking_format_dates(string $checkIn, string $checkOut): string
{
    return date('M j, Y', strtotime($checkIn)) . ' → ' . date('M j, Y', strtotime($checkOut));
}
