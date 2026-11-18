<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$config = require dirname(__DIR__) . '/app/config/app.php';
$dbConfig = require dirname(__DIR__) . '/app/config/database.php';
App\Core\DB::init($dbConfig);

echo "<h2>🕵️‍♂️ X-Ray Data User</h2>";
echo "<p>Masukkan username guru yang mau login (misal: guru1 / nip guru):</p>";

if (isset($_GET['user'])) {
    $u = $_GET['user'];
    $user = App\Core\DB::table('users')->where('username', $u)->first();
    
    if (!$user) {
        die("<h3 style='color:red;'>❌ User dengan username '$u' TIDAK ADA di database!</h3>");
    }
    
    echo "<h3 style='color:green;'>✅ User Ditemukan!</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><td><b>ID</b></td><td>{$user['id']}</td></tr>";
    echo "<tr><td><b>Username</b></td><td>{$user['username']}</td></tr>";
    echo "<tr><td><b>Role</b></td><td><b style='color:blue;'>{$user['role']}</b></td></tr>";
    echo "<tr><td><b>Must Change Pass</b></td><td><b style='color:red;'>{$user['must_change_password']}</b></td></tr>";
    echo "<tr><td><b>Status Aktif</b></td><td>{$user['aktif']}</td></tr>";
    echo "</table>";

    // Cek relasi ke tabel guru
    $guru = App\Core\DB::table('guru')->where('user_id', $user['id'])->first();
    if ($guru) {
        echo "<h3 style='color:green;'>✅ Terhubung ke Tabel Guru</h3>";
        echo "<p>Guru ID: <b>{$guru['id']}</b> | NIP: <b>{$guru['nip']}</b></p>";
    } else {
        echo "<h3 style='color:red;'>❌ BLOKIR! User ini TIDAK PUNYA data di tabel `guru`!</h3>";
        echo "<p>Inilah yang menyebabkan sistem menendang Anda ke halaman login.</p>";
    }
}
?>
<hr>
<form method="GET">
    <input type="text" name="user" placeholder="Ketik username guru..." required>
    <button type="submit">Cek Data</button>
</form>