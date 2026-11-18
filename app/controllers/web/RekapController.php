<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\DB;
use App\Core\View;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
class RekapController
{
    // Halaman Rekap Presensi Siswa
        // Halaman Rekap Presensi Siswa
       // Halaman Rekap Presensi Siswa
    public function siswa(): void
    {
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        
        $kelasWali = DB::table('kelas')->where('wali_kelas_id', $guru['id'])->where('school_id', current_school_id())->first();
        
        if (!$kelasWali) {
            $kelasWali = DB::rawOne("SELECT k.id, k.nama FROM kelas k JOIN pengampu p ON k.id = p.kelas_id WHERE p.guru_id = ? LIMIT 1", [$guru['id']]);
        }

        if (!$kelasWali) {
            Session::toast('error', 'Anda tidak terdaftar sebagai wali kelas atau pengampu.');
            Response::redirect(url('guru'));
        }

        // Filter Bulan & Tahun
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');

        // --- PAGINATION LOGIC ---
        $perPage = 10; // Tampilkan 10 hari per halaman
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        // Hitung Total Hari Unik (yang ada sesinya)
        $totalHari = DB::rawOne("
            SELECT COUNT(DISTINCT s.tanggal) as total 
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            WHERE p.kelas_id = ? AND s.school_id = ? 
            AND MONTH(s.tanggal) = ? AND YEAR(s.tanggal) = ?
        ", [$kelasWali['id'], current_school_id(), $bulan, $tahun])['total'] ?? 0;

        $lastPage = (int) ceil($totalHari / $perPage);

        // Ambil tanggal dengan Limit & Offset
        $tanggalAdaSesi = DB::raw("
            SELECT DISTINCT s.tanggal 
            FROM sesi_presensi s
            JOIN jadwal j ON s.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            WHERE p.kelas_id = ? AND s.school_id = ? 
            AND MONTH(s.tanggal) = ? AND YEAR(s.tanggal) = ?
            ORDER BY s.tanggal DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", [$kelasWali['id'], current_school_id(), $bulan, $tahun]);

        // Array nama hari Indonesia
        $hariIndo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];

        // Ambil summary per tanggal
        $rekapHarian = [];
        foreach ($tanggalAdaSesi as $row) {
            $tgl = $row['tanggal'];
            
            $sql = "SELECT pr.status, COUNT(*) as jumlah 
                    FROM presensi_siswa pr
                    JOIN siswa sw ON pr.siswa_id = sw.id
                    WHERE sw.kelas_id = ? AND pr.school_id = ? AND DATE(pr.waktu) = ?
                    GROUP BY pr.status";
            
            $stats = DB::raw($sql, [$kelasWali['id'], current_school_id(), $tgl]);
            
            $summary = ['hadir' => 0, 'alpha' => 0, 'izin' => 0, 'sakit' => 0];
            foreach ($stats as $s) {
                $summary[$s['status']] = $s['jumlah'];
            }

            $hariEng = date('l', strtotime($tgl));

            $rekapHarian[] = [
                'tanggal' => $tgl,
                'hari' => $hariIndo[$hariEng] ?? $hariEng, // Pakai Bahasa Indonesia
                'summary' => $summary
            ];
        }

        View::render('guru.rekap_siswa', [
            'title' => 'Rekap Presensi Siswa',
            'kelas' => $kelasWali,
            'rekapHarian' => $rekapHarian,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'page' => $page,
            'lastPage' => $lastPage,
            'totalHari' => $totalHari
        ], 'guru');
    }

    // Download PDF
    public function siswaPdf(): void
    {
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        $kelasWali = DB::table('kelas')->where('wali_kelas_id', $guru['id'])->where('school_id', current_school_id())->first();
        
        if (!$kelasWali) {
            $kelasWali = DB::rawOne("SELECT k.id, k.nama FROM kelas k JOIN pengampu p ON k.id = p.kelas_id WHERE p.guru_id = ? LIMIT 1", [$guru['id']]);
        }

        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        // Ambil data siswa dan presensinya di tanggal tersebut
        $dataPresensi = DB::raw("
            SELECT sw.nis, u.nama_lengkap, pr.status, pr.keterangan 
            FROM siswa sw
            JOIN users u ON sw.user_id = u.id
            LEFT JOIN presensi_siswa pr ON sw.id = pr.siswa_id AND DATE(pr.waktu) = ?
            WHERE sw.kelas_id = ? AND sw.aktif = 1
            ORDER BY u.nama_lengkap ASC
        ", [$tanggal, $kelasWali['id']]);

        // Generate HTML untuk PDF
        $html = '
        <html><head><style>
            body { font-family: sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .text-center { text-align: center; }
        </style></head><body>
            <h2 style="text-align:center;">Rekap Presensi Siswa</h2>
            <p><strong>Kelas:</strong> ' . $kelasWali['nama'] . ' | <strong>Tanggal:</strong> ' . date('d F Y', strtotime($tanggal)) . '</p>
            <table>
                <thead><tr><th class="text-center">No</th><th>NIS</th><th>Nama Lengkap</th><th class="text-center">Status</th><th>Keterangan</th></tr></thead>
                <tbody>';
        
        $no = 1;
        foreach ($dataPresensi as $d) {
            $status = $d['status'] ?? 'Belum Ada Sesi';
            $html .= '<tr>
                        <td class="text-center">' . $no++ . '</td>
                        <td>' . $d['nis'] . '</td>
                        <td>' . $d['nama_lengkap'] . '</td>
                        <td class="text-center"><strong>' . ucfirst($status) . '</strong></td>
                        <td>' . ($d['keterangan'] ?? '-') . '</td>
                      </tr>';
        }
        
        $html .= '</tbody></table></body></html>';

        // Render ke PDF pakai Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Rekap_Presensi_' . $kelasWali['nama'] . '_' . $tanggal . '.pdf', ['Attachment' => false]);
        exit;
    }

    // Halaman Rekap Presensi Guru (Diri Sendiri)
        // Halaman Rekap Presensi Guru (Diri Sendiri)
        // Halaman Rekap Presensi Guru (Diri Sendiri)
    public function guru(): void
    {
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        
        // Filter Bulan, Tahun, dan Pencarian Tanggal
        $bulan = $_GET['bulan'] ?? date('m');
        $tahun = $_GET['tahun'] ?? date('Y');
        $searchTanggal = $_GET['tanggal'] ?? '';

        $sql = "SELECT * FROM presensi_guru_event 
                WHERE school_id = ? AND guru_id = ? 
                AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
        $bindings = [current_school_id(), $guru['id'], $bulan, $tahun];

        // Jika guru mencari tanggal spesifik
        if ($searchTanggal !== '') {
            $sql .= " AND tanggal = ?";
            $bindings[] = $searchTanggal;
        }

        $sql .= " ORDER BY tanggal DESC, waktu DESC";
        $presensi = DB::raw($sql, $bindings);

        View::render('guru.rekap_guru', [
            'title' => 'Rekap Presensi Saya',
            'presensi' => $presensi,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'searchTanggal' => $searchTanggal
        ], 'guru');
    }

    // Menampilkan gambar dari folder storage secara aman
    public function showFoto(string $filename): void
    {
        // Tentukan folder dasar (sesuaikan dengan struktur folder project-mu)
        $baseDir = __DIR__ . '/../../../storage/presensi/'; 
        
        // Cari file secara rekursif (karena file mungkin ada di dalam folder YYYY-MM)
        $files = glob($baseDir . '**/' . $filename);
        $realPath = !empty($files) ? $files[0] : $baseDir . $filename;

        if (!file_exists($realPath)) {
            http_response_code(404);
            exit('File foto tidak ditemukan.');
        }

        $mime = mime_content_type($realPath);
        header('Content-Type: ' . $mime);
        readfile($realPath);
        exit;
    }
   public function siswaMandiri(): void
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
            \App\Core\Response::redirect(url('dashboard'));
        }

        $bulan = (int) ($_GET['bulan'] ?? date('m'));
        $tahun = (int) ($_GET['tahun'] ?? date('Y'));

        $rekap = DB::raw("
            SELECT ps.waktu, ps.status, ps.metode, ps.keterangan,
                m.nama AS mapel_nama, k.nama AS kelas_nama,
                ug.nama_lengkap AS guru_nama
            FROM presensi_siswa ps
            JOIN sesi_presensi sp ON ps.sesi_id = sp.id
            JOIN jadwal j ON sp.jadwal_id = j.id
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN guru g ON p.guru_id = g.id
            JOIN users ug ON g.user_id = ug.id
            WHERE ps.school_id = ? AND ps.siswa_id = ?
            AND MONTH(ps.waktu) = ? AND YEAR(ps.waktu) = ?
            ORDER BY ps.waktu DESC
        ", [current_school_id(), $siswa['id'], $bulan, $tahun]);

        $summary = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        foreach ($rekap as $r) {
            $summary[$r['status']] = ($summary[$r['status']] ?? 0) + 1;
        }

        View::render('siswa.rekap', [
            'title' => 'Rekap Kehadiran Saya',
            'siswa' => $siswa,
            'rekap' => $rekap,
            'summary' => $summary,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ], 'siswa');
    }
    // ==========================================
    // FITUR REKAP GLOBAL ADMIN
    // ==========================================

    public function global(): void
    {
        $bulan = Request::string('bulan', date('m'));
        $tahun = Request::string('tahun', date('Y'));

        // Ambil daftar kelas
        $kelasList = DB::raw("SELECT id, nama, tingkat FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY tingkat ASC, nama ASC", [current_school_id()]);

        // Hitung total kehadiran per kelas pada bulan & tahun terpilih
        $rekapData = [];
        foreach ($kelasList as $k) {
            // Ambil semua siswa di kelas ini pada TA aktif
            $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
            $taId = $taAktif ? $taAktif['id'] : 0;

            // Query hitung status
            $stats = DB::rawOne("
                SELECT 
                    COUNT(CASE WHEN ps.status = 'hadir' THEN 1 END) as hadir,
                    COUNT(CASE WHEN ps.status = 'izin' THEN 1 END) as izin,
                    COUNT(CASE WHEN ps.status = 'sakit' THEN 1 END) as sakit,
                    COUNT(CASE WHEN ps.status = 'alpha' THEN 1 END) as alpha,
                    COUNT(ps.id) as total
                FROM presensi_siswa ps
                JOIN sesi_presensi sp ON ps.sesi_id = sp.id
                JOIN siswa s ON ps.siswa_id = s.id
                JOIN riwayat_kelas rk ON s.id = rk.siswa_id AND rk.tahun_ajaran_id = sp.tahun_ajaran_id
                WHERE ps.school_id = ? 
                  AND rk.kelas_id = ?
                  AND MONTH(ps.waktu) = ? 
                  AND YEAR(ps.waktu) = ?
            ", [current_school_id(), $k['id'], $bulan, $tahun]);

            $persentase = ($stats['total'] > 0) ? round(($stats['hadir'] / $stats['total']) * 100, 1) : 0;

            $rekapData[] = [
                'kelas_id' => $k['id'],
                'kelas_nama' => $k['nama'],
                'hadir' => $stats['hadir'] ?? 0,
                'izin' => $stats['izin'] ?? 0,
                'sakit' => $stats['sakit'] ?? 0,
                'alpha' => $stats['alpha'] ?? 0,
                'total' => $stats['total'] ?? 0,
                'persentase' => $persentase
            ];
        }

        View::render('admin.rekap_global', [
            'title' => 'Rekap Global Kehadiran',
            'rekapData' => $rekapData,
            'bulan' => $bulan,
            'tahun' => $tahun
        ], 'admin');
    }
}