<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;
use App\Core\Response;

class RoleMiddleware extends Middleware
{
    public function __construct(private array $roles = []) {}

    public function handle(): bool
    {
        if (!Auth::check()) {
            Response::redirect(url('login'));
            return false;
        }
        
        if (!Auth::isRole(...$this->roles)) {
            http_response_code(403);
            echo '<h1>403</h1><p>Anda tidak punya akses ke halaman ini.</p>';
            exit;
        }
        return true;
    }
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    
}