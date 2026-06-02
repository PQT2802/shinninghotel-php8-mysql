<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Setting;
use App\Services\MailService;

class BookingController extends Controller
{
    /** Step 1: Dates & guests */
    public function stepDates(): void
    {
        $roomId = (int) ($_GET['room_id'] ?? 0);
        $checkIn = $_GET['check_in'] ?? '';
        $checkOut = $_GET['check_out'] ?? '';

        if ($roomId && $checkIn && $checkOut && !booking_validate_dates($checkIn, $checkOut)) {
            $room = Room::find($roomId);
            if ($room && $room['status'] === 'published' && Room::isAvailable($roomId, $checkIn, $checkOut)) {
                booking_wizard_set([
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'guests_count' => max(1, (int) ($_GET['guests'] ?? 2)),
                    'room_id' => $roomId,
                ]);
                $this->redirect(url('/book/guest'));
            }
        }

        if (isset($_GET['reset'])) {
            booking_wizard_clear();
        }

        $wizard = booking_wizard();
        $this->renderStep('dates', [
            'title' => 'Book Your Stay — Select Dates',
            'step' => 1,
            'checkIn' => $wizard['check_in'] ?? $checkIn,
            'checkOut' => $wizard['check_out'] ?? $checkOut,
            'guestsCount' => (int) ($wizard['guests_count'] ?? 2),
            'categoryId' => (int) ($wizard['category_id'] ?? 0),
            'categories' => RoomCategory::allActive(),
            'settings' => Setting::allKeyed(),
        ]);
    }

    public function saveDates(): void
    {
        $checkIn = trim($_POST['check_in'] ?? '');
        $checkOut = trim($_POST['check_out'] ?? '');
        $guestsCount = max(1, (int) ($_POST['guests_count'] ?? 2));
        $categoryId = (int) ($_POST['category_id'] ?? 0) ?: null;

        if ($err = booking_validate_dates($checkIn, $checkOut)) {
            Session::flash('error', $err);
            $this->redirect(url('/book'));
        }

        booking_wizard_set([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests_count' => $guestsCount,
            'category_id' => $categoryId,
            'room_id' => null,
        ]);

        $this->redirect(url('/book/rooms'));
    }

    /** Step 2: Choose room */
    public function stepRooms(): void
    {
        $wizard = $this->requireWizardThrough(['check_in', 'check_out']);
        $rooms = Room::published([
            'check_in' => $wizard['check_in'],
            'check_out' => $wizard['check_out'],
            'category_id' => $wizard['category_id'] ?? null,
            'guests_count' => $wizard['guests_count'] ?? 2,
        ]);

        $nights = booking_nights($wizard['check_in'], $wizard['check_out']);
        foreach ($rooms as &$room) {
            $room['nights'] = $nights;
            $room['stay_total'] = $nights * (float) $room['price_per_night'];
        }
        unset($room);

        $this->renderStep('rooms', [
            'title' => 'Choose Your Room',
            'step' => 2,
            'rooms' => $rooms,
            'wizard' => $wizard,
            'nights' => $nights,
            'settings' => Setting::allKeyed(),
        ]);
    }

    public function saveRoom(): void
    {
        $wizard = $this->requireWizardThrough(['check_in', 'check_out']);
        $roomId = (int) ($_POST['room_id'] ?? 0);
        $room = Room::find($roomId);

        if (!$room || $room['status'] !== 'published') {
            Session::flash('error', 'Please select a valid room.');
            $this->redirect(url('/book/rooms'));
        }

        if ((int) $room['max_guests'] < (int) ($wizard['guests_count'] ?? 2)) {
            Session::flash('error', 'This room cannot accommodate your party size.');
            $this->redirect(url('/book/rooms'));
        }

        if (!Room::isAvailable($roomId, $wizard['check_in'], $wizard['check_out'])) {
            Session::flash('error', 'This room is no longer available for your dates.');
            $this->redirect(url('/book/rooms'));
        }

        booking_wizard_set(['room_id' => $roomId]);
        $this->redirect(url('/book/guest'));
    }

    /** Step 3: Guest details */
    public function stepGuest(): void
    {
        $wizard = $this->requireWizardThrough(['check_in', 'check_out', 'room_id']);
        $room = Room::find((int) $wizard['room_id']);
        if (!$room) {
            Session::flash('error', 'Room not found. Please select again.');
            $this->redirect(url('/book/rooms'));
        }

        $nights = booking_nights($wizard['check_in'], $wizard['check_out']);

        $this->renderStep('guest', [
            'title' => 'Guest Information',
            'step' => 3,
            'room' => $room,
            'wizard' => $wizard,
            'nights' => $nights,
            'totalPrice' => $nights * (float) $room['price_per_night'],
            'settings' => Setting::allKeyed(),
        ]);
    }

