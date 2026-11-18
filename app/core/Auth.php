<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    private static ?array $user = null;

    public static function attempt(string $username, string $password, int $schoolId): bool
    {
        $user = DB::table('users')
            ->where('school_id', $schoolId)
            ->where('username', $username)
            ->where('aktif', 1)
            ->first();

        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Session::set('school_id', (int) $user['school_id']);
        Session::set('role', $user['role']);
        Session::set('nama_lengkap', $user['nama_lengkap']);
        Session::set('must_change_password', (bool) $user['must_change_password']);

        self::$user = $user;
        return true;
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }
        self::$user = DB::table('users')
            ->where('id', $id)
            ->where('aktif', 1)
            ->first();
        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    public static function isRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::destroy();
    }

    public static function hashPassword(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }
}