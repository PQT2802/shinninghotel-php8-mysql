<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Locale;
use PHPUnit\Framework\TestCase;

final class LangTest extends TestCase
{
    protected function setUp(): void
    {
        Locale::set('en');
    }

    public function testTranslationEnglish(): void
    {
        $this->assertSame('Book Now', __('nav.book_now'));
    }

    public function testTranslationVietnamese(): void
    {
        Locale::set('vi');
        $this->assertSame('Đặt phòng', __('nav.book_now'));
    }

    public function testFallbackForMissingKey(): void
    {
        $this->assertSame('missing.key', __('missing.key'));
    }

    public function testLocaleUrl(): void
    {
        $this->assertStringEndsWith('/en/rooms', locale_url('/rooms'));
        $this->assertStringEndsWith('/vi/rooms', locale_url('/rooms', 'vi'));
    }
}
