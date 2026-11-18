<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{CSRF, DB, Request, Response, Session, View, Auth};
class JadwalController
{
    public function index(): void
    {
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        if (!$taAktif) {
            Session::toast('error', 'Silakan aktifkan Tahun Ajaran terlebih dahulu.');
            Response::redirect(url('admin/tahun-ajaran'));
        }

        $search = Request::string('q');
        $filterKelas = Request::int('filter_kelas');
        $perPage = Request::int('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20, 50, 100])) $perPage = 10;

        $sqlBase = "FROM jadwal j
                    JOIN slot_jam s ON j.slot_jam_id = s.id
                    JOIN pengampu p ON j.pengampu_id = p.id
                    JOIN mapel m ON p.mapel_id = m.id
                    JOIN kelas k ON p.kelas_id = k.id
                    JOIN guru g ON p.guru_id = g.id
                    JOIN users u ON g.user_id = u.id
                    WHERE j.school_id = ? AND j.tahun_ajaran_id = ?";
        $bindings = [current_school_id(), $taAktif['id']];

        if ($search !== '') {
            $sqlBase .= " AND (m.nama LIKE ? OR u.nama_lengkap LIKE ?)";
            $bindings[] = "%{$search}%"; $bindings[] = "%{$search}%";
        }
        if ($filterKelas > 0) {
            $sqlBase .= " AND p.kelas_id = ?";
            $bindings[] = $filterKelas;
        }

        $total = (int) DB::rawOne("SELECT COUNT(*) as c " . $sqlBase, $bindings)['c'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage; $lastPage = (int) ceil($total / $perPage);
        
        $data = DB::raw("SELECT j.*, s.jam_mulai as slot_mulai, s.jam_selesai as slot_selesai, s.jam_ke, m.nama as mapel_nama, k.nama as kelas_nama, u.nama_lengkap as guru_nama " . $sqlBase . " ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), s.jam_ke ASC LIMIT {$perPage} OFFSET {$offset}", $bindings);

        $paginator = ['data' => $data, 'total' => $total, 'current_page' => $page, 'last_page' => $lastPage, 'from' => $total > 0 ? $offset + 1 : 0, 'to' => min($offset + $perPage, $total), 'search' => $search, 'filter_kelas' => $filterKelas, 'per_page' => $perPage];

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['html' => $this->renderTableBody($paginator), 'pagination_html' => $this->renderPagination($paginator), 'info' => "Menampilkan {$paginator['from']} - {$paginator['to']} dari {$paginator['total']} data"]);
            exit;
        }

        $kelasList = DB::raw("SELECT id, nama FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);
        $guruList  = DB::raw("SELECT g.id, u.nama_lengkap FROM guru g JOIN users u ON g.user_id = u.id WHERE g.school_id = ? AND g.aktif = 1 ORDER BY u.nama_lengkap", [current_school_id()]);
        $mapelList = DB::raw("SELECT id, nama FROM mapel WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);
        $slotList  = DB::raw("SELECT * FROM slot_jam WHERE school_id = ? ORDER BY jam_ke", [current_school_id()]);

        View::render('admin.jadwal.index', [
            'title' => 'Jadwal Pelajaran', 
            'paginator' => $paginator, 
            'taAktif' => $taAktif, 
            'kelasList' => $kelasList,
            'guruList' => $guruList,     // <-- Kirim ini
            'mapelList' => $mapelList,   // <-- Kirim ini
            'slotList' => $slotList      // <-- Kirim ini
        ], 'admin');
    }

        // Halaman Jadwal Khusus Guru
    public function jadwalGuru(): void
    {
        $guru = DB::table('guru')->where('user_id', Auth::id())->first();
        if (!$guru) {
            Session::toast('error', 'Data guru tidak ditemukan.');
            Response::redirect(url('dashboard'));
        }

        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();

        // Ambil semua jadwal guru ini, urutkan berdasarkan hari dan jam
        $jadwal = DB::raw("
            SELECT j.id as jadwal_id, j.hari, m.nama as mapel_nama, k.nama as kelas_nama, 
                   s.jam_mulai, s.jam_selesai, s.jam_ke
            FROM jadwal j
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN slot_jam s ON j.slot_jam_id = s.id
            WHERE j.school_id = ? AND p.guru_id = ? AND j.tahun_ajaran_id = ?
            ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), s.jam_ke ASC
        ", [current_school_id(), $guru['id'], $taAktif['id']]);

        // Kelompokkan berdasarkan hari
        $grouped = [];
        foreach ($jadwal as $j) {
            $grouped[strtolower($j['hari'])][] = $j;
        }

        View::render('guru.jadwal', [
            'title' => 'Jadwal Mengajar',
            'groupedJadwal' => $grouped
        ], 'guru');
    }

    private function renderTableBody(array $paginator): string {
        ob_start(); ?>
        <?php if (empty($paginator['data'])): ?><tr><td colspan="6" class="py-8 text-center text-slate-500">Belum ada jadwal.</td></tr><?php else: ?>
            <?php foreach ($paginator['data'] as $j): ?>
                                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4 text-slate-600 text-xs whitespace-nowrap">
                        <div class="font-bold text-slate-800">Jam <?= e($j['jam_ke']) ?></div>
                        <div><?= substr($j['slot_mulai'], 0, 5) ?> - <?= substr($j['slot_selesai'], 0, 5) ?></div>
                    </td>
                    <td class="py-3 px-4 capitalize font-medium text-blue-600"><?= e($j['hari']) ?></td>
                    <td class="py-3 px-4 font-semibold whitespace-nowrap"><?= e($j['kelas_nama']) ?></td>
                    <td class="py-3 px-4"><?= e($j['mapel_nama']) ?></td>
                    <td class="py-3 px-4 text-slate-600"><?= e($j['guru_nama']) ?></td>
                    <td class="py-3 px-4">
                        <form method="post" action="<?= url("admin/jadwal/{$j['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Hapus jadwal ini?');">
                            <?= \App\Core\CSRF::field() ?>
                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; return ob_get_clean();
    }

    private function renderPagination(array $paginator): string {
        if ($paginator['last_page'] <= 1) return '';
        ob_start(); ?><div class="flex gap-1"><?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?><button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>"><?= $i ?></button><?php endfor; ?></div><?php return ob_get_clean();
    }

    public function create(): void
    {
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        if (!$taAktif) { Session::toast('error', 'Aktifkan Tahun Ajaran terlebih dahulu.'); Response::redirect(url('admin/jadwal')); }

        $guruList = DB::raw("SELECT g.id, u.nama_lengkap FROM guru g JOIN users u ON g.user_id = u.id WHERE g.school_id = ? AND g.aktif = 1 ORDER BY u.nama_lengkap", [current_school_id()]);
        $mapelList = DB::raw("SELECT id, nama FROM mapel WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);
        $kelasList = DB::raw("SELECT id, nama FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);
        $slotList = DB::raw("SELECT * FROM slot_jam WHERE school_id = ? ORDER BY jam_ke", [current_school_id()]);

        View::render('admin.jadwal.form', [
            'title' => 'Tambah Jadwal', 'taAktif' => $taAktif,
            'guruList' => $guruList, 'mapelList' => $mapelList, 'kelasList' => $kelasList, 'slotList' => $slotList
        ], 'admin');
    }

    public function store(): void
    {
        CSRF::check();
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        $data = Request::only(['hari', 'slot_jam_id', 'guru_id', 'mapel_id', 'kelas_id']);
        
        $pengampu = DB::table('pengampu')->where('school_id', current_school_id())->where('tahun_ajaran_id', $taAktif['id'])->where('mapel_id', $data['mapel_id'])->where('kelas_id', $data['kelas_id'])->first();
        if (!$pengampu) {
            $pengampuId = DB::table('pengampu')->insert(['school_id' => current_school_id(), 'tahun_ajaran_id' => $taAktif['id'], 'guru_id' => $data['guru_id'], 'mapel_id' => $data['mapel_id'], 'kelas_id' => $data['kelas_id']]);
        } else {
            $pengampuId = $pengampu['id'];
            DB::table('pengampu')->where('id', $pengampuId)->update(['guru_id' => $data['guru_id']]);
        }

        $slot = DB::table('slot_jam')->where('id', $data['slot_jam_id'])->first();
        $bentrok = DB::rawOne("SELECT j.id FROM jadwal j JOIN pengampu p ON j.pengampu_id = p.id WHERE j.school_id = ? AND j.tahun_ajaran_id = ? AND j.hari = ? AND j.slot_jam_id = ? AND (p.guru_id = ? OR p.kelas_id = ?)", [current_school_id(), $taAktif['id'], $data['hari'], $data['slot_jam_id'], $data['guru_id'], $data['kelas_id']]);

        if ($bentrok) {
            Session::toast('error', 'Guru atau Kelas sudah memiliki jadwal di jam tersebut.');
            Response::redirect(url('admin/jadwal/create'));
        }

        DB::table('jadwal')->insert(['school_id' => current_school_id(), 'tahun_ajaran_id' => $taAktif['id'], 'pengampu_id' => $pengampuId, 'hari' => $data['hari'], 'slot_jam_id' => $data['slot_jam_id'], 'jam_mulai' => $slot['jam_mulai'], 'jam_selesai' => $slot['jam_selesai']]);
        Session::toast('success', 'Jadwal ditambahkan.'); Response::redirect(url('admin/jadwal'));
    }

    public function jadwalSiswa(): void
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
            Session::toast('error', 'Data siswa tidak ditemukan.');
            Response::redirect(url('dashboard'));
        }

        $taAktif = DB::table('tahun_ajaran')
            ->where('school_id', current_school_id())
            ->where('aktif', 1)
            ->first();

        $jadwal = DB::raw("
            SELECT j.id, j.hari, m.nama AS mapel_nama,
                ug.nama_lengkap AS guru_nama,
                sl.jam_mulai, sl.jam_selesai, sl.jam_ke
            FROM jadwal j
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN guru g ON p.guru_id = g.id
            JOIN users ug ON g.user_id = ug.id
            JOIN slot_jam sl ON j.slot_jam_id = sl.id
            WHERE j.school_id = ? AND p.kelas_id = ? AND j.tahun_ajaran_id = ?
            ORDER BY FIELD(j.hari,'senin','selasa','rabu','kamis','jumat','sabtu'), sl.jam_ke
        ", [current_school_id(), $siswa['kelas_id'], $taAktif['id'] ?? 0]);

        // Group per hari (format: 'Senin', 'Selasa', ...)
        $groupedJadwal = [];
        foreach ($jadwal as $j) {
            $groupedJadwal[ucfirst(strtolower($j['hari']))][] = $j;
        }

        View::render('siswa.jadwal', [
            'title' => 'Jadwal Pelajaran',
            'siswa' => $siswa,
            'groupedJadwal' => $groupedJadwal,
        ], 'siswa');
    }
    public function jadwalPdf(): void
    {
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        $kelasId = $_GET['kelas_id'] ?? '';

        $sql = "SELECT j.*, s.jam_ke, s.jam_mulai, s.jam_selesai, m.nama as mapel_nama, k.nama as kelas_nama, u.nama_lengkap as guru_nama
                FROM jadwal j
                JOIN slot_jam s ON j.slot_jam_id = s.id
                JOIN pengampu p ON j.pengampu_id = p.id
                JOIN mapel m ON p.mapel_id = m.id
                JOIN kelas k ON p.kelas_id = k.id
                JOIN guru g ON p.guru_id = g.id
                JOIN users u ON g.user_id = u.id
                WHERE j.school_id = ? AND j.tahun_ajaran_id = ?";
        $bindings = [current_school_id(), $taAktif['id']];

        if (!empty($kelasId)) {
            $sql .= " AND p.kelas_id = ?";
            $bindings[] = $kelasId;
        }

        $sql .= " ORDER BY FIELD(j.hari, 'senin','selasa','rabu','kamis','jumat','sabtu'), s.jam_ke ASC";
        $jadwal = DB::raw($sql, $bindings);

        $judulKelas = "Semua Kelas";
        if (!empty($kelasId)) {
            $kls = DB::table('kelas')->where('id', $kelasId)->first();
            $judulKelas = $kls ? $kls['nama'] : '-';
        }

        // Render HTML untuk PDF
        $html = '
        <html><head><style>
            body { font-family: sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .text-center { text-align: center; }
        </style></head><body>
            <h2 style="text-align:center;">Jadwal Pelajaran</h2>
            <p style="text-align:center;">Tahun Ajaran: ' . $taAktif['nama'] . ' | Kelas: ' . $judulKelas . '</p>
            <table>
                <thead><tr><th class="text-center">Jam</th><th>Hari</th><th>Kelas</th><th>Mapel</th><th>Guru</th></tr></thead>
                <tbody>';

        foreach ($jadwal as $j) {
            $html .= '<tr>
                        <td class="text-center">Jam ' . $j['jam_ke'] . '<br><small>' . substr($j['jam_mulai'],0,5) . ' - ' . substr($j['jam_selesai'],0,5) . '</small></td>
                        <td>' . ucfirst($j['hari']) . '</td>
                        <td>' . $j['kelas_nama'] . '</td>
                        <td>' . $j['mapel_nama'] . '</td>
                        <td>' . $j['guru_nama'] . '</td>
                    </tr>';
        }

        $html .= '</tbody></table></body></html>';

        // Render PDF pakai Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Jadwal_Pelajaran_' . str_replace(' ', '_', $judulKelas) . '.pdf', ['Attachment' => false]);
        exit;
    }

    public function destroy(int $id): void {
        CSRF::check(); DB::table('jadwal')->where('id', $id)->where('school_id', current_school_id())->delete(); Session::toast('success', 'Jadwal dihapus.'); Response::redirect(url('admin/jadwal'));
    }
}