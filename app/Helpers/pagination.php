<?php

declare(strict_types=1);

function pagination_links(array $pager, string $baseUrl): string
{
    $page = (int) ($pager['page'] ?? 1);
    $pages = (int) ($pager['pages'] ?? 1);
    if ($pages <= 1) {
        return '';
    }

    $sep = str_contains($baseUrl, '?') ? '&' : '?';
    $html = '<nav class="pagination">';
    if ($page > 1) {
        $html .= '<a href="' . e($baseUrl . $sep . 'page=' . ($page - 1)) . '">← Prev</a>';
    }
    $html .= '<span>Page ' . $page . ' / ' . $pages . '</span>';
    if ($page < $pages) {
        $html .= '<a href="' . e($baseUrl . $sep . 'page=' . ($page + 1)) . '">Next →</a>';
    }
    $html .= '</nav>';
    return $html;
}
