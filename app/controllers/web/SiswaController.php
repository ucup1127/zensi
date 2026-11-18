<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

class SiswaController
{
    public function index(): void
    {
        $search = Request::string('q');
        $kelasId = Request::int('kelas_id');
        $statusFilter = Request::string('status', 'aktif'); // Default aktif
        $perPage = Request::int('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20, 50, 100])) $perPage = 10;

        // Ambil ID Tahun Ajaran Aktif
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        $taId = $taAktif ? $taAktif['id'] : 0;

        $sqlBase = "FROM siswa s 
                    JOIN users u ON s.user_id = u.id 
                    LEFT JOIN riwayat_kelas rk ON s.id = rk.siswa_id AND rk.tahun_ajaran_id = ? 
                    LEFT JOIN kelas k ON rk.kelas_id = k.id 
                    WHERE s.school_id = ?";
        $bindings = [$taId, current_school_id()];

        // Filter Status Lifecycle
        if ($statusFilter === 'alumni') {
            $sqlBase .= " AND s.status_lifecycle IN ('lulus', 'pindah', 'keluar', 'meninggal')";
        } else {
            $sqlBase .= " AND s.aktif = 1 AND s.status_lifecycle = 'aktif'";
        }

        if ($search !== '') {
            $sqlBase .= " AND (u.nama_lengkap LIKE ? OR s.nis LIKE ?)";
            $bindings[] = "%{$search}%";
            $bindings[] = "%{$search}%";
        }

        if ($kelasId > 0) {
            $sqlBase .= " AND rk.kelas_id = ?";
            $bindings[] = $kelasId;
        }

        $total = (int) DB::rawOne("SELECT COUNT(*) as c " . $sqlBase, $bindings)['c'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $lastPage = (int) ceil($total / $perPage);

        // Ambil nama kelas dari riwayat_kelas
        $data = DB::raw("SELECT s.*, u.nama_lengkap, u.username, k.nama as kelas_nama " . $sqlBase . " ORDER BY k.nama ASC, u.nama_lengkap ASC LIMIT {$perPage} OFFSET {$offset}", $bindings);
        
        $kelasList = DB::raw("SELECT id, nama FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);

        $paginator = [
            'data' => $data, 'total' => $total, 'current_page' => $page,
            'last_page' => $lastPage, 'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total), 'search' => $search,
            'kelas_id' => $kelasId, 'per_page' => $perPage, 'status' => $statusFilter
        ];

        // Jika request via AJAX, kirim JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'html' => $this->renderTableBody($paginator),
                'pagination_html' => $this->renderPagination($paginator),
                'info' => "Menampilkan {$paginator['from']} - {$paginator['to']} dari {$paginator['total']} data"
            ]);
            exit;
        }

        View::render('admin.siswa.index', [
            'title' => 'Data Siswa',
            'paginator' => $paginator,
            'kelasList' => $kelasList
        ], 'admin');
    }

