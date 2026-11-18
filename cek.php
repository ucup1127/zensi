<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    
    // Kita buat hash baru langsung di sini biar pasti valid
    $hashAdmin = password_hash('admin123', PASSWORD_DEFAULT);
    $hashGuru  = password_hash('guru123', PASSWORD_DEFAULT);
    $hashSiswa = password_hash('siswa123', PASSWORD_DEFAULT);

    // Update ke database
    $pdo->exec("UPDATE users SET password_hash = '$hashAdmin' WHERE username = 'admin'");
    $pdo->exec("UPDATE users SET password_hash = '$hashGuru' WHERE username = 'guru1'");
    $pdo->exec("UPDATE users SET password_hash = '$hashGuru' WHERE username = 'guru2'");
    $pdo->exec("UPDATE users SET password_hash = '$hashSiswa' WHERE username = 'siswa1'");
    $pdo->exec("UPDATE users SET password_hash = '$hashSiswa' WHERE username = 'siswa2'");

    echo "SUKSES! Semua password sudah di-reset.\n";
    echo "Admin : admin / admin123\n";
    echo "Guru  : guru1 / guru123\n";
    echo "Siswa : siswa1 / siswa123\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}