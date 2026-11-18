<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// ---- Autoload Composer ----
$autoload = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    exit('Vendor belum terinstall. Jalankan: composer install');
}
require $autoload;

// ---- Load .env ----
use Dotenv\Dotenv;

if (file_exists(BASE_PATH . '/.env')) {
    Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

// ---- Load config ----
$config = require BASE_PATH . '/app/config/app.php';
$dbConfig = require BASE_PATH . '/app/config/database.php';

// ---- Timezone ----
date_default_timezone_set($config['timezone']);

// ---- Error handling ----
if ($config['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
}

// ---- Secure session ----
use App\Core\Session;

Session::start($config);

// ---- Boot DB ----
use App\Core\DB;

DB::init($dbConfig);

// ---- Router ----
use App\Core\Router;

$router = new Router();

// Daftarkan route
require BASE_PATH . '/app/routes.php';

// Dispatch
try {
    $router->dispatch();
} catch (\Throwable $e) {
    if ($config['debug']) {
        echo '<pre>';
        echo htmlspecialchars((string) $e);
        echo '</pre>';
    } else {
        http_response_code(500);
        echo 'Terjadi kesalahan. Silakan coba lagi.';
        error_log((string) $e);
    }
}