<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ViSeedTranslator;
use PHPUnit\Framework\TestCase;

class ViSeedTranslatorTest extends TestCase
{
    public function testDetectsCorruptedText(): void
    {
        $this->assertTrue(ViSeedTranslator::isCorrupted('Trang ch???'));
        $this->assertTrue(ViSeedTranslator::isCorrupted('Ch??o m???ng'));
        $this->assertFalse(ViSeedTranslator::isCorrupted('Trang chủ'));
        $this->assertFalse(ViSeedTranslator::isCorrupted(''));
    }

    public function testExactMenuTranslation(): void
    {
        $translator = new ViSeedTranslator(false);
        $this->assertSame('Trang chủ', $translator->translate('Home', ['entity' => 'menu_item', 'field' => 'title']));
        $this->assertSame('Đặt phòng', $translator->translate('Book Now', ['entity' => 'menu_item', 'field' => 'title']));
    }

    public function testSlugAwarePageTitle(): void
    {
        $translator = new ViSeedTranslator(false);
        $this->assertSame(
            'Về chúng tôi',
            $translator->translate('About Us', [
                'entity' => 'page',
                'field' => 'title',
                'slug' => 'about-us',
            ])
        );
    }
}
