<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?= url('admin/siswa') ?>" class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h3 class="text-xl font-bold text-slate-800">Proses Kenaikan Kelas</h3>
            <p class="text-sm text-slate-500">Pindahkan siswa dari tahun ajaran lama ke tahun ajaran baru.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        <form method="POST" action="<?= url('admin/kenaikan-kelas/preview') ?>">
            <?= \App\Core\CSRF::field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Dari Tahun Ajaran (Asal)</label>
                    <select name="ta_asal_id" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach ($taList as $ta): ?>
                            <option value="<?= $ta['id'] ?>"><?= e($ta['nama']) ?> <?= $ta['aktif'] ? '(Aktif)' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ke Tahun Ajaran (Tujuan)</label>
                    <select name="ta_tujuan_id" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50 focus:bg-white">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach ($taList as $ta): ?>
                            <option value="<?= $ta['id'] ?>"><?= e($ta['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Penting:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Pastikan Tahun Ajaran Tujuan sudah dibuat di menu <b>Tahun Ajaran</b>.</li>
                        <li>Sistem akan menampilkan daftar siswa untuk dipetakan kelasnya sebelum disimpan.</li>
                        <li>Siswa kelas 9 dapat langsung dipilih <b>Luluskan</b> pada tahap berikutnya.</li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition flex items-center gap-2">
                    Lanjut ke Preview <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>