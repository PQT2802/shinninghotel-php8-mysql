<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\Room;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $bookingStats = Booking::dashboardStats();
        $this->view('admin/dashboard/index', [
            'title' => 'Dashboard',
            'stats' => [
                'pages' => Page::count(),
                'news' => News::count(),
                'media' => Media::count(),
                'rooms' => count(Room::all()),
                'pendingBookings' => $bookingStats['pending'],
            ],
            'bookingStats' => $bookingStats,
            'recentMessages' => ContactMessage::recent(5),
            'recentBookings' => Booking::allRecent(8),
            'unreadMessages' => ContactMessage::countUnread(),
            'userCount' => User::count(),
        ]);
    }
}
