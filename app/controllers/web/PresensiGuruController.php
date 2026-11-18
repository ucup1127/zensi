<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class PresensiGuruController
{
    // Tampilkan halaman kamera
    public function index(): void
    {
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        if (!$guru) {
            Session::toast('error', 'Data guru tidak ditemukan.');
            Response::redirect(url('dashboard'));
        }

        // Cek apakah hari ini sudah presensi datang?
        $hariIni = date('Y-m-d');
        $sudahDatang = DB::table('presensi_guru_event')
            ->where('guru_id', $guru['id'])
            ->where('tanggal', $hariIni)
            ->where('tipe', 'datang')
            ->first();

        $sudahPulang = DB::table('presensi_guru_event')
            ->where('guru_id', $guru['id'])
            ->where('tanggal', $hariIni)
            ->where('tipe', 'pulang')
            ->first();

        View::render('guru.presensi_saya', [
            'title' => 'Presensi Saya',
            'sudahDatang' => $sudahDatang,
            'sudahPulang' => $sudahPulang
        ], 'guru');
    }

    // Proses simpan foto & watermark
        // Proses simpan foto & watermark
    public function simpan(): void
    {
        try {
            // 1. Validasi Request
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['image']) || empty($input['tipe'])) {
                Response::json(['success' => false, 'message' => 'Data tidak valid.'], 400);
            }

            // Validasi CSRF manual
            if (!isset($input['csrf_token']) || !CSRF::verify($input['csrf_token'])) {
                Response::json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan refresh halaman.'], 403);
            }

            $base64Image = $input['image'];
            $tipe = $input['tipe']; // 'datang' atau 'pulang'
            $guru = DB::table('guru')->where('user_id', Auth::id())->first();
            $user = Auth::user();
            $sekolah = $_ENV['APP_NAME'] ?? 'SMP Muhammadiyah';

            // 2. Decode Base64 ke Gambar GD
            $imageParts = explode(";base64,", $base64Image);
            if (count($imageParts) < 2) {
                Response::json(['success' => false, 'message' => 'Format gambar tidak valid.'], 400);
            }
            
            $imageBase64 = base64_decode($imageParts[1]);
            
            // Cek apakah GD library aktif
            if (!function_exists('imagecreatefromstring')) {
                Response::json(['success' => false, 'message' => 'Ekstensi PHP GD belum aktif di server (Cek php.ini).'], 500);
            }

            $image = imagecreatefromstring($imageBase64);
            if (!$image) {
                Response::json(['success' => false, 'message' => 'Gagal membuat gambar dari string Base64.'], 500);
            }

            // 3. Buat Watermark (Teks)
            $warnaTeks = imagecolorallocate($image, 255, 255, 255); // Putih
            $warnaBg = imagecolorallocatealpha($image, 0, 0, 0, 60); // Hitam transparan
            
            $waktu = date('d-m-Y H:i:s');
            $teks1 = strtoupper($user['nama_lengkap']);
            $teks2 = "Presensi " . ucfirst($tipe);
            $teks3 = $waktu . " WIB";
            $teks4 = $sekolah;

            $lebarGambar = imagesx($image);
            $tinggiGambar = imagesy($image);
            
            // Buat background hitam transparan di pojok kiri bawah
            imagefilledrectangle($image, 0, $tinggiGambar - 80, $lebarGambar, $tinggiGambar, $warnaBg);

            // Tulis Teks
            imagestring($image, 5, 10, $tinggiGambar - 70, $teks1, $warnaTeks);
            imagestring($image, 4, 10, $tinggiGambar - 55, $teks2, $warnaTeks);
            imagestring($image, 3, 10, $tinggiGambar - 40, $teks3, $warnaTeks);
            imagestring($image, 3, 10, $tinggiGambar - 25, $teks4, $warnaTeks);

            // 4. Simpan ke Folder Storage
            $folderPath = BASE_PATH . '/storage/presensi/' . date('Y-m');
            if (!is_dir($folderPath)) {
                // Coba buat folder
                if (!mkdir($folderPath, 0777, true)) {
                    Response::json(['success' => false, 'message' => 'Gagal membuat folder penyimpanan. Pastikan folder storage bisa ditulisi.'], 500);
                }
            }

            $namaFile = $guru['id'] . '_' . $tipe . '_' . date('Ymd_His') . '.jpg';
            $fullPath = $folderPath . '/' . $namaFile;
            
            // Simpan sebagai JPEG
            if (!imagejpeg($image, $fullPath, 80)) {
                Response::json(['success' => false, 'message' => 'Gagal menyimpan file gambar ke folder storage.'], 500);
            }
            imagedestroy($image);

            $dbPath = 'storage/presensi/' . date('Y-m') . '/' . $namaFile;

                        // 5. Simpan ke Database
            $exists = DB::table('presensi_guru_event')
                ->where('school_id', current_school_id())
                ->where('guru_id', $guru['id'])
                ->where('tanggal', date('Y-m-d'))
                ->where('tipe', $tipe)
                ->first();

            if ($exists) {
                Response::json(['success' => false, 'message' => "Anda sudah melakukan presensi $tipe hari ini."], 400);
            }

            try {
                DB::table('presensi_guru_event')->insert([
                    'school_id' => current_school_id(),
                    'guru_id' => $guru['id'],
                    'tanggal' => date('Y-m-d'),
                    'tipe' => $tipe,
                    'metode' => 'foto', // <-- Sesuaikan dengan ENUM di database Anda
                    'waktu' => date('Y-m-d H:i:s'),
                    'foto_path' => $dbPath,
                    'dicatat_oleh' => Auth::id(), // <-- Tambahkan ini (siapa yang mencatat)
                    // Hapus 'status' karena tidak ada di tabel
                ]);
            } catch (\PDOException $e) {
                // Tangkap error PDO dan kirim ke Javascript
                Response::json(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()], 500);
            }

            // 6. Respon Sukses
            Response::json(['success' => true, 'message' => 'Presensi ' . $tipe . ' berhasil disimpan!']);

        } catch (\Throwable $e) {
            // TANGKAP ERROR PHP DAN KIRIM SEBAGAI JSON
            Response::json([
                'success' => false, 
                'message' => 'PHP Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ], 500);
        }
    }
}