<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Auth, CSRF, DB, Request, Response, Session, Validator, View};

class GuruController
{
    public function index(): void
    {
        $search = Request::string('q');
        $statusFilter = Request::string('status', 'aktif'); // Default aktif
        $perPage = Request::int('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20, 50, 100])) $perPage = 10;

        // Ubah query dasar
        $sqlBase = "FROM guru g JOIN users u ON g.user_id = u.id WHERE g.school_id = ?";
        $bindings = [current_school_id()];

        if ($statusFilter === 'arsip') {
            $sqlBase .= " AND g.aktif = 0";
        } else {
            $sqlBase .= " AND g.aktif = 1";
        }

        if ($search !== '') { 
            $sqlBase .= " AND (u.nama_lengkap LIKE ? OR g.nip LIKE ? OR u.username LIKE ?)"; 
            $bindings[] = "%{$search}%"; 
            $bindings[] = "%{$search}%"; 
            $bindings[] = "%{$search}%"; 
        }

        $total = (int) DB::rawOne("SELECT COUNT(*) as c " . $sqlBase, $bindings)['c'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage; 
        $lastPage = (int) ceil($total / $perPage);
        $data = DB::raw("SELECT g.*, u.nama_lengkap, u.username " . $sqlBase . " ORDER BY u.nama_lengkap ASC LIMIT {$perPage} OFFSET {$offset}", $bindings);

        $paginator = [
            'data' => $data, 'total' => $total, 'current_page' => $page, 
            'last_page' => $lastPage, 'from' => $total > 0 ? $offset + 1 : 0, 
            'to' => min($offset + $perPage, $total), 'search' => $search, 
            'per_page' => $perPage, 'status' => $statusFilter
        ];

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['html' => $this->renderTableBody($paginator), 'pagination_html' => $this->renderPagination($paginator), 'info' => "Menampilkan {$paginator['from']} - {$paginator['to']} dari {$paginator['total']} data"]);
            exit;
        }
        View::render('admin.guru.index', ['title' => 'Data Guru', 'paginator' => $paginator], 'admin');
    }

    private function renderTableBody(array $paginator): string {
        ob_start(); ?>
        <?php if (empty($paginator['data'])): ?><tr><td colspan="4" class="py-8 text-center text-slate-500">Belum ada data guru.</td></tr><?php else: ?>
            <?php foreach ($paginator['data'] as $g): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6 text-slate-600 font-mono text-xs"><?= e($g['nip'] ?? '-') ?></td>
                    <td class="py-3 px-6 font-semibold text-slate-800"><?= e($g['nama_lengkap']) ?></td>
                    <td class="py-3 px-6 text-slate-600"><?= e($g['username']) ?></td>
                    <td class="py-3 px-6 flex gap-3">
                        <?php if (($paginator['status'] ?? 'aktif') === 'aktif'): ?>
                            <button onclick="openEditModal(<?= $g['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                            <a href="<?= url("admin/guru/{$g['id']}/nonaktifkan") ?>" class="text-red-600 hover:text-red-800 font-medium">Nonaktifkan</a>
                           <?php else: ?>
                            <!-- Tombol Aktifkan Kembali -->
                            <button type="button" onclick="konfirmasiAktifkan(<?= $g['id'] ?>)" class="text-emerald-600 hover:text-emerald-800 font-medium font-semibold">Aktifkan Kembali</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; return ob_get_clean();
    }

    private function renderPagination(array $paginator): string {
        if ($paginator['last_page'] <= 1) return '';
        ob_start(); ?>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?>
                <button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </button>
            <?php endfor; ?>
        </div>
        <?php return ob_get_clean();
    }

    public function detail(int $id): void {
        $guru = DB::rawOne("SELECT g.*, u.nama_lengkap, u.username FROM guru g JOIN users u ON g.user_id = u.id WHERE g.id = ? AND g.school_id = ?", [$id, current_school_id()]);
        header('Content-Type: application/json'); echo json_encode($guru ?: ['error' => 'Not found']); exit;
    }

    public function store(): void {
        CSRF::check(); $data = Request::only(['nama_lengkap', 'username', 'nip', 'password']); $data['school_id'] = current_school_id();
        if (DB::table('users')->where('school_id', current_school_id())->where('username', $data['username'])->exists()) { Session::toast('error', 'Username sudah ada.'); Response::redirect(url('admin/guru')); }
        
        DB::transaction(function() use ($data) {
            $userId = DB::table('users')->insert(['school_id' => $data['school_id'], 'role' => 'guru', 'username' => $data['username'], 'password_hash' => Auth::hashPassword($data['password']), 'nama_lengkap' => $data['nama_lengkap'], 'aktif' => 1, 'must_change_password' => 1]);
            DB::table('guru')->insert(['user_id' => $userId, 'school_id' => $data['school_id'], 'nip' => $data['nip'] ?: null, 'aktif' => 1]);
        });
        Session::toast('success', 'Guru ditambahkan.'); Response::redirect(url('admin/guru'));
    }

    public function update(int $id): void {
        CSRF::check(); $guru = DB::rawOne("SELECT * FROM guru WHERE id = ? AND school_id = ?", [$id, current_school_id()]);
        $data = Request::only(['nama_lengkap', 'username', 'nip', 'password']);
        DB::transaction(function() use ($data, $guru) {
            $userData = ['nama_lengkap' => $data['nama_lengkap'], 'username' => $data['username']];
            if (!empty($data['password'])) $userData['password_hash'] = Auth::hashPassword($data['password']);
            DB::table('users')->where('id', $guru['user_id'])->update($userData);
            DB::table('guru')->where('id', $guru['id'])->update(['nip' => $data['nip'] ?: null]);
        });
        Session::toast('success', 'Guru berhasil diperbarui.'); Response::redirect(url('admin/guru'));
    }

