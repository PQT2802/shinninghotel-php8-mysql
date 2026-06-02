<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(): void
    {
        $this->requirePermission('bookings.manage');
        $status = $_GET['status'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $pager = Booking::paginate($page, 20, $status ?: null);

        $this->view('admin/bookings/index', [
            'title' => 'Bookings',
            'bookings' => $pager['data'],
            'pager' => $pager,
            'statusFilter' => $status,
            'stats' => [
                'pending' => Booking::countByStatus('pending'),
                'confirmed' => Booking::countByStatus('confirmed'),
                'cancelled' => Booking::countByStatus('cancelled'),
            ],
        ]);
    }

    public function show(int $id): void
    {
        $this->requirePermission('bookings.manage');
        $booking = Booking::find($id);
        if (!$booking) {
            Session::flash('error', 'Booking not found.');
            $this->redirect(url('/admin/bookings'));
        }

        $this->view('admin/bookings/show', [
            'title' => 'Booking ' . booking_reference($id),
            'booking' => $booking,
            'reference' => booking_reference($id),
            'nights' => booking_nights($booking['check_in'], $booking['check_out']),
        ]);
    }

    public function updateStatus(int $id): void
    {
        $this->requirePermission('bookings.manage');
        $status = $_POST['status'] ?? 'pending';
        if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
            $status = 'pending';
        }
        Booking::updateStatus($id, $status);
        Session::flash('success', 'Booking status updated to ' . $status . '.');
        $redirect = $_POST['redirect'] ?? url('/admin/bookings');
        $this->redirect($redirect);
    }
}
