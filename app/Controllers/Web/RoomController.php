<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Setting;

class RoomController extends Controller
{
    public function index(): void
    {
        $checkIn = $_GET['check_in'] ?? '';
        $checkOut = $_GET['check_out'] ?? '';
        $categoryId = !empty($_GET['category']) ? (int) $_GET['category'] : null;
        $guestsCount = max(1, (int) ($_GET['guests'] ?? 2));

        $filters = ['guests_count' => $guestsCount];
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        $datesValid = $checkIn && $checkOut && !booking_validate_dates($checkIn, $checkOut);
        if ($datesValid) {
            $filters['check_in'] = $checkIn;
            $filters['check_out'] = $checkOut;
        }

        $rooms = $datesValid ? Room::published($filters) : Room::published(array_filter([
            'category_id' => $categoryId,
            'guests_count' => $guestsCount,
        ]));

        $nights = $datesValid ? booking_nights($checkIn, $checkOut) : 0;
        foreach ($rooms as &$room) {
            $room['nights'] = $nights;
            $room['stay_total'] = $nights > 0 ? $nights * (float) $room['price_per_night'] : null;
        }
        unset($room);

        $settings = Setting::allKeyed();
        $this->view('web/rooms/index', [
            'title' => 'Rooms & Suites',
            'metaDescription' => 'Browse luxury rooms and suites at ' . brand_name() . ' Hotel. Check availability and book direct for the best rates.',
            'breadcrumbs' => [['label' => 'Rooms & Suites', 'url' => url('/rooms')]],
            'rooms' => $rooms,
            'categories' => RoomCategory::allActive(),
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'categoryId' => $categoryId,
            'guestsCount' => $guestsCount,
            'datesValid' => $datesValid,
            'nights' => $nights,
            'settings' => $settings,
        ]);
    }

    public function show(string $slug): void
    {
        $room = Room::findPublishedBySlug($slug);
        if (!$room) {
            http_response_code(404);
            $this->view('web/errors/404', ['title' => 'Room Not Found', 'robots' => 'noindex, nofollow']);
            return;
        }
        $settings = Setting::allKeyed();
        $this->view('web/rooms/show', [
            'title' => $room['name'],
            'metaDescription' => mb_substr(strip_tags($room['description'] ?? ''), 0, 160),
            'ogImagePath' => $room['image_path'] ?? null,
            'breadcrumbs' => [
                ['label' => __('rooms.title'), 'url' => url('/rooms')],
                ['label' => $room['name'], 'url' => url('/rooms/' . $room['slug'])],
            ],
            'room' => $room,
            'settings' => $settings,
        ]);
    }
}
