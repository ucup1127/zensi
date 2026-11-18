<?php
declare(strict_types=1);

namespace App\Core;

class CSRF
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::KEY);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::KEY, $token);
        }
        return $token;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        $expected = Session::get(self::KEY);
        return is_string($token) && is_string($expected)
            && hash_equals($expected, $token);
    }

    public static function check(): void
    {
        $token = Request::input('_token') ?? Request::input('csrf_token');
        if (!self::verify($token)) {
            http_response_code(419);
            echo '<h1>419</h1><p>Sesi form kedaluwarsa. Silakan refresh halaman.</p>';
            exit;
        }
    }
}