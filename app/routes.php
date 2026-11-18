<?php
declare(strict_types=1);

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

/** @var Router $router */

// ---- Publik ----
$router->get('/', 'App\Controllers\Web\AuthController@redirectRoot');
$router->get('/login', 'App\Controllers\Web\AuthController@showLogin');
$router->post('/login', 'App\Controllers\Web\AuthController@login');
$router->post('/logout', 'App\Controllers\Web\AuthController@logout', [fn() => (new AuthMiddleware())->handle()]);

// ---- Auth required (Semua Role) ----
$router->group(['middleware' => [fn() => (new AuthMiddleware())->handle()]], function (Router $r) {
    $r->get('/dashboard', 'App\Controllers\Web\DashboardController@index');
    $r->get('/ganti-password', 'App\Controllers\Web\AuthController@showChangePassword');
    $r->post('/ganti-password', 'App\Controllers\Web\AuthController@changePassword');
});

// ---- Admin only ----
$router->group(['middleware' => [
    fn() => (new AuthMiddleware())->handle(),
    fn() => (new RoleMiddleware(['admin']))->handle(),
]], function (Router $r) {
    $r->get('/admin', 'App\Controllers\Web\DashboardController@admin');
    $r->post('/admin/ganti-tahun-ajaran', 'App\Controllers\Web\DashboardController@gantiTahunAjaran');
    
    // Tahun Ajaran
    $r->get('/admin/tahun-ajaran', 'App\Controllers\Web\TahunAjaranController@index');
    $r->get('/admin/tahun-ajaran/{id}/detail', 'App\Controllers\Web\TahunAjaranController@detail');
    $r->post('/admin/tahun-ajaran', 'App\Controllers\Web\TahunAjaranController@store');
    $r->post('/admin/tahun-ajaran/{id}', 'App\Controllers\Web\TahunAjaranController@update');
    $r->post('/admin/tahun-ajaran/{id}/delete', 'App\Controllers\Web\TahunAjaranController@destroy');

    // Kelas
    $r->get('/admin/kelas', 'App\Controllers\Web\KelasController@index');
    $r->get('/admin/kelas/{id}/detail', 'App\Controllers\Web\KelasController@detail');
    $r->post('/admin/kelas', 'App\Controllers\Web\KelasController@store');
    $r->post('/admin/kelas/{id}', 'App\Controllers\Web\KelasController@update');
    $r->post('/admin/kelas/{id}/delete', 'App\Controllers\Web\KelasController@destroy');

    // Mapel
    $r->get('/admin/mapel', 'App\Controllers\Web\MapelController@index');
    $r->get('/admin/mapel/{id}/detail', 'App\Controllers\Web\MapelController@detail');
    $r->post('/admin/mapel', 'App\Controllers\Web\MapelController@store');
    $r->post('/admin/mapel/{id}', 'App\Controllers\Web\MapelController@update');
    $r->post('/admin/mapel/{id}/delete', 'App\Controllers\Web\MapelController@destroy');

    // Guru
    $r->get('/admin/guru', 'App\Controllers\Web\GuruController@index');
    $r->get('/admin/guru/{id}/detail', 'App\Controllers\Web\GuruController@detail');
    $r->post('/admin/guru', 'App\Controllers\Web\GuruController@store');
    $r->post('/admin/guru/{id}', 'App\Controllers\Web\GuruController@update');
    $r->post('/admin/guru/{id}/delete', 'App\Controllers\Web\GuruController@destroy');

    // Siswa
    $r->get('/admin/siswa', 'App\Controllers\Web\SiswaController@index');
    $r->get('/admin/siswa/{id}/detail', 'App\Controllers\Web\SiswaController@detail');
    $r->post('/admin/siswa', 'App\Controllers\Web\SiswaController@store');
    $r->post('/admin/siswa/{id}', 'App\Controllers\Web\SiswaController@update');
    $r->post('/admin/siswa/{id}/delete', 'App\Controllers\Web\SiswaController@destroy');

    // Jadwal
    $r->get('/admin/jadwal', 'App\Controllers\Web\JadwalController@index');
    $r->get('/admin/jadwal/create', 'App\Controllers\Web\JadwalController@create');
    $r->post('/admin/jadwal', 'App\Controllers\Web\JadwalController@store');
    $r->post('/admin/jadwal/{id}/delete', 'App\Controllers\Web\JadwalController@destroy');
    $r->get('/admin/jadwal/pdf', 'App\Controllers\Web\JadwalController@jadwalPdf');

    // Hari Libur
    $r->get('/admin/hari-libur', 'App\Controllers\Web\HariLiburController@index');
    $r->get('/admin/hari-libur/events', 'App\Controllers\Web\HariLiburController@events');
    $r->post('/admin/hari-libur', 'App\Controllers\Web\HariLiburController@store');
    $r->post('/admin/hari-libur/{id}/delete', 'App\Controllers\Web\HariLiburController@destroy');

    // Slot Jam
    $r->get('/admin/slot-jam', 'App\Controllers\Web\SlotJamController@index');
    $r->post('/admin/slot-jam', 'App\Controllers\Web\SlotJamController@store');
    $r->post('/admin/slot-jam/{id}', 'App\Controllers\Web\SlotJamController@update');
    $r->post('/admin/slot-jam/{id}/delete', 'App\Controllers\Web\SlotJamController@destroy');

    // Profil
    $r->get('/admin/profil', 'App\Controllers\Web\ProfilController@index');
    $r->post('/admin/profil', 'App\Controllers\Web\ProfilController@update');

    // Kenaikan Kelas
    $r->get('/admin/kenaikan-kelas', 'App\Controllers\Web\SiswaController@kenaikanKelas');
    $r->post('/admin/kenaikan-kelas/preview', 'App\Controllers\Web\SiswaController@kenaikanKelasPreview');
    $r->post('/admin/kenaikan-kelas/proses', 'App\Controllers\Web\SiswaController@kenaikanKelasProses');

        // Import Siswa
    $r->get('/admin/siswa/import', 'App\Controllers\Web\SiswaController@importForm');
    $r->post('/admin/siswa/import', 'App\Controllers\Web\SiswaController@importProses');

        // Nonaktifkan Guru & Reassign Jadwal
    $r->get('/admin/guru/{id}/nonaktifkan', 'App\Controllers\Web\GuruController@formNonaktifkan');
    $r->post('/admin/guru/{id}/cek-jadwal', 'App\Controllers\Web\GuruController@cekJadwalBentrok');
    $r->post('/admin/guru/{id}/nonaktifkan', 'App\Controllers\Web\GuruController@prosesNonaktifkan');
    $r->post('/admin/guru/{id}/aktifkan', 'App\Controllers\Web\GuruController@aktifkanKembali');

        // Monitoring Sesi
    $r->get('/admin/monitoring-sesi', 'App\Controllers\Web\DashboardController@monitoringSesi');
    $r->post('/admin/monitoring-sesi/{id}/tutup', 'App\Controllers\Web\DashboardController@tutupSesiPaksa');

     // Rekap Global
    $r->get('/admin/rekap-global', 'App\Controllers\Web\RekapController@global');
    $r->get('/admin/rekap-global/print', 'App\Controllers\Web\RekapController@globalPrint');
});

