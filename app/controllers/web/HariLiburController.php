<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{CSRF, DB, Request, Response, Session, Validator, View};

class HariLiburController
{
    public function index(): void
    {
        $search = Request::string('q');
        $perPage = Request::int('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20, 50, 100])) $perPage = 10;

        $sqlBase = "FROM hari_libur WHERE school_id = ?";
        $bindings = [current_school_id()];
        if ($search !== '') { $sqlBase .= " AND keterangan LIKE ?"; $bindings[] = "%{$search}%"; }

        $total = (int) DB::rawOne("SELECT COUNT(*) as c " . $sqlBase, $bindings)['c'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage; $lastPage = (int) ceil($total / $perPage);
        $data = DB::raw("SELECT * " . $sqlBase . " ORDER BY tanggal ASC LIMIT {$perPage} OFFSET {$offset}", $bindings);

        $paginator = ['data' => $data, 'total' => $total, 'current_page' => $page, 'last_page' => $lastPage, 'from' => $total > 0 ? $offset + 1 : 0, 'to' => min($offset + $perPage, $total), 'search' => $search, 'per_page' => $perPage];

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['html' => $this->renderTableBody($paginator), 'pagination_html' => $this->renderPagination($paginator), 'info' => "Menampilkan {$paginator['from']} - {$paginator['to']} dari {$paginator['total']} data"]);
            exit;
        }
        View::render('admin.hari_libur.index', ['title' => 'Hari Libur', 'paginator' => $paginator], 'admin');
    }

    public function events(): void
    {
        $data = DB::table('hari_libur')
            ->where('school_id', current_school_id())
            ->get();

        $events = array_map(function($d) {
            return [
                'title' => $d['keterangan'],
                'start' => $d['tanggal'],
                'allDay' => true,
                'backgroundColor' => '#ef4444', // Warna merah (Tailwind red-500)
                'borderColor' => '#ef4444',
                'textColor' => '#ffffff'
            ];
        }, $data);

        header('Content-Type: application/json');
        echo json_encode($events);
        exit;
    }

    private function renderTableBody(array $paginator): string {
        ob_start(); ?>
        <?php if (empty($paginator['data'])): ?><tr><td colspan="3" class="py-8 text-center text-slate-500">Belum ada data hari libur.</td></tr><?php else: ?>
            <?php foreach ($paginator['data'] as $l): ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-6 font-semibold text-slate-800"><?= tanggal_id($l['tanggal'], true) ?></td>
                    <td class="py-3 px-6 text-slate-600"><?= e($l['keterangan']) ?></td>
                    <td class="py-3 px-6">
                        <form method="post" action="<?= url("admin/hari-libur/{$l['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Hapus libur ini?');"><?= \App\Core\CSRF::field() ?><button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button></form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; return ob_get_clean();
    }

    private function renderPagination(array $paginator): string {
        if ($paginator['last_page'] <= 1) return '';
        ob_start(); ?><div class="flex gap-1"><?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?><button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>"><?= $i ?></button><?php endfor; ?></div><?php return ob_get_clean();
    }

    public function store(): void {
        CSRF::check(); $data = Request::only(['tanggal', 'keterangan']); $data['school_id'] = current_school_id();
        if (DB::table('hari_libur')->where('school_id', current_school_id())->where('tanggal', $data['tanggal'])->exists()) { Session::toast('error', 'Tanggal sudah ada.'); }
        else { DB::table('hari_libur')->insert($data); Session::toast('success', 'Hari libur berhasil ditambahkan.'); }
        Response::redirect(url('admin/hari-libur'));
    }

    public function destroy(int $id): void {
        CSRF::check(); DB::table('hari_libur')->where('id', $id)->where('school_id', current_school_id())->delete(); Session::toast('success', 'Hari libur dihapus.'); Response::redirect(url('admin/hari-libur'));
    }
}