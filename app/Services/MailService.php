<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Logger;

class MailService
{
    public static function isEnabled(): bool
    {
        $config = require BASE_PATH . '/config/mail.php';

        return (bool) $config['enabled'];
    }

    public static function send(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool
    {
        $config = require BASE_PATH . '/config/mail.php';
        if (!$config['enabled']) {
            return false;
        }

        $to = trim($to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $fromName = $config['from_name'];
        $fromAddress = $config['from_address'];
        $replyTo = $replyTo ?? $config['reply_to'];

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . self::encodeAddress($fromName, $fromAddress),
            'Reply-To: ' . $replyTo,
            'X-Mailer: ShinningHotel/1.0',
        ];

        $ok = @mail($to, self::encodeSubject($subject), $htmlBody, implode("\r\n", $headers));
        if (!$ok) {
            Logger::warning('Mail send failed', ['to' => $to, 'subject' => $subject]);
        }

        return $ok;
    }

    public static function sendBookingConfirmation(array $booking, string $reference): bool
    {
        $subject = 'Booking received — ' . $reference . ' | ' . brand_name() . ' Hotel';
        $html = self::renderTemplate('booking_confirmation', [
            'booking' => $booking,
            'reference' => $reference,
            'nights' => booking_nights($booking['check_in'], $booking['check_out']),
            'siteName' => brand_name() . ' Hotel',
            'siteUrl' => url('/'),
        ]);

        return self::send(
            (string) $booking['guest_email'],
            $subject,
            $html,
            null
        );
    }

    private static function renderTemplate(string $name, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require BASE_PATH . '/app/Views/emails/' . $name . '.php';

        return (string) ob_get_clean();
    }

    private static function encodeAddress(string $name, string $email): string
    {
        return sprintf('"%s" <%s>', str_replace('"', '', $name), $email);
    }

    private static function encodeSubject(string $subject): string
    {
        return '=?UTF-8?B?' . base64_encode($subject) . '?=';
    }
}
