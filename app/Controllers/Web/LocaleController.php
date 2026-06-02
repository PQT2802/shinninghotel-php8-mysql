<?php

declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Locale;

class LocaleController extends Controller
{
    public function redirectRoot(): void
    {
        Locale::handleRootRedirect();
    }

    public function switch(string $targetLocale): void
    {
        Locale::set($targetLocale);
        $back = $_GET['back'] ?? '';
        if ($back !== '' && str_starts_with($back, '/')) {
            $this->redirect(url($back));
        }
        $this->redirect(locale_url('/'));
    }
}
