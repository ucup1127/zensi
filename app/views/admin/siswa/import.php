<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?= url('admin/siswa') ?>" class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h3 class="text-xl font-bold text-slate-800">Import Siswa Baru</h3>
            <p class="text-sm text-slate-500">Tambahkan siswa secara massal menggunakan file CSV.</p>
        </div>
    </div>

    <!-- Pesan Gagal (Jika ada) -->
    <?php if ($failed = \App\Core\Session::flash('import_failed')): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
            <p class="font-bold mb-2">Beberapa baris gagal diproses:</p>
            <ul class="list-disc pl-5 space-y-1 max-h-40 overflow-y-auto">
                <?php foreach ($failed as $f): ?>
                    <li><?= e($f) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        
        <!-- Panduan -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-6">
            <h4 class="font-bold text-blue-800 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Panduan Format CSV
            </h4>
            <p class="text-sm text-blue-700 mb-3">File harus berformat <b>.csv</b> dengan pemisah koma (,). Baris pertama wajib berisi header. Urutan kolom:</p>
            
            <div class="bg-white rounded-lg border border-blue-200 overflow-hidden text-xs font-mono mb-3">
                <table class="w-full text-left">
                    <thead class="bg-blue-100 text-blue-800">
                        <tr>
                            <th class="px-3 py-2">Kolom 1</th>
                            <th class="px-3 py-2">Kolom 2</th>
                            <th class="px-3 py-2">Kolom 3</th>
                            <th class="px-3 py-2">Kolom 4</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-600 divide-y divide-blue-100">
                        <tr>
                            <td class="px-3 py-2">Nama Lengkap</td>
                            <td class="px-3 py-2">NIS</td>
                            <td class="px-3 py-2">Tanggal Lahir (YYYY-MM-DD)</td>
                            <td class="px-3 py-2">Nama Kelas (cth: 7A)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-blue-600">Contoh baris: <code>Andi Pratama,25004,2013-05-12,7A</code></p>
            <p class="text-xs text-blue-600 mt-1">Password otomatis: <b>DDMMYYYY</b> (misal: 12052013).</p>
            
            <a href="data:text/csv;charset=utf-8,nama_lengkap%2Cnis%2Ctanggal_lahir%2Ckelas%0AAndi%20Pratama%2C25004%2C2013-05-12%2C7A" download="template_siswa.csv" class="inline-flex items-center gap-2 mt-3 text-xs font-bold text-blue-700 bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Template CSV
            </a>
        </div>

        <!-- Form Upload -->
        <form method="POST" action="<?= url('admin/siswa/import') ?>" enctype="multipart/form-data">
            <?= \App\Core\CSRF::field() ?>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih File CSV</label>
                <input type="file" name="file_csv" accept=".csv, .txt" required 
                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?= url('admin/siswa') ?>" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>