    public function saveGuest(): void
    {
        $wizard = $this->requireWizardThrough(['check_in', 'check_out', 'room_id']);
        $input = [
            'guest_name' => trim($_POST['guest_name'] ?? ''),
            'guest_email' => trim($_POST['guest_email'] ?? ''),
            'guest_phone' => trim($_POST['guest_phone'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        $validator = new Validator($input, [
            'guest_name' => 'required|max:120',
            'guest_email' => 'required|email',
        ]);
        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Session::set('old_input', $input);
            $this->redirect(url('/book/guest'));
        }

        booking_wizard_set($input);
        $this->redirect(url('/book/review'));
    }

    /** Step 4: Review & confirm */
    public function stepReview(): void
    {
        $wizard = $this->requireWizardThrough(['check_in', 'check_out', 'room_id', 'guest_name', 'guest_email']);
        $room = Room::find((int) $wizard['room_id']);
        if (!$room) {
            $this->redirect(url('/book/rooms'));
        }

        $nights = booking_nights($wizard['check_in'], $wizard['check_out']);
        $total = $nights * (float) $room['price_per_night'];

        if (!Room::isAvailable((int) $wizard['room_id'], $wizard['check_in'], $wizard['check_out'])) {
            Session::flash('error', 'Sorry, this room was just booked. Please choose another room.');
            $this->redirect(url('/book/rooms'));
        }

        $this->renderStep('review', [
            'title' => 'Review & Confirm',
            'step' => 4,
            'room' => $room,
            'wizard' => $wizard,
            'nights' => $nights,
            'totalPrice' => $total,
            'settings' => Setting::allKeyed(),
        ]);
    }

    public function confirm(): void
    {
        $wizard = $this->requireWizardThrough(['check_in', 'check_out', 'room_id', 'guest_name', 'guest_email']);
        $roomId = (int) $wizard['room_id'];
        $room = Room::find($roomId);

        if (!$room || $room['status'] !== 'published') {
            Session::flash('error', 'Invalid booking. Please start again.');
            booking_wizard_clear();
            $this->redirect(url('/book'));
        }

        if (!Room::isAvailable($roomId, $wizard['check_in'], $wizard['check_out'])) {
            Session::flash('error', 'This room is no longer available.');
            $this->redirect(url('/book/rooms'));
        }

        $nights = booking_nights($wizard['check_in'], $wizard['check_out']);
        $totalPrice = $nights * (float) $room['price_per_night'];

        $bookingId = Booking::create([
            'room_id' => $roomId,
            'check_in' => $wizard['check_in'],
            'check_out' => $wizard['check_out'],
            'guest_name' => $wizard['guest_name'],
            'guest_email' => $wizard['guest_email'],
            'guest_phone' => $wizard['guest_phone'] ?? '',
            'guests_count' => (int) ($wizard['guests_count'] ?? 2),
            'total_price' => $totalPrice,
            'status' => 'pending',
            'notes' => $wizard['notes'] ?? '',
            'locale' => locale(),
        ]);

        $booking = Booking::find($bookingId);
        $reference = booking_reference($bookingId);
        $emailStatus = 'skipped';
        if ($booking && MailService::isEnabled()) {
            $emailStatus = MailService::sendBookingConfirmation($booking, $reference) ? 'sent' : 'failed';
        }

        booking_wizard_clear();
        Session::flash('booking_id', $bookingId);
        Session::flash('email_status', $emailStatus);
        $this->redirect(url('/book/complete/' . $bookingId));
    }

    public function complete(int $id): void
    {
        $booking = Booking::find($id);
        if (!$booking) {
            $this->redirect(url('/'));
        }

        $this->view('web/booking/complete', [
            'title' => 'Booking Confirmed',
            'metaDescription' => 'Your reservation request has been received.',
            'booking' => $booking,
            'reference' => booking_reference($id),
            'nights' => booking_nights($booking['check_in'], $booking['check_out']),
            'settings' => Setting::allKeyed(),
        ]);
    }

    private function requireWizardThrough(array $requiredKeys): array
    {
        $wizard = booking_wizard();
        foreach ($requiredKeys as $key) {
            if (empty($wizard[$key])) {
                Session::flash('error', 'Please complete the booking steps in order.');
                $this->redirect(url('/book'));
            }
        }
        return $wizard;
    }

    private function renderStep(string $view, array $data): void
    {
        $data['wizardSteps'] = [
            1 => ['label' => __('book.step_dates'), 'url' => url('/book')],
            2 => ['label' => __('book.step_room'), 'url' => url('/book/rooms')],
            3 => ['label' => __('book.step_guest'), 'url' => url('/book/guest')],
            4 => ['label' => __('book.step_confirm'), 'url' => url('/book/review')],
        ];
        $this->view('web/booking/' . $view, $data);
    }
}
