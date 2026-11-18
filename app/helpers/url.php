<?php
declare(strict_types=1);

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? 'http://localhost/presensi/public', '/');
        return $base . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return base_url($path);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        \App\Core\Response::redirect(url($path));
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): string
    {
        $old = \App\Core\Session::flash('old') ?? [];
        return htmlspecialchars((string) ($old[$key] ?? $default));
    }
}

if (!function_exists('errors')) {
    function errors(): array
    {
        return \App\Core\Session::flash('errors') ?? [];
    }
}
if (!function_exists('pagination_links')) {
    function pagination_links(array $paginator, string $pageName = 'page'): string
    {
        if ($paginator['last_page'] <= 1) return '';

        $html = '<div style="display:flex; gap:4px; margin-top:16px; flex-wrap:wrap;">';
        $current = $paginator['current_page'];
        $last = $paginator['last_page'];
        
        // Ambil query string saat ini (buang parameter page)
        $query = $_GET;
        unset($query[$pageName]);

        $buildUrl = function($page) use ($query, $pageName) {
            $q = $query;
            $q[$pageName] = $page;
            return '?' . http_build_query($q);
        };

        // Prev
        if ($current > 1) {
            $html .= '<a href="' . e($buildUrl($current - 1)) . '" class="btn btn-primary" style="min-height:36px; padding:6px 12px; font-size:14px; background:#e2e8f0; color:#0f172a;">&laquo; Prev</a>';
        }

        // Pages (tampilkan maksimal 5 halaman)
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current) {
                $html .= '<span class="btn" style="min-height:36px; padding:6px 12px; font-size:14px; background:var(--primary); color:white;">' . $i . '</span>';
            } else {
                $html .= '<a href="' . e($buildUrl($i)) . '" class="btn" style="min-height:36px; padding:6px 12px; font-size:14px; background:#fff; border:1px solid #e2e8f0; color:#0f172a;">' . $i . '</a>';
            }
        }

        // Next
        if ($current < $last) {
            $html .= '<a href="' . e($buildUrl($current + 1)) . '" class="btn btn-primary" style="min-height:36px; padding:6px 12px; font-size:14px; background:#e2e8f0; color:#0f172a;">Next &raquo;</a>';
        }
        
        $html .= '</div>';
        return $html;
    }
}