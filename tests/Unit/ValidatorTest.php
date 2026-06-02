<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testRequiredRule(): void
    {
        $v = new Validator(['name' => ''], ['name' => 'required']);
        $this->assertTrue($v->fails());
        $this->assertStringContainsString('required', strtolower($v->firstError() ?? ''));
    }

    public function testEmailRule(): void
    {
        $v = new Validator(['email' => 'not-an-email'], ['email' => 'required|email']);
        $this->assertTrue($v->fails());
    }

    public function testValidPayloadPasses(): void
    {
        $v = new Validator(
            ['name' => 'Guest', 'email' => 'guest@example.com'],
            ['name' => 'required|max:120', 'email' => 'required|email']
        );
        $this->assertFalse($v->fails());
    }
}