// ---- Guru only ----
$router->group(['middleware' => [
    fn() => (new AuthMiddleware())->handle(),
    fn() => (new RoleMiddleware(['guru']))->handle(),
]], function (Router $r) {
    $r->get('/guru', 'App\Controllers\Web\DashboardController@guru'); // <-- TAMBAHKAN INI
    $r->get('/guru/buka-sesi', 'App\Controllers\Web\PresensiSiswaController@bukaSesi');
    $r->post('/guru/buka-sesi', 'App\Controllers\Web\PresensiSiswaController@storeSesi');
    $r->get('/guru/presensi/{id}', 'App\Controllers\Web\PresensiSiswaController@show');
    $r->post('/guru/presensi/{id}/simpan', 'App\Controllers\Web\PresensiSiswaController@simpan');

    $r->get('/guru/jadwal', 'App\Controllers\Web\JadwalController@jadwalGuru');
    $r->get('/guru/presensi-saya', 'App\Controllers\Web\PresensiGuruController@index');
    $r->post('/guru/presensi-saya/simpan', 'App\Controllers\Web\PresensiGuruController@simpan');
    $r->get('/guru/rekap/siswa', 'App\Controllers\Web\RekapController@siswa');
    $r->get('/guru/rekap/saya', 'App\Controllers\Web\RekapController@guru');
    $r->get('/guru/file-presensi/{filename}', 'App\Controllers\Web\RekapController@showFoto');
    $r->get('/guru/rekap/siswa', 'App\Controllers\Web\RekapController@siswa');
    $r->get('/guru/rekap/siswa/pdf', 'App\Controllers\Web\RekapController@siswaPdf'); 

    $r->get('/guru/about', 'App\Controllers\Web\DashboardController@about');
    $r->get('/guru/profile', 'App\Controllers\Web\DashboardController@profile');

        // QR Code Presensi
    $r->get('/guru/sesi-qr/{id}', 'App\Controllers\Web\PresensiSiswaController@showQr');
    $r->get('/guru/sesi-qr/{id}/token', 'App\Controllers\Web\PresensiSiswaController@getQrToken');
});

// ---- Siswa only ----
$router->group(['middleware' => [
    fn() => (new AuthMiddleware())->handle(),
    fn() => (new RoleMiddleware(['siswa']))->handle(),
]], function (Router $r) {
    $r->get('/siswa', 'App\Controllers\Web\DashboardController@siswa');
    $r->get('/siswa/scan', 'App\Controllers\Web\ScanQrController@index');
    $r->post('/siswa/scan/verify', 'App\Controllers\Web\ScanQrController@verify');
    $r->get('/siswa/jadwal', 'App\Controllers\Web\JadwalController@jadwalSiswa');
    $r->get('/siswa/rekap', 'App\Controllers\Web\RekapController@siswaMandiri');
    $r->get('/siswa/rekap/pdf', 'App\Controllers\Web\RekapController@siswaMandiriPdf');
    $r->get('/siswa/about', 'App\Controllers\Web\DashboardController@aboutSiswa');
    $r->get('/siswa/profile', 'App\Controllers\Web\DashboardController@profileSiswa');
    $r->post('/siswa/profile/avatar', 'App\Controllers\Web\DashboardController@updateAvatar');
});

// Endpoint untuk verifikasi token oleh e-library
$router->post('/api/sso/verify-token', 'App\Controllers\Web\AuthController@verifySsoToken');