    private function renderTableBody(array $paginator): string
    {
        ob_start();
        if (empty($paginator['data'])): ?>
            <tr><td colspan="4" class="py-8 text-center text-slate-500">Belum ada data siswa.</td></tr>
        <?php else: ?>
            <?php foreach ($paginator['data'] as $s): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6 text-slate-600 font-mono text-xs"><?= e($s['nis']) ?></td>
                    <td class="py-3 px-6 font-semibold text-slate-800"><?= e($s['nama_lengkap']) ?></td>
                    <td class="py-3 px-6 text-slate-600"><?= e($s['kelas_nama']) ?></td>
                    <td class="py-3 px-6 flex gap-3">
                        <?php if (($paginator['status'] ?? 'aktif') === 'aktif'): ?>
                            <button onclick="openEditModal(<?= $s['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                            <form method="post" action="<?= url("admin/siswa/{$s['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Nonaktifkan siswa ini?');">
                                <?= \App\Core\CSRF::field() ?>
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                            </form>
                        <?php else: ?>
                            <span class="text-slate-400 italic text-xs">Arsip</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif;
        return ob_get_clean();
    }

    private function renderPagination(array $paginator): string
    {
        if ($paginator['last_page'] <= 1) return '';
        
        ob_start(); ?>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?>
                <button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </button>
            <?php endfor; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function create(): void
    {
        $kelasList = DB::raw("SELECT id, nama FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);
        View::render('admin.siswa.form', ['title' => 'Tambah Siswa', 'siswa' => null, 'kelasList' => $kelasList], 'admin');
    }

    public function store(): void
    {
        CSRF::check();
        
        $data = Request::only(['nama_lengkap', 'nis', 'kelas_id', 'tanggal_lahir']);
        $data['school_id'] = current_school_id();

        $v = Validator::make($data, [
            'nama_lengkap' => 'required|max:150',
            'nis' => 'required|max:30',
            'kelas_id' => 'required|numeric',
            'tanggal_lahir' => 'required'
        ]);

        if (!$v->validate()) {
            Session::toast('error', $v->firstError());
            Session::flash('old', $data);
            Response::redirect(url('admin/siswa/create'));
        }

        if (DB::table('siswa')->where('school_id', current_school_id())->where('nis', $data['nis'])->exists()) {
            Session::toast('error', 'NIS sudah digunakan.');
            Session::flash('old', $data);
            Response::redirect(url('admin/siswa/create'));
        }

        $tgl = strtotime($data['tanggal_lahir']);
        $passwordDefault = date('dmY', $tgl);
        $username = $data['nis'];

        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        
        // Ambil nama kelas untuk sinkronisasi
        $kelasData = DB::table('kelas')->where('id', $data['kelas_id'])->first();
        $kelasNama = $kelasData['nama'] ?? '-';

        DB::transaction(function() use ($data, $passwordDefault, $username, $taAktif) {
            $userId = DB::table('users')->insert([
                'school_id' => $data['school_id'],
                'role' => 'siswa',
                'username' => $username,
                'password_hash' => Auth::hashPassword($passwordDefault),
                'nama_lengkap' => $data['nama_lengkap'],
                'aktif' => 1,
                'must_change_password' => 1
            ]);

            $siswaId = DB::table('siswa')->insert([
                'user_id' => $userId,
                'school_id' => $data['school_id'],
                'nis' => $data['nis'],
                'kelas_id' => $data['kelas_id'],
                'aktif' => 1,
                'status_lifecycle' => 'aktif'
            ]);

            if ($taAktif) {
                DB::table('riwayat_kelas')->insert([
                    'school_id' => current_school_id(),
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $taAktif['id'],
                    'kelas_id' => $data['kelas_id'],
                    'status' => 'aktif',
                    'tanggal_masuk' => date('Y-m-d')
                ]);
            }
        });

        // ==========================================
        // AUTO SYNC KE E-LIBRARY
        // ==========================================
        try {
            $this->syncKeELibrary([
                [
                    'nisn' => $data['nis'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'kelas_nama' => $kelasNama
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Gagal sync ke e-library: " . $e->getMessage());
        }

        Session::toast('success', "Siswa berhasil ditambahkan. Username: {$username}, Password: {$passwordDefault}");
        Response::redirect(url('admin/siswa'));
    }

    public function edit(int $id): void
    {
        $siswa = DB::rawOne("SELECT s.*, u.nama_lengkap, u.username FROM siswa s JOIN users u ON s.user_id = u.id WHERE s.id = ? AND s.school_id = ?", [$id, current_school_id()]);
        if (!$siswa) {
            Session::toast('error', 'Data tidak ditemukan.');
            Response::redirect(url('admin/siswa'));
        }

        $kelasList = DB::raw("SELECT id, nama FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY nama", [current_school_id()]);
        View::render('admin.siswa.form', ['title' => 'Edit Data Siswa', 'siswa' => $siswa, 'kelasList' => $kelasList], 'admin');
    }

    public function update(int $id): void
    {
        CSRF::check();
        $siswa = DB::rawOne("SELECT s.*, u.nama_lengkap FROM siswa s JOIN users u ON s.user_id = u.id WHERE s.id = ? AND s.school_id = ?", [$id, current_school_id()]);
        if (!$siswa) {
            Session::toast('error', 'Data tidak ditemukan.');
            Response::redirect(url('admin/siswa'));
        }

        $data = Request::only(['nama_lengkap', 'nis', 'kelas_id']);
        $v = Validator::make($data, [
            'nama_lengkap' => 'required|max:150',
            'nis' => 'required|max:50',
            'kelas_id' => 'required|numeric'
        ]);

        if (!$v->validate()) {
            Session::toast('error', $v->firstError());
            Response::redirect(url('admin/siswa')); 
        }

        if (DB::table('siswa')->where('school_id', current_school_id())->where('nis', $data['nis'])->where('id', '!=', $id)->exists()) {
            Session::toast('error', 'NIS sudah digunakan.');
            Response::redirect(url('admin/siswa'));
        }

        DB::transaction(function() use ($data, $siswa) {
            DB::table('users')->where('id', $siswa['user_id'])->update([
                'nama_lengkap' => $data['nama_lengkap'],
                'username' => $data['nis']
            ]);
            
            // Tetap update di tabel siswa untuk backward compatibility
            DB::table('siswa')->where('id', $siswa['id'])->update([
                'nis' => $data['nis'],
                'kelas_id' => $data['kelas_id'] // Update ini juga agar jika ada query lama tetap sinkron
            ]);

            // Update di riwayat kelas TA aktif
            $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
            if ($taAktif) {
                $existingRiwayat = DB::table('riwayat_kelas')
                    ->where('siswa_id', $siswa['id'])
                    ->where('tahun_ajaran_id', $taAktif['id'])
                    ->first();
                
                if ($existingRiwayat) {
                    DB::table('riwayat_kelas')->where('id', $existingRiwayat['id'])->update([
                        'kelas_id' => $data['kelas_id']
                    ]);
                } else {
                    DB::table('riwayat_kelas')->insert([
                        'school_id' => current_school_id(),
                        'siswa_id' => $siswa['id'],
                        'tahun_ajaran_id' => $taAktif['id'],
                        'kelas_id' => $data['kelas_id'],
                        'status' => 'aktif',
                        'tanggal_masuk' => date('Y-m-d')
                    ]);
                }
            }
        });

        // ==========================================
        // AUTO SYNC KE E-LIBRARY (Jika data berubah)
        // ==========================================
        try {
            $kelasData = DB::table('kelas')->where('id', $data['kelas_id'])->first();
            $this->syncKeELibrary([
                [
                    'nisn' => $data['nis'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'kelas_nama' => $kelasData['nama'] ?? '-'
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Gagal sync ke e-library: " . $e->getMessage());
        }

        Session::toast('success', 'Data siswa berhasil diperbarui.');
        Response::redirect(url('admin/siswa'));
    }

    public function destroy(int $id): void
    {
        CSRF::check();
        $siswa = DB::table('siswa')->where('id', $id)->where('school_id', current_school_id())->first();
        if ($siswa) {
            DB::table('users')->where('id', $siswa['user_id'])->update(['aktif' => 0]);
            DB::table('siswa')->where('id', $id)->update([
                'aktif' => 0,
                'status_lifecycle' => 'keluar', // Default jika dihapus manual
                'tanggal_status' => date('Y-m-d')
            ]);
        }
        Session::toast('success', 'Data siswa berhasil dinonaktifkan.');
        Response::redirect(url('admin/siswa'));
    }

    public function detail(int $id): void
    {
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        $taId = $taAktif ? $taAktif['id'] : 0;

        // Ambil data spesifik dengan alias yang jelas
        $siswa = DB::rawOne("
            SELECT 
                s.id, 
                s.nis, 
                u.nama_lengkap, 
                u.username, 
                rk.kelas_id as kelas_id_saat_ini,
                k.nama as kelas_nama_saat_ini
            FROM siswa s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN riwayat_kelas rk ON s.id = rk.siswa_id AND rk.tahun_ajaran_id = ?
            LEFT JOIN kelas k ON rk.kelas_id = k.id
            WHERE s.id = ? AND s.school_id = ?
        ", [$taId, $id, current_school_id()]);

        if ($siswa) {
            // Siapkan array respons yang bersih
            $response = [
                'id' => $siswa['id'],
                'nis' => $siswa['nis'],
                'nama_lengkap' => $siswa['nama_lengkap'],
                'username' => $siswa['username'],
                // Jika tidak ada riwayat kelas di TA aktif, fallback ke kelas_id dari tabel siswa
                'kelas_id' => $siswa['kelas_id_saat_ini'] ?? null,
            ];

            // Jika null, coba ambil dari tabel siswa (untuk jaga-jaga)
            if (!$response['kelas_id']) {
                $fallback = DB::table('siswa')->where('id', $id)->first();
                $response['kelas_id'] = $fallback['kelas_id'] ?? null;
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }

    // ==========================================
    // FITUR KENAIKAN KELAS
    // ==========================================

    public function kenaikanKelas(): void
    {
        $taList = DB::raw("SELECT id, nama, aktif FROM tahun_ajaran WHERE school_id = ? ORDER BY nama DESC", [current_school_id()]);
        
        View::render('admin.siswa.kenaikan_kelas', [
            'title' => 'Kenaikan Kelas',
            'taList' => $taList
        ], 'admin');
    }

    public function kenaikanKelasPreview(): void
    {
        CSRF::check();
        $taAsalId = (int) Request::input('ta_asal_id');
        $taTujuanId = (int) Request::input('ta_tujuan_id');

        if ($taAsalId === $taTujuanId) {
            Session::toast('error', 'Tahun ajaran asal dan tujuan tidak boleh sama.');
            Response::redirect(url('admin/kenaikan-kelas'));
        }

        $siswaList = DB::raw("
            SELECT s.id as siswa_id, s.nis, u.nama_lengkap, rk.kelas_id as kelas_asal_id, k.nama as kelas_asal_nama, k.tingkat as tingkat_asal
            FROM riwayat_kelas rk
            JOIN siswa s ON rk.siswa_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN kelas k ON rk.kelas_id = k.id
            WHERE rk.school_id = ? AND rk.tahun_ajaran_id = ? AND s.aktif = 1
            ORDER BY k.tingkat ASC, k.nama ASC, u.nama_lengkap ASC
        ", [current_school_id(), $taAsalId]);

        $kelasTujuan = DB::raw("SELECT id, nama, tingkat FROM kelas WHERE school_id = ? AND aktif = 1 ORDER BY tingkat ASC, nama ASC", [current_school_id()]);

        View::render('admin.siswa.kenaikan_kelas_preview', [
            'title' => 'Preview Kenaikan Kelas',
            'siswaList' => $siswaList,
            'kelasTujuan' => $kelasTujuan,
            'taAsalId' => $taAsalId,
            'taTujuanId' => $taTujuanId
        ], 'admin');
    }

    public function kenaikanKelasProses(): void
    {
        CSRF::check();
        $taTujuanId = (int) Request::input('ta_tujuan_id');
        $mapping = Request::input('mapping', []);

        if (empty($mapping)) {
            Session::toast('error', 'Tidak ada data yang diproses.');
            Response::redirect(url('admin/kenaikan-kelas'));
        }

        try {
            DB::transaction(function() use ($taTujuanId, $mapping) {
                foreach ($mapping as $siswaId => $data) {
                    $siswaId = (int) $siswaId;
                    $kelasId = (int) ($data['kelas_id'] ?? 0);
                    $status  = $data['status'] ?? 'naik';

                    if ($status === 'lulus') {
                        DB::table('siswa')->where('id', $siswaId)->update([
                            'status_lifecycle' => 'lulus',
                            'aktif' => 0,
                            'tanggal_status' => date('Y-m-d')
                        ]);
                        $siswa = DB::table('siswa')->where('id', $siswaId)->first();
                        DB::table('users')->where('id', $siswa['user_id'])->update(['aktif' => 0]);

                        DB::table('riwayat_kelas')->insert([
                            'school_id' => current_school_id(),
                            'siswa_id' => $siswaId,
                            'tahun_ajaran_id' => $taTujuanId,
                            'kelas_id' => $kelasId,
                            'status' => 'lulus',
                            'keterangan' => 'Lulus'
                        ]);
                    } else {
                        DB::table('riwayat_kelas')->insert([
                            'school_id' => current_school_id(),
                            'siswa_id' => $siswaId,
                            'tahun_ajaran_id' => $taTujuanId,
                            'kelas_id' => $kelasId,
                            'status' => 'aktif',
                            'tanggal_masuk' => date('Y-m-d')
                        ]);
                        
                        // Sinkronkan kolom kelas_id di tabel siswa dengan kelas barunya
                        DB::table('siswa')->where('id', $siswaId)->update(['kelas_id' => $kelasId]);
                    }
                }
            });

            Session::toast('success', 'Proses kenaikan kelas berhasil disimpan!');
            Response::redirect(url('admin/siswa'));

        } catch (\Exception $e) {
            Session::toast('error', 'Gagal memproses: ' . $e->getMessage());
            Response::redirect(url('admin/kenaikan-kelas'));
        }
    }

    // ==========================================
    // FITUR IMPORT CSV SISWA
    // ==========================================

    public function importForm(): void
    {
        View::render('admin.siswa.import', [
            'title' => 'Import Siswa Baru'
        ], 'admin');
    }

    public function importProses(): void
    {
        CSRF::check();

        if (!isset($_FILES['file_csv']) || $_FILES['file_csv']['error'] !== UPLOAD_ERR_OK) {
            Session::toast('error', 'Gagal mengunggah file. Pastikan file tidak kosong.');
            Response::redirect(url('admin/siswa/import'));
        }

        $file = $_FILES['file_csv']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['file_csv']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'txt'])) {
            Session::toast('error', 'Format file harus .csv atau .txt');
            Response::redirect(url('admin/siswa/import'));
        }

        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        if (!$taAktif) {
            Session::toast('error', 'Tidak ada Tahun Ajaran aktif. Aktifkan TA terlebih dahulu.');
            Response::redirect(url('admin/siswa/import'));
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            Session::toast('error', 'Gagal membaca file.');
            Response::redirect(url('admin/siswa/import'));
        }

        $header = fgetcsv($handle);
        $success = 0;
        $failed = [];
        $rowNum = 1;
        $siswaUntukSync = []; // Array penampung untuk sinkronisasi massal

        DB::transaction(function() use ($handle, $taAktif, &$success, &$failed, &$rowNum, &$siswaUntukSync) {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count($row) < 4) {
                    $failed[] = "Baris {$rowNum}: Kolom kurang dari 4.";
                    continue;
                }

                $nama = trim($row[0]);
                $nis = trim($row[1]);
                $tglLahir = trim($row[2]);
                $kelasNama = trim($row[3]);

                if (empty($nama) || empty($nis) || empty($tglLahir) || empty($kelasNama)) {
                    $failed[] = "Baris {$rowNum}: Ada kolom wajib yang kosong.";
                    continue;
                }

                if (DB::table('siswa')->where('school_id', current_school_id())->where('nis', $nis)->exists()) {
                    $failed[] = "Baris {$rowNum}: NIS {$nis} sudah terdaftar.";
                    continue;
                }

                $kelas = DB::table('kelas')->where('school_id', current_school_id())->where('nama', $kelasNama)->first();
                if (!$kelas) {
                    $failed[] = "Baris {$rowNum}: Kelas '{$kelasNama}' tidak ditemukan.";
                    continue;
                }

                $ts = strtotime($tglLahir);
                if (!$ts) {
                    $failed[] = "Baris {$rowNum}: Format tanggal lahir salah (Gunakan YYYY-MM-DD).";
                    continue;
                }
                $passwordDefault = date('dmY', $ts);

                try {
                    $userId = DB::table('users')->insert([
                        'school_id' => current_school_id(),
                        'role' => 'siswa',
                        'username' => $nis,
                        'password_hash' => Auth::hashPassword($passwordDefault),
                        'nama_lengkap' => $nama,
                        'aktif' => 1,
                        'must_change_password' => 1
                    ]);

                    $siswaId = DB::table('siswa')->insert([
                        'user_id' => $userId,
                        'school_id' => current_school_id(),
                        'nis' => $nis,
                        'kelas_id' => $kelas['id'],
                        'aktif' => 1,
                        'status_lifecycle' => 'aktif'
                    ]);

                    DB::table('riwayat_kelas')->insert([
                        'school_id' => current_school_id(),
                        'siswa_id' => $siswaId,
                        'tahun_ajaran_id' => $taAktif['id'],
                        'kelas_id' => $kelas['id'],
                        'status' => 'aktif',
                        'tanggal_masuk' => date('Y-m-d')
                    ]);

                    $siswaUntukSync[] = [
                        'nisn' => $nis,
                        'nama_lengkap' => $nama,
                        'kelas_nama' => $kelasNama
                    ];

                    $success++;
                } catch (\Exception $e) {
                    $failed[] = "Baris {$rowNum}: Gagal disimpan (" . $e->getMessage() . ")";
                }
            }
        });

        fclose($handle);

        // ==========================================
        // AUTO SYNC KE E-LIBRARY (Massal)
        // ==========================================
        if (!empty($siswaUntukSync)) {
            try {
                $this->syncKeELibrary($siswaUntukSync);
            } catch (\Exception $e) {
                error_log("Gagal sync import ke e-library: " . $e->getMessage());
            }
        }

        $msg = "Berhasil: {$success} siswa diimport.";
        if (!empty($failed)) {
            $msg .= " Gagal: " . count($failed) . " baris.";
            Session::set('import_failed', $failed);
        }

        Session::toast('success', $msg);
        Response::redirect(url('admin/siswa/import'));
    }

    // ==========================================
    // PRIVATE HELPER: SINKRONISASI KE E-LIBRARY
    // ==========================================
    private function syncKeELibrary(array $siswaList): void
    {
        // Pastikan curl aktif di PHP
        if (!function_exists('curl_init')) {
            error_log("cURL tidak aktif. Sinkronisasi e-library dilewati.");
            return;
        }

        $payload = [
            'secret' => 'rahasia_zensi_123', // Harus sama dengan SYNC_SECRET_KEY di Next.js
            'school_slug' => 'mtsmuhwangon', // Sesuaikan dengan slug di e-library
            'siswa' => $siswaList
        ];

        // Ganti URL jika e-library sudah production (misal https://mtsmuhbms.id)
        $url = 'http://localhost:3000/api/sync-siswa'; 

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout 5 detik agar Zensi tidak hang
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Sync e-library gagal (HTTP $httpCode): " . $response);
        }
    }
}