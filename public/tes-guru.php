<?php
require '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
$stmt = $pdo->query("SELECT * FROM users WHERE username = 'guru1'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User guru1 TIDAK ADA di database.");
}

echo "User ditemukan: " . $user['nama_lengkap'] . "<br>";
echo "Role: " . $user['role'] . "<br>";
echo "Harus ganti password: " . $user['must_change_password'] . "<br>";

if (password_verify('guru123', $user['password_hash'])) {
    echo "<h2 style='color:green'>PASSWORD COCOK!</h2>";

    $pdo->exec("UPDATE users SET must_change_password = 0 WHERE username = 'guru1'");
    echo "<h3 style='color:blue'>Kewajiban ganti password untuk guru1 sudah DIMATIKAN.</h3>";
    echo "<p>Silakan <a href='/login'>Login Kembali</a>.</p>";
} else {
    echo "<h2 style='color:red'>PASSWORD TIDAK COCOK!</h2>";
    // Kita paksa reset ke guru123
    $hash = password_hash('guru123', PASSWORD_DEFAULT);
    $pdo->exec("UPDATE users SET password_hash = '$hash' WHERE username = 'guru1'");
    echo "Password sudah di-reset otomatis. Silakan coba login lagi.";
}