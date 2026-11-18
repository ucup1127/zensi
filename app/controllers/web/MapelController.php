<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{CSRF, DB, Request, Response, Session, Validator, View};

class MapelController
{
    public function index(): void
    {
        $search = Request::string('q');
        $perPage = Request::int('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20, 50, 100])) $perPage = 10;

        $sqlBase = "FROM mapel WHERE school_id = ? AND aktif = 1";
        $bindings = [current_school_id()];
        if ($search !== '') {
            $sqlBase .= " AND (nama LIKE ? OR kode LIKE ?)";
            $bindings[] = "%{$search}%"; $bindings[] = "%{$search}%";
        }

        $total = (int) DB::rawOne("SELECT COUNT(*) as c " . $sqlBase, $bindings)['c'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $lastPage = (int) ceil($total / $perPage);
        $data = DB::raw("SELECT * " . $sqlBase . " ORDER BY nama ASC LIMIT {$perPage} OFFSET {$offset}", $bindings);

        $paginator = ['data' => $data, 'total' => $total, 'current_page' => $page, 'last_page' => $lastPage, 'from' => $total > 0 ? $offset + 1 : 0, 'to' => min($offset + $perPage, $total), 'search' => $search, 'per_page' => $perPage];

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['html' => $this->renderTableBody($paginator), 'pagination_html' => $this->renderPagination($paginator), 'info' => "Menampilkan {$paginator['from']} - {$paginator['to']} dari {$paginator['total']} data"]);
            exit;
        }

        View::render('admin.mapel.index', ['title' => 'Mata Pelajaran', 'paginator' => $paginator], 'admin');
    }

    private function renderTableBody(array $paginator): string {
        ob_start(); ?>
        <?php if (empty($paginator['data'])): ?><tr><td colspan="3" class="py-8 text-center text-slate-500">Belum ada data mapel.</td></tr><?php else: ?>
            <?php foreach ($paginator['data'] as $m): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6 text-slate-600 font-mono text-xs"><?= e($m['kode']) ?></td>
                    <td class="py-3 px-6 font-semibold text-slate-800"><?= e($m['nama']) ?></td>
                    <td class="py-3 px-6 flex gap-3">
                        <button onclick="openEditModal(<?= $m['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                        <form method="post" action="<?= url("admin/mapel/{$m['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Hapus mapel ini?');">
                            <?= \App\Core\CSRF::field() ?><button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; return ob_get_clean();
    }

    private function renderPagination(array $paginator): string {
        if ($paginator['last_page'] <= 1) return '';
        ob_start(); ?>
        <div class="flex gap-1"><?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?><button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>"><?= $i ?></button><?php endfor; ?></div>
        <?php return ob_get_clean();
    }

    public function detail(int $id): void {
        $mapel = DB::table('mapel')->where('id', $id)->where('school_id', current_school_id())->first();
        header('Content-Type: application/json'); echo json_encode($mapel ?: ['error' => 'Not found']); exit;
    }

    public function store(): void {
        CSRF::check(); $data = Request::only(['nama', 'kode']); $data['school_id'] = current_school_id(); $data['aktif'] = 1;
        if (DB::table('mapel')->where('school_id', current_school_id())->where('kode', $data['kode'])->exists()) { Session::toast('error', 'Kode mapel sudah ada.'); Response::redirect(url('admin/mapel')); }
        DB::table('mapel')->insert($data); Session::toast('success', 'Mapel ditambahkan.'); Response::redirect(url('admin/mapel'));
    }

    public function update(int $id): void {
        CSRF::check(); $data = Request::only(['nama', 'kode']);
        DB::table('mapel')->where('id', $id)->where('school_id', current_school_id())->update($data); Session::toast('success', 'Mapel diperbarui.'); Response::redirect(url('admin/mapel'));
    }

    public function destroy(int $id): void {
        CSRF::check(); DB::table('mapel')->where('id', $id)->update(['aktif' => 0]); Session::toast('success', 'Mapel dihapus.'); Response::redirect(url('admin/mapel'));
    }
}