<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\DB;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Core\CSRF;

class DashboardController
{
    public function index(): void
    {
        $role = Auth::role();
        if ($role === 'admin') {
            $this->admin();
        } elseif ($role === 'guru') {
            $this->guru();
        } elseif ($role === 'siswa') {
            $this->siswa();
        } else {
            Response::redirect(url('login'));
        }
    }

       public function admin(): void
    {
        $schoolId = current_school_id();
        $taAktif = DB::table('tahun_ajaran')->where('school_id', $schoolId)->where('aktif', 1)->first();
        $namaTa = $taAktif ? $taAktif['nama'] : 'Belum Diatur';
        $taId = $taAktif ? $taAktif['id'] : 0;
        $tglHariIni = date('Y-m-d');

        // --- 1. STATISTIK UTAMA ---
        $totalGuru = DB::table('guru')->where('school_id', $schoolId)->where('aktif', 1)->count();
        $totalSiswa = DB::table('siswa')->where('school_id', $schoolId)->where('aktif', 1)->count();
        $totalKelas = DB::table('kelas')->where('school_id', $schoolId)->where('aktif', 1)->count();

        // --- 2. GURU HADIR HARI INI ---
        $guruHadirHariIni = DB::rawOne("
            SELECT COUNT(DISTINCT guru_id) as total 
            FROM presensi_guru_event 
            WHERE school_id = ? AND tanggal = ? AND tipe = 'datang'
        ", [$schoolId, $tglHariIni])['total'] ?? 0;

        // --- 3. STATISTIK SISWA HARI INI (Untuk Grafik Pie) ---
        // Menghitung status presensi siswa hari ini berdasarkan sesi yang sudah ditutup
        $statsSiswa = DB::rawOne("
            SELECT 
                COUNT(CASE WHEN status = 'hadir' THEN 1 END) as hadir,
                COUNT(CASE WHEN status = 'izin' THEN 1 END) as izin,
                COUNT(CASE WHEN status = 'sakit' THEN 1 END) as sakit,
                COUNT(CASE WHEN status = 'alpha' THEN 1 END) as alpha
            FROM presensi_siswa
            WHERE school_id = ? AND DATE(waktu) = ?
        ", [$schoolId, $tglHariIni]);

        $chartSiswa = [
            'hadir' => (int)($statsSiswa['hadir'] ?? 0),
            'izin'  => (int)($statsSiswa['izin'] ?? 0),
            'sakit' => (int)($statsSiswa['sakit'] ?? 0),
            'alpha' => (int)($statsSiswa['alpha'] ?? 0),
        ];

        // Jika belum ada data presensi siswa hari ini, tampilkan 0 (bukan dummy)
        if (array_sum($chartSiswa) === 0) {
            $chartSiswa = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        }

        // --- 4. TREN KEHADIRAN GURU (7 HARI TERAKHIR) ---
        $chartGuruLabels = [];
        $chartGuruData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = date('Y-m-d', strtotime("-{$i} days"));
            $chartGuruLabels[] = date('d M', strtotime($tgl));
            
            $totalHadir = DB::rawOne("
                SELECT COUNT(DISTINCT guru_id) as total 
                FROM presensi_guru_event 
                WHERE school_id = ? AND tanggal = ? AND tipe = 'datang'
            ", [$schoolId, $tgl])['total'] ?? 0;
            
            $chartGuruData[] = (int)$totalHadir;
        }

        View::render('dashboard.admin', [
            'title' => 'Dashboard Utama',
            'user'  => Auth::user(),
            'namaTa' => $namaTa,
            'stats' => [
                'guru' => $totalGuru,
                'siswa' => $totalSiswa,
                'kelas' => $totalKelas,
                'hadir_hari_ini' => $guruHadirHariIni,
            ],
            'chartSiswa' => $chartSiswa,
            'chartGuru' => ['labels' => $chartGuruLabels, 'data' => $chartGuruData]
        ], 'admin');
    }

    public function guru(): void
    {
        // 1. CEK APAKAH USER INI PUNYA DATA DI TABEL GURU
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        
        if (!$guru) {
            // JANGAN REDIRECT KE LOGOUT. Tampilkan error di layar!
            http_response_code(500);
            echo "<div style='padding:20px; font-family:sans-serif;'>";
            echo "<h1 style='color:red;'>BLOKIR! Data Guru Tidak Ditemukan di Database</h1>";
            echo "<p>Anda berhasil login sebagai <b>" . Auth::user()['nama_lengkap'] . "</b> (Role: " . Auth::role() . ").</p>";
            echo "<p>Tetapi sistem tidak menemukan baris data di tabel <code>guru</code> yang memiliki <code>user_id = " . Auth::id() . "</code>.</p>";
            echo "<p><b>Solusi:</b> Masuk sebagai Admin, buka menu Data Guru, lalu pastikan data guru ini tersimpan dengan benar (atau buat ulang).</p>";
            echo "<a href='" . url('logout') . "'>Klik di sini untuk Logout Manual</a>";
            echo "</div>";
            exit;
        }

        // 2. JIKA DATA GURU ADA, LANJUTKAN SEPERTI BIASA
        $hariIni = strtolower(date('l'));
        $mapHari = ['monday'=>'senin','tuesday'=>'selasa','wednesday'=>'rabu','thursday'=>'kamis','friday'=>'jumat','saturday'=>'sabtu','sunday'=>'minggu'];
        $hari = $mapHari[$hariIni] ?? 'senin';

        $sesiAktif = DB::rawOne("
            SELECT s.*, m.nama as mapel_nama, k.nama as kelas_nama 
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            WHERE s.school_id = ? AND s.dibuka_oleh = ? AND s.ditutup_at IS NULL
        ", [current_school_id(), Auth::id()]);

        $jadwalHariIni = DB::raw("
            SELECT j.id as jadwal_id, m.nama as mapel_nama, k.nama as kelas_nama, s.jam_mulai, s.jam_selesai, s.jam_ke
            FROM jadwal j
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN slot_jam s ON j.slot_jam_id = s.id
            WHERE j.school_id = ? AND p.guru_id = ? AND j.hari = ?
            ORDER BY s.jam_ke ASC
        ", [current_school_id(), $guru['id'], $hari]);

        View::render('dashboard.guru', [
            'title' => 'Dashboard Guru',
            'user'  => Auth::user(),
            'sesiAktif' => $sesiAktif,
            'jadwalHariIni' => $jadwalHariIni,
            'hari' => ucfirst($hari),
            'tanggal' => date('d F Y')
        ], 'guru'); 
    }

public function siswa(): void
{
    $siswa = DB::rawOne("
        SELECT s.*, u.nama_lengkap, k.nama AS kelas_nama
        FROM siswa s
        JOIN users u ON s.user_id = u.id
        JOIN kelas k ON s.kelas_id = k.id
        WHERE s.user_id = ? AND s.school_id = ?
        LIMIT 1
    ", [Auth::id(), current_school_id()]);

    if (!$siswa) {
        \App\Core\Session::toast('error', 'Data siswa tidak ditemukan.');
        Response::redirect(url('login'));
    }

    $sesiAktif = DB::rawOne("
        SELECT 
            s.id, 
            m.nama AS mapel_nama, 
            k.nama AS kelas_nama,
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

    // Buat SSO token untuk e-library (berlaku 5 menit)
    $ssoToken = bin2hex(random_bytes(32));
    DB::table('sso_tokens')->insert([
        'user_id'     => Auth::id(),
        'token'       => $ssoToken,
        'school_slug' => 'mtsmuhwangon', // Slug sekolah di e-library
        'expires_at'  => date('Y-m-d H:i:s', strtotime('+5 minutes'))
    ]);

    View::render('dashboard.siswa', [
        'title' => 'Dashboard Siswa',
        'user' => Auth::user(),
        'siswa' => $siswa,
        'sesiAktif' => $sesiAktif,
        'ssoToken' => $ssoToken,
    ], 'siswa');
}

public function aboutSiswa(): void
{
    View::render('siswa.about', ['title' => 'Tentang Aplikasi'], 'siswa');
}

    public function profileSiswa(): void
    {
        $siswa = DB::rawOne("
            SELECT s.*, u.nama_lengkap, u.no_hp,
                k.nama AS kelas_nama, k.tingkat, k.wali_kelas_id
            FROM siswa s
            JOIN users u ON s.user_id = u.id
            JOIN kelas k ON s.kelas_id = k.id
            WHERE s.user_id = ? AND s.school_id = ?
            LIMIT 1
        ", [Auth::id(), current_school_id()]);

        $waliKelas = null;
        if ($siswa && !empty($siswa['wali_kelas_id'])) {
            $row = DB::rawOne("
                SELECT u.nama_lengkap
                FROM guru g JOIN users u ON g.user_id = u.id
                WHERE g.id = ? LIMIT 1
            ", [$siswa['wali_kelas_id']]);
            $waliKelas = $row['nama_lengkap'] ?? null;
        }

        View::render('siswa.profile', [
            'title' => 'Profil Saya',
            'user' => Auth::user(),
            'siswa' => $siswa,
            'waliKelas' => $waliKelas,   // <-- kirim ke view
        ], 'siswa');
    }
    public const AVATARS = [
        'avatar-01.svg', 'avatar-02.svg', 'avatar-03.svg',
        'avatar-04.svg', 'avatar-05.svg', 'avatar-06.svg',
        'avatar-07.svg', 'avatar-08.svg', 'avatar-09.svg',
        'avatar-10.svg', 'avatar-11.svg', 'avatar-12.svg',
    ];

    public function updateAvatar(): void
    {
        CSRF::check();

        if (Auth::role() !== 'siswa') {
            http_response_code(403);
            exit('Forbidden');
        }

        $avatar = trim((string) ($_POST['avatar'] ?? ''));

        if (!in_array($avatar, self::AVATARS, true)) {
            Session::toast('error', 'Karakter tidak valid.');
            Response::redirect(url('siswa/profile'));
        }

        DB::table('users')
            ->where('id', Auth::id())
            ->update(['avatar' => $avatar]);

        Session::toast('success', 'Karakter berhasil diganti.');
        Response::redirect(url('siswa/profile'));
    }


        public function about(): void
    {
        View::render('guru.about', ['title' => 'Tentang Aplikasi'], 'guru');
    }

       public function profile(): void
    {
        $user = Auth::user();
        
        // Ambil data guru berdasarkan user_id
        $guru = DB::table('guru')->where('user_id', $user['id'])->first();
        
        $nip = $guru['nip'] ?? '-';
        $noHp = $user['no_hp'] ?? '-';
        
        // Cek apakah dia wali kelas
        $waliKelas = null;
        if ($guru) {
            $waliKelas = DB::table('kelas')->where('wali_kelas_id', $guru['id'])->where('school_id', current_school_id())->first();
        }

        // Ambil mapel yang diampu
        $mapelDiampu = [];
        if ($guru) {
            $mapelDiampu = DB::raw("
                SELECT DISTINCT m.nama 
                FROM pengampu p
                JOIN mapel m ON p.mapel_id = m.id
                WHERE p.guru_id = ? AND p.school_id = ?
            ", [$guru['id'], current_school_id()]);
        }

        View::render('guru.profile', [
            'title' => 'Profil Saya', 
            'user' => $user,
            'nip' => $nip,
            'noHp' => $noHp,
            'waliKelas' => $waliKelas ? $waliKelas['nama'] : 'Bukan Wali Kelas',
            'mapelDiampu' => $mapelDiampu
        ], 'guru');
    }
    public function gantiTahunAjaran(): void
    {
        CSRF::check();
        $taId = (int) $_POST['tahun_ajaran_id'];
        
        // 1. Matikan semua
        DB::table('tahun_ajaran')->where('school_id', current_school_id())->update(['aktif' => 0]);
        
        // 2. Nyalakan yang dipilih
        DB::table('tahun_ajaran')->where('id', $taId)->update(['aktif' => 1]);
        
        \App\Core\Session::toast('success', 'Tahun ajaran berhasil diubah.');
        \App\Core\Response::back();
    }

        // ==========================================
    // FITUR MONITORING SESI
    // ==========================================

    public function monitoringSesi(): void
    {
        // Ambil semua sesi yang belum ditutup (ditutup_at IS NULL)
        $sesiAktif = DB::raw("
            SELECT 
                s.id as sesi_id,
                s.dibuka_at,
                s.mode,
                m.nama as mapel_nama,
                k.nama as kelas_nama,
                u.nama_lengkap as guru_nama,
                sj.jam_mulai,
                sj.jam_selesai,
                sj.jam_ke
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN guru g ON s.dibuka_oleh = g.user_id
            JOIN users u ON g.user_id = u.id
            JOIN slot_jam sj ON j.slot_jam_id = sj.id
            WHERE s.school_id = ? AND s.ditutup_at IS NULL
            ORDER BY s.dibuka_at DESC
        ", [current_school_id()]);

        View::render('admin.monitoring_sesi', [
            'title' => 'Monitoring Sesi',
            'sesiAktif' => $sesiAktif
        ], 'admin');
    }

    public function tutupSesiPaksa(int $id): void
    {
        CSRF::check();

        // GANTI BAGIAN INI: Gunakan rawOne dengan IS NULL
        $sesi = DB::rawOne("
            SELECT * FROM sesi_presensi 
            WHERE id = ? 
              AND school_id = ? 
              AND ditutup_at IS NULL
        ", [$id, current_school_id()]);

        if (!$sesi) {
            Session::toast('error', 'Sesi tidak ditemukan atau sudah ditutup.');
            Response::redirect(url('admin/monitoring-sesi'));
        }

        DB::transaction(function() use ($id, $sesi) {
            // 1. Tutup Sesi
            DB::table('sesi_presensi')->where('id', $id)->update([
                'ditutup_at' => date('Y-m-d H:i:s'),
                'closed_by' => 'admin' // Tandai bahwa ini ditutup paksa oleh admin
            ]);

            // 2. Opsional: Tandai siswa yang belum absen sebagai Alpha
            // Cari siswa di kelas tersebut (berdasarkan riwayat_kelas TA aktif)
            $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
            if ($taAktif && $sesi['jadwal_id']) {
                // Ambil kelas_id dari jadwal
                $jadwal = DB::table('jadwal')->where('id', $sesi['jadwal_id'])->first();
                $pengampu = DB::table('pengampu')->where('id', $jadwal['pengampu_id'])->first();
                
                if ($pengampu) {
                    $siswaKelas = DB::raw("
                        SELECT siswa_id FROM riwayat_kelas 
                        WHERE tahun_ajaran_id = ? AND kelas_id = ?
                    ", [$taAktif['id'], $pengampu['kelas_id']]);

                    foreach ($siswaKelas as $s) {
                        // Cek apakah siswa ini sudah punya presensi di sesi ini
                        $existing = DB::table('presensi_siswa')
                            ->where('sesi_id', $id)
                            ->where('siswa_id', $s['siswa_id'])
                            ->first();
                        
                        if (!$existing) {
                            DB::table('presensi_siswa')->insert([
                                'school_id' => current_school_id(),
                                'sesi_id' => $id,
                                'siswa_id' => $s['siswa_id'],
                                'status' => 'alpha',
                                'metode' => 'manual_admin',
                                'waktu' => date('Y-m-d H:i:s'),
                                'dicatat_oleh' => Auth::id()
                            ]);
                        }
                    }
                }
            }
        });

        Session::toast('success', 'Sesi berhasil ditutup paksa dan siswa yang belum absen ditandai Alpha.');
        Response::redirect(url('admin/monitoring-sesi'));
    }
}