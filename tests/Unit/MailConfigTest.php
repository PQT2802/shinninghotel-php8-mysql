<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class MailConfigTest extends TestCase
{
    public function testMailConfigLoadsDefaults(): void
    {
        $_ENV['MAIL_ENABLED'] = 'false';
        $config = require BASE_PATH . '/config/mail.php';
        $this->assertFalse($config['enabled']);
        $this->assertNotEmpty($config['from_address']);
    }
}
