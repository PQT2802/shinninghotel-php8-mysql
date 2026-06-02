<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class BookingHelperTest extends TestCase
{
    public function testBookingReferenceFormat(): void
    {
        $this->assertSame('SHN-000001', booking_reference(1));
        $this->assertSame('SHN-000042', booking_reference(42));
        $this->assertSame('SHN-123456', booking_reference(123456));
    }

    public function testValidateDatesRejectsEmpty(): void
    {
        $this->assertNotNull(booking_validate_dates('', ''));
    }

    public function testValidateDatesRejectsPastCheckIn(): void
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $this->assertNotNull(booking_validate_dates($yesterday, $tomorrow));
    }

    public function testValidateDatesAcceptsValidRange(): void
    {
        $in = date('Y-m-d', strtotime('+2 days'));
        $out = date('Y-m-d', strtotime('+5 days'));
        $this->assertNull(booking_validate_dates($in, $out));
    }

    public function testNightsCalculation(): void
    {
        $this->assertSame(3, booking_nights('2026-06-10', '2026-06-13'));
        $this->assertSame(1, booking_nights('2026-06-10', '2026-06-11'));
    }

    public function testFormatDates(): void
    {
        $formatted = booking_format_dates('2026-06-10', '2026-06-13');
        $this->assertStringContainsString('Jun', $formatted);
        $this->assertStringContainsString('→', $formatted);
    }
}
