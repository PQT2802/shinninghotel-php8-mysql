<?php

declare(strict_types=1);

function slugify(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text) ?? '';
    $text = preg_replace('/[\s-]+/', '-', trim($text)) ?? '';
    return trim($text, '-');
}