    // ==========================================
    // FITUR LIFECYCLE GURU
    // ==========================================

    public function formNonaktifkan(int $id): void
    {
        $guru = DB::rawOne("SELECT g.*, u.nama_lengkap FROM guru g JOIN users u ON g.user_id = u.id WHERE g.id = ? AND g.school_id = ?", [$id, current_school_id()]);
        if (!$guru) {
            Session::toast('error', 'Data tidak ditemukan.');
            Response::redirect(url('admin/guru'));
        }

        // Cek jadwal aktif guru ini di TA aktif
        $taAktif = DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first();
        $taId = $taAktif ? $taAktif['id'] : 0;

        $jadwalAktif = DB::raw("
            SELECT j.id as jadwal_id, m.nama as mapel_nama, k.nama as kelas_nama, 
                   sj.jam_ke, j.hari, sj.jam_mulai, sj.jam_selesai
            FROM jadwal j
            JOIN pengampu p ON j.pengampu_id = p.id
            JOIN mapel m ON p.mapel_id = m.id
            JOIN kelas k ON p.kelas_id = k.id
            JOIN slot_jam sj ON j.slot_jam_id = sj.id
            WHERE p.guru_id = ? AND j.tahun_ajaran_id = ?
            ORDER BY j.hari ASC, sj.jam_ke ASC
        ", [$id, $taId]);

        // Ambil daftar guru lain untuk pilihan pengganti
        $guruPengganti = DB::raw("
            SELECT g.id, u.nama_lengkap 
            FROM guru g JOIN users u ON g.user_id = u.id 
            WHERE g.school_id = ? AND g.aktif = 1 AND g.id != ?
            ORDER BY u.nama_lengkap ASC
        ", [current_school_id(), $id]);

        View::render('admin.guru.nonaktifkan', [
            'title' => 'Nonaktifkan Guru',
            'guru' => $guru,
            'jadwalAktif' => $jadwalAktif,
            'guruPengganti' => $guruPengganti
        ], 'admin');
    }

    public function prosesNonaktifkan(int $id): void
    {
        CSRF::check();
        $guru = DB::table('guru')->where('id', $id)->where('school_id', current_school_id())->first();
        if (!$guru) {
            Session::toast('error', 'Data tidak ditemukan.');
            Response::redirect(url('admin/guru'));
        }

        $status = Request::string('status'); // pensiun, pindah, meninggal, berhenti
        $keterangan = Request::string('keterangan');
        $tanggal = Request::string('tanggal_status', date('Y-m-d'));
        $reassign = Request::input('reassign', []); // [jadwal_id => guru_pengganti_id]

        // Validasi status
        if (!in_array($status, ['pensiun', 'pindah', 'meninggal', 'berhenti'])) {
            Session::toast('error', 'Status tidak valid.');
            Response::redirect(url("admin/guru/{$id}/nonaktifkan"));
        }

        try {
            DB::transaction(function() use ($id, $guru, $status, $keterangan, $tanggal, $reassign) {
                // 1. Proses Reassign Jadwal jika ada
                if (!empty($reassign)) {
                    foreach ($reassign as $jadwalId => $guruBaruId) {
                        if (empty($guruBaruId)) continue;

                        // Ambil pengampu_id dari jadwal tersebut
                        $jadwal = DB::table('jadwal')->where('id', $jadwalId)->first();
                        if ($jadwal) {
                            // Update pengampu dengan guru baru
                            DB::table('pengampu')
                                ->where('id', $jadwal['pengampu_id'])
                                ->update(['guru_id' => (int)$guruBaruId]);
                        }
                    }
                }

                // 2. Update status guru
                DB::table('guru')->where('id', $id)->update([
                    'aktif' => 0,
                    'status_lifecycle' => $status,
                    'tanggal_status' => $tanggal,
                    'keterangan_status' => $keterangan ?: null
                ]);

                // 3. Matikan akun user
                DB::table('users')->where('id', $guru['user_id'])->update(['aktif' => 0]);
            });

            Session::toast('success', 'Guru berhasil dinonaktifkan dan jadwal telah dialihkan.');
            Response::redirect(url('admin/guru'));

        } catch (\Exception $e) {
            Session::toast('error', 'Gagal memproses: ' . $e->getMessage());
            Response::redirect(url("admin/guru/{$id}/nonaktifkan"));
        }
    }

    public function aktifkanKembali(int $id): void
    {
        CSRF::check();
        
        $guru = DB::table('guru')->where('id', $id)->where('school_id', current_school_id())->first();
        if (!$guru) {
            Session::toast('error', 'Data tidak ditemukan.');
            Response::redirect(url('admin/guru?status=arsip'));
        }

        DB::transaction(function() use ($guru) {
            // 1. Aktifkan kembali tabel guru
            DB::table('guru')->where('id', $guru['id'])->update([
                'aktif' => 1,
                'status_lifecycle' => 'aktif',
                'tanggal_status' => null,
                'keterangan_status' => null
            ]);

            // 2. Aktifkan kembali akun user (agar bisa login)
            DB::table('users')->where('id', $guru['user_id'])->update(['aktif' => 1]);
        });

        Session::toast('success', 'Akun guru berhasil diaktifkan kembali.');
        Response::redirect(url('admin/guru?status=arsip'));
    }
}