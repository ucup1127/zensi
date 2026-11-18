<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class PresensiSiswaController
{
    // Halaman buka sesi (form pilih jadwal)
    // Halaman buka sesi (form pilih jadwal)
    public function bukaSesi(): void
    {
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        
        // Lazy Close: Tutup sesi lama jika ada
        $this->lazyCloseExpired($guru['id']);

        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();

        // AMBIL SEMUA JADWAL GURU INI (TANPA FILTER HARI)
        $jadwalGuru = DB::raw("
            SELECT j.id as jadwal_id, m.nama as mapel_nama, k.nama as kelas_nama, 
                   s.jam_mulai, s.jam_selesai, s.jam_ke, j.hari
            FROM jadwal j
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN slot_jam s ON j.slot_jam_id = s.id
            WHERE j.school_id = ? AND p.guru_id = ? AND j.tahun_ajaran_id = ?
            ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), s.jam_ke ASC
        ", [current_school_id(), $guru['id'], $taAktif['id']]);

        View::render('guru.buka_sesi', [
            'title' => 'Buka Sesi Presensi',
            'jadwalGuru' => $jadwalGuru,
        ], 'guru'); // Gunakan layout guru yang baru
    }

    // Aksi membuka sesi baru
       // Aksi membuka sesi baru
    public function storeSesi(): void
    {
        CSRF::check();
        $jadwalId = Request::int('jadwal_id');
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        $hariIni = date('Y-m-d');
        $metode = $_POST['metode'] ?? 'manual'; 
         
        // 1. Cek Hari Libur
        if (DB::table('hari_libur')->where('school_id', current_school_id())->where('tanggal', $hariIni)->exists()) {
            Session::toast('error', 'Hari ini adalah hari libur. Tidak bisa membuka sesi.');
            Response::redirect(url('guru/buka-sesi'));
        }

        // 2. Auto-tutup sesi lama (Satu guru satu sesi aktif)
        $sesiLama = DB::table('sesi_presensi')->where('school_id', current_school_id())->where('dibuka_oleh', Auth::id())->whereNull('ditutup_at')->first();
        if ($sesiLama) {
            DB::table('sesi_presensi')->where('id', $sesiLama['id'])->update([
                'ditutup_at' => date('Y-m-d H:i:s'),
                'closed_by' => 'system_auto_next_session'
            ]);
            $this->autoAlpha($sesiLama['id'], $sesiLama['jadwal_id']);
        }

        // 3. CEK: Apakah sesi untuk jadwal ini di tanggal ini sudah pernah dibuat sebelumnya?
        $sesiHariIni = DB::table('sesi_presensi')
            ->where('school_id', current_school_id())
            ->where('jadwal_id', $jadwalId)
            ->where('tanggal', $hariIni)
            ->first();

        if ($sesiHariIni) {
            // JIKA SUDAH ADA: Buka kembali sesi tersebut (hapus tanggal tutupnya)
            $sesiId = $sesiHariIni['id'];
            DB::table('sesi_presensi')->where('id', $sesiId)->update([
                'ditutup_at' => null,
                'dibuka_at' => date('Y-m-d H:i:s'),
                'dibuka_oleh' => Auth::id()
            ]);
            
            // Hapus presensi siswa lama di sesi ini agar guru bisa ceklis ulang dari awal
            DB::table('presensi_siswa')->where('sesi_id', $sesiId)->delete();
            
            Session::toast('info', 'Sesi dibuka kembali. Presensi sebelumnya telah di-reset.');
        } else {
            // JIKA BELUM ADA: Buat sesi baru
            $jadwal = DB::rawOne("SELECT j.*, p.guru_id FROM jadwal j JOIN pengampu p ON j.pengampu_id = p.id WHERE j.id = ?", [$jadwalId]);
            
                $sesiId = DB::table('sesi_presensi')->insert([
                'school_id' => current_school_id(),
                'tahun_ajaran_id' => $taAktif['id'],
                'jadwal_id' => $jadwalId,
                'tanggal' => $hariIni,
                'guru_pemilik_jadwal_id' => $jadwal['guru_id'],
                'dibuka_oleh' => Auth::id(),
                'is_pengganti' => ($jadwal['guru_id'] != $guru['id']) ? 1 : 0,
                'guru_pengganti_id' => ($jadwal['guru_id'] != $guru['id']) ? $guru['id'] : null,
                'mode' => 'manual',
                'metode_buka' => $metode, // <--- UBAH 'hybrid' MENJADI $metode
                'dibuka_at' => date('Y-m-d H:i:s')
            ]);
            Session::toast('success', 'Sesi baru berhasil dibuka.');
        }

         if ($metode === 'qr') {
            Response::redirect(url("guru/sesi-qr/{$sesiId}"));
        } else {
            Response::redirect(url("guru/presensi/{$sesiId}"));
        }
    }
    // Halaman Ceklis Presensi
    public function show(mixed $sesiId): void
    {
        $sesiId = (int) $sesiId; // Konversi paksa ke integer
        
        $sesi = DB::rawOne("
            SELECT s.*, m.nama as mapel_nama, k.id as kelas_id, k.nama as kelas_nama, sl.jam_mulai, sl.jam_selesai
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN slot_jam sl ON j.slot_jam_id = sl.id
            WHERE s.id = ? AND s.school_id = ?
        ", [$sesiId, current_school_id()]);

        if (!$sesi || $sesi['ditutup_at'] !== null) {
            Session::toast('error', 'Sesi tidak ditemukan atau sudah ditutup.');
            Response::redirect(url('dashboard'));
        }

        // Ambil siswa di kelas tersebut
        $siswa = DB::raw("
            SELECT s.id, s.nis, u.nama_lengkap 
            FROM siswa s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.kelas_id = ? AND s.aktif = 1 
            ORDER BY u.nama_lengkap ASC
        ", [$sesi['kelas_id']]);

        // Ambil izin siswa hari ini (jika ada)
        $izin = DB::raw("
            SELECT siswa_id, jenis, alasan FROM izin_siswa 
            WHERE school_id = ? AND tanggal = ? AND siswa_id IN (SELECT id FROM siswa WHERE kelas_id = ?)
        ", [current_school_id(), $sesi['tanggal'], $sesi['kelas_id']]);

        $izinMap = [];
        foreach ($izin as $i) {
            $izinMap[$i['siswa_id']] = $i;
        }

        View::render('guru.ceklis_presensi', [
            'title' => 'Presensi Siswa',
            'sesi' => $sesi,
            'siswa' => $siswa,
            'izinMap' => $izinMap
        ], 'guru');
    }
    // Halaman Tampil QR Code
    public function showQr(mixed $sesiId): void
    {
        $sesiId = (int) $sesiId;
        
        $sesi = DB::rawOne("
            SELECT s.*, m.nama as mapel_nama, k.nama as kelas_nama 
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            WHERE s.id = ? AND s.school_id = ?
        ", [$sesiId, current_school_id()]);

        if (!$sesi || $sesi['ditutup_at'] !== null) {
            Session::toast('error', 'Sesi tidak ditemukan atau sudah ditutup.');
            Response::redirect(url('guru'));
        }

        View::render('guru.qr_presensi', [
            'title' => 'QR Code Presensi',
            'sesi' => $sesi
        ], 'guru');
    }

    // Endpoint AJAX untuk Generate Token & Gambar QR Baru
    public function getQrToken(mixed $sesiId): void
    {
        $sesiId = (int) $sesiId;
        
        // 1. Buat token acak unik
        $token = bin2hex(random_bytes(16)); 
        $waktuKedaluwarsa = date('Y-m-d H:i:s', strtotime('+10 seconds')); // Berlaku 10 detik

        // 2. Simpan token ke database
        // Asumsi kita simpan di tabel sesi_presensi (tambahkan kolom qr_token & qr_expired_at)
        // Jika belum ada kolomnya, jalankan query ALTER TABLE di phpMyAdmin
        DB::table('sesi_presensi')->where('id', $sesiId)->update([
            'qr_token' => $token,
            'qr_expired_at' => $waktuKedaluwarsa
        ]);

        // 3. Generate Gambar QR Code menggunakan library endroid/qr-code
        $qrCode = new \Endroid\QrCode\QrCode(
            data: $token,
            size: 300,
            margin: 10
        );
        
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        
        // 4. Kembalikan respons JSON berisi Base64 gambar
        Response::json([
            'success' => true,
            'token' => $token,
            'qr_image' => $result->getDataUri() // Menghasilkan data:image/png;base64,...
        ]);
    }

    // Aksi Simpan & Tutup Sesi
    public function simpan(int $sesiId): void
    {
        CSRF::check();
        $presensiData = $_POST['presensi'] ?? []; // Array [siswa_id => ['status' => 'hadir', 'keterangan' => '...']]

        // Validasi: Izin wajib keterangan
        foreach ($presensiData as $siswaId => $data) {
            if ($data['status'] === 'izin' && empty(trim($data['keterangan']))) {
                Session::toast('error', 'Keterangan izin wajib diisi untuk semua siswa yang dipilih Izin.');
                Response::redirect(url("guru/presensi/{$sesiId}"));
            }
        }

        DB::transaction(function() use ($sesiId, $presensiData) {
            $sesi = DB::table('sesi_presensi')->where('id', $sesiId)->first();

            foreach ($presensiData as $siswaId => $data) {
                $status = $data['status'];
                $ket = $data['keterangan'] ?? null;

                DB::table('presensi_siswa')->insert([
                    'school_id' => current_school_id(),
                    'sesi_id' => $sesiId,
                    'siswa_id' => $siswaId,
                    'status' => $status,
                    'metode' => 'manual',
                    'keterangan' => $ket,
                    'waktu' => date('Y-m-d H:i:s'),
                    'dicatat_oleh' => Auth::id()
                ]);
            }

            // Tutup Sesi
            DB::table('sesi_presensi')->where('id', $sesiId)->update([
                'ditutup_at' => date('Y-m-d H:i:s'),
                'closed_by' => 'guru'
            ]);

            // Auto Alpha untuk yang tidak disentuh
            $this->autoAlpha($sesiId, $sesi['jadwal_id']);
        });

        Session::toast('success', 'Presensi berhasil disimpan dan sesi ditutup.');
        Response::redirect(url('dashboard'));
    }

    // Helper: Auto Alpha
    private function autoAlpha(int $sesiId, int $jadwalId): void
    {
        $sesi = DB::table('sesi_presensi')->where('id', $sesiId)->first();
        $kelas = DB::rawOne("SELECT p.kelas_id FROM jadwal j JOIN pengampu p ON j.pengampu_id = p.id WHERE j.id = ?", [$jadwalId]);
        
        $siswa = DB::raw("SELECT id FROM siswa WHERE kelas_id = ? AND aktif = 1", [$kelas['kelas_id']]);
        
        foreach ($siswa as $s) {
            $exists = DB::table('presensi_siswa')->where('sesi_id', $sesiId)->where('siswa_id', $s['id'])->exists();
            if (!$exists) {
                DB::table('presensi_siswa')->insert([
                    'school_id' => current_school_id(),
                    'sesi_id' => $sesiId,
                    'siswa_id' => $s['id'],
                    'status' => 'alpha',
                    'metode' => 'manual',
                    'waktu' => date('Y-m-d H:i:s'),
                    'dicatat_oleh' => Auth::id()
                ]);
            }
        }
    }

    // Helper: Lazy Close (Tutup otomatis jika lewat jam)
    private function lazyCloseExpired(int $guruId): void
    {
        $sesiAktif = DB::raw("
            SELECT s.id, s.jadwal_id, sl.jam_selesai 
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN slot_jam sl ON j.slot_jam_id = sl.id
            WHERE s.school_id = ? AND s.dibuka_oleh = ? AND s.ditutup_at IS NULL
        ", [current_school_id(), Auth::id()]);

        foreach ($sesiAktif as $s) {
            $batasWaktu = strtotime($s['jam_selesai']) + (30 * 60); // Buffer 30 menit
            if (time() > $batasWaktu) {
                DB::table('sesi_presensi')->where('id', $s['id'])->update([
                    'ditutup_at' => date('Y-m-d H:i:s'),
                    'closed_by' => 'system_timeout'
                ]);
                $this->autoAlpha($s['id'], $s['jadwal_id']);
            }
        }
    }
}