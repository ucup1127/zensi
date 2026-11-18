<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class ScanQrController
{
    // Halaman scanner
    public function index(): void
    {
        $siswa = $this->getSiswa();
        if (!$siswa) {
            \App\Core\Session::toast('error', 'Data siswa tidak ditemukan.');
            Response::redirect(url('dashboard'));
        }

        // Cari sesi aktif untuk kelas siswa ini
        $sesiAktif = DB::rawOne("
            SELECT s.id, s.tanggal, s.dibuka_at, s.qr_expired_at,
                   m.nama AS mapel_nama, k.nama AS kelas_nama,
                   u.nama_lengkap AS guru_nama
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN guru g ON s.dibuka_oleh = g.user_id
            JOIN users u ON g.user_id = u.id
            WHERE s.school_id = ?
              AND s.ditutup_at IS NULL
              AND s.tanggal = CURDATE()
              AND p.kelas_id = ?
            ORDER BY s.dibuka_at DESC
            LIMIT 1
        ", [current_school_id(), $siswa['kelas_id']]);

        View::render('siswa.scan_qr', [
            'title' => 'Scan QR Presensi',
            'siswa' => $siswa,
            'sesiAktif' => $sesiAktif,
        ], 'siswa');
    }

    // Endpoint AJAX: verifikasi token
    public function verify(): void
    {
        // 1. Validasi CSRF
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['token']) || empty($input['csrf_token'])) {
            Response::json(['success' => false, 'message' => 'Data tidak valid.'], 400);
        }
        if (!CSRF::verify($input['csrf_token'])) {
            Response::json(['success' => false, 'message' => 'Sesi Anda berakhir. Refresh halaman.'], 403);
        }

        $token = trim((string) $input['token']);
        $siswa = $this->getSiswa();
        if (!$siswa) {
            Response::json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 403);
        }

        // 2. Cari sesi yang sedang buka dengan token ini
        $sesi = DB::rawOne("
            SELECT s.*, p.kelas_id
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            WHERE s.school_id = ?
              AND s.qr_token = ?
              AND s.ditutup_at IS NULL
              AND s.tanggal = CURDATE()
            LIMIT 1
        ", [current_school_id(), $token]);

        if (!$sesi) {
            Response::json(['success' => false, 'message' => 'QR tidak dikenali atau sesi sudah ditutup.'], 404);
        }

        // 3. Cek expired
        if (empty($sesi['qr_expired_at']) || strtotime($sesi['qr_expired_at']) < time()) {
            Response::json(['success' => false, 'message' => 'QR sudah kedaluwarsa. Minta guru refresh atau scan ulang.'], 410);
        }

        // 4. Cek kelas cocok
        if ((int) $sesi['kelas_id'] !== (int) $siswa['kelas_id']) {
            Response::json(['success' => false, 'message' => 'Sesi ini bukan untuk kelas Anda.'], 403);
        }

        // 5. Cek sudah presensi di sesi ini
        $sudah = DB::table('presensi_siswa')
            ->where('sesi_id', $sesi['id'])
            ->where('siswa_id', $siswa['id'])
            ->first();
        if ($sudah) {
            Response::json(['success' => false, 'message' => 'Anda sudah tercatat presensi di sesi ini.'], 409);
        }

        // 6. Cek token belum dipakai siswa ini (anti replay)
        $tokenHash = hash('sha256', $token);
        $sudahPakai = DB::rawOne("
            SELECT id FROM qr_token_usage
            WHERE sesi_id = ? AND token_hash = ? AND used_by_siswa_id = ?
            LIMIT 1
        ", [$sesi['id'], $tokenHash, $siswa['id']]);
        if ($sudahPakai) {
            Response::json(['success' => false, 'message' => 'QR ini sudah Anda pakai. Tunggu QR berikutnya.'], 409);
        }

        // 7. Simpan presensi + log
        try {
            DB::transaction(function () use ($sesi, $siswa, $token, $tokenHash) {
                DB::table('presensi_siswa')->insert([
                    'school_id' => current_school_id(),
                    'sesi_id' => $sesi['id'],
                    'siswa_id' => $siswa['id'],
                    'status' => 'hadir',
                    'metode' => 'scan',
                    'keterangan' => 'Scan QR',
                    'waktu' => date('Y-m-d H:i:s'),
                    'dicatat_oleh' => Auth::id(),
                ]);

                DB::table('qr_token_usage')->insert([
                    'sesi_id' => $sesi['id'],
                    'window' => (int) strtotime($sesi['qr_expired_at']),
                    'token_hash' => $tokenHash,
                    'used_by_siswa_id' => $siswa['id'],
                    'used_at' => date('Y-m-d H:i:s'),
                ]);
            });
        } catch (\PDOException $e) {
            // Kalau UNIQUE KEY uq_ps (sesi_id, siswa_id) kena, artinya sudah ada
            Response::json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }

        Response::json([
            'success' => true,
            'message' => 'Presensi berhasil! Anda tercatat HADIR.',
            'data' => [
                'mapel' => $sesi['jadwal_id'],
                'waktu' => date('H:i:s'),
            ],
        ]);
    }

    private function getSiswa(): ?array
    {
        return DB::rawOne("
            SELECT s.*, u.nama_lengkap, k.nama AS kelas_nama
            FROM siswa s
            JOIN users u ON s.user_id = u.id
            JOIN kelas k ON s.kelas_id = k.id
            WHERE s.user_id = ? AND s.school_id = ?
            LIMIT 1
        ", [Auth::id(), current_school_id()]);
    }
}