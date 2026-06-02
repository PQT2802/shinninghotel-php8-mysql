<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking confirmation</title>
</head>
<body style="font-family: Georgia, serif; color: #2c2c2c; line-height: 1.6; max-width: 560px; margin: 0 auto; padding: 24px;">
    <p style="color: #c9a962; letter-spacing: 0.15em; text-transform: uppercase; font-size: 12px;"><?= htmlspecialchars($siteName ?? 'Shinning Hotel') ?></p>
    <h1 style="color: #0f1a2e; font-size: 24px;">Thank you for your reservation</h1>
    <p>Hello <?= htmlspecialchars($booking['guest_name'] ?? '') ?>,</p>
    <p>We have received your booking request. Our team will confirm availability shortly.</p>

    <table style="width: 100%; border-collapse: collapse; margin: 24px 0; font-family: sans-serif; font-size: 14px;">
        <tr><td style="padding: 8px 0; color: #6b7280;">Reference</td><td style="padding: 8px 0;"><strong><?= htmlspecialchars($reference ?? '') ?></strong></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Room</td><td style="padding: 8px 0;"><?= htmlspecialchars($booking['room_name'] ?? '') ?></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Check-in</td><td style="padding: 8px 0;"><?= htmlspecialchars($booking['check_in'] ?? '') ?></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Check-out</td><td style="padding: 8px 0;"><?= htmlspecialchars($booking['check_out'] ?? '') ?></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Nights</td><td style="padding: 8px 0;"><?= (int) ($nights ?? 1) ?></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Guests</td><td style="padding: 8px 0;"><?= (int) ($booking['guests_count'] ?? 2) ?></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Total</td><td style="padding: 8px 0;"><strong>$<?= number_format((float) ($booking['total_price'] ?? 0), 2) ?></strong></td></tr>
        <tr><td style="padding: 8px 0; color: #6b7280;">Status</td><td style="padding: 8px 0;">Pending confirmation</td></tr>
    </table>

    <p style="font-family: sans-serif; font-size: 13px; color: #6b7280;">If you have questions, reply to this email or contact our reservations team.</p>
    <p style="font-family: sans-serif; font-size: 13px;"><a href="<?= htmlspecialchars($siteUrl ?? '/') ?>" style="color: #a88b4a;">Visit our website</a></p>
</body>
</html>
