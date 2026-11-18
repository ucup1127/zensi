<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware extends Middleware
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            Response::redirect(url('login'));
            return false;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $isChangePasswordPage = str_contains($uri, '/ganti-password') 
                            || str_contains($uri, '/logout');

        if (Session::get('must_change_password') && !$isChangePasswordPage) {
            Response::redirect(url('ganti-password'));
            return false;
        }

        return true;
    }
}