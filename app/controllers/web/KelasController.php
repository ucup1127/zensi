<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

class KelasController
{
    public function index(): void
    {
        $search = Request::string('q');
        $tingkat = Request::string('tingkat');
        $perPage = Request::int('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20, 50, 100])) $perPage = 10;

        $sqlBase = "FROM kelas k LEFT JOIN guru g ON k.wali_kelas_id = g.id LEFT JOIN users u ON g.user_id = u.id WHERE k.school_id = ? AND k.aktif = 1";
        $bindings = [current_school_id()];

        if ($search !== '') {
            $sqlBase .= " AND k.nama LIKE ?";
            $bindings[] = "%{$search}%";
        }
        if ($tingkat !== '') {
            $sqlBase .= " AND k.tingkat = ?";
            $bindings[] = $tingkat;
        }

        $total = (int) DB::rawOne("SELECT COUNT(*) as c " . $sqlBase, $bindings)['c'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $lastPage = (int) ceil($total / $perPage);

        $data = DB::raw("SELECT k.*, u.nama_lengkap as wali_kelas " . $sqlBase . " ORDER BY k.tingkat ASC, k.nama ASC LIMIT {$perPage} OFFSET {$offset}", $bindings);

        $paginator = [
            'data' => $data, 'total' => $total, 'current_page' => $page,
            'last_page' => $lastPage, 'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total), 'search' => $search,
            'tingkat' => $tingkat, 'per_page' => $perPage
        ];

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'html' => $this->renderTableBody($paginator),
                'pagination_html' => $this->renderPagination($paginator),
                'info' => "Menampilkan {$paginator['from']} - {$paginator['to']} dari {$paginator['total']} data"
            ]);
            exit;
        }

        $guruList = DB::raw("SELECT g.id, u.nama_lengkap FROM guru g JOIN users u ON g.user_id = u.id WHERE g.school_id = ? AND g.aktif = 1 ORDER BY u.nama_lengkap", [current_school_id()]);

        View::render('admin.kelas.index', [
            'title' => 'Data Kelas', 'paginator' => $paginator, 'guruList' => $guruList
        ], 'admin');
    }

    private function renderTableBody(array $paginator): string
    {
        ob_start();
        if (empty($paginator['data'])): ?>
            <tr><td colspan="4" class="py-8 text-center text-slate-500">Belum ada data kelas.</td></tr>
        <?php else: ?>
            <?php foreach ($paginator['data'] as $k): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6 font-semibold text-slate-800"><?= e($k['nama']) ?></td>
                    <td class="py-3 px-6 text-slate-600">Tingkat <?= e($k['tingkat']) ?></td>
                    <td class="py-3 px-6 text-slate-600"><?= e($k['wali_kelas'] ?? '-') ?></td>
                    <td class="py-3 px-6 flex gap-3">
                        <button onclick="openEditModal(<?= $k['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                        <form method="post" action="<?= url("admin/kelas/{$k['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Hapus kelas ini?');">
                            <?= \App\Core\CSRF::field() ?>
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                        </form>
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
                <button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>"><?= $i ?></button>
            <?php endfor; ?>
        </div>
        <?php return ob_get_clean();
    }

    public function detail(int $id): void
    {
        $kelas = DB::table('kelas')->where('id', $id)->where('school_id', current_school_id())->first();
        header('Content-Type: application/json');
        echo json_encode($kelas ?: ['error' => 'Not found']);
        exit;
    }

    public function store(): void
    {
        CSRF::check();
        $data = Request::only(['nama', 'tingkat', 'wali_kelas_id']);
        $data['school_id'] = current_school_id();
        $data['aktif'] = 1;
        if (empty($data['wali_kelas_id'])) $data['wali_kelas_id'] = null;

        $v = Validator::make($data, ['nama' => 'required|max:30', 'tingkat' => 'required|numeric']);
        if (!$v->validate()) {
            Session::toast('error', $v->firstError());
            Response::redirect(url('admin/kelas'));
        }
        if (DB::table('kelas')->where('school_id', current_school_id())->where('nama', $data['nama'])->exists()) {
            Session::toast('error', 'Nama kelas sudah ada.');
            Response::redirect(url('admin/kelas'));
        }
        DB::table('kelas')->insert($data);
        Session::toast('success', 'Kelas berhasil ditambahkan.');
        Response::redirect(url('admin/kelas'));
    }

    public function update(int $id): void
    {
        CSRF::check();
        $data = Request::only(['nama', 'tingkat', 'wali_kelas_id']);
        if (empty($data['wali_kelas_id'])) $data['wali_kelas_id'] = null;

        $v = Validator::make($data, ['nama' => 'required|max:30', 'tingkat' => 'required|numeric']);
        if (!$v->validate()) {
            Session::flash('error', $v->firstError());
            Response::redirect(url('admin/kelas'));
        }
        DB::table('kelas')->where('id', $id)->where('school_id', current_school_id())->update($data);
        Session::toast('success', 'Kelas berhasil diperbarui.');
        Response::redirect(url('admin/kelas'));
    }

    public function destroy(int $id): void
    {
        CSRF::check();
        if (DB::table('siswa')->where('kelas_id', $id)->exists()) {
            Session::toast('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa.');
        } else {
            DB::table('kelas')->where('id', $id)->update(['aktif' => 0]);
            Session::toast('success', 'Kelas berhasil dihapus.');
        }
        Response::redirect(url('admin/kelas'));
    }
}