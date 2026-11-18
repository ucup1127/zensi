<div class="bg-slate-50 min-h-screen pb-24 md:pb-8">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 md:px-8 py-4 flex items-center gap-3">
            <a href="<?= url('guru') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Rekap Siswa</h1>
                <p class="text-xs text-slate-500 truncate">Kelas <?= e($kelas['nama']) ?></p>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 md:px-8 pt-5 space-y-4">

        <!-- Filter -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3">
            <form method="GET" class="flex gap-2">
                <select name="bulan"
                        class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none"
                        onchange="this.form.submit()">
                    <?php
                    $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    foreach ($namaBulan as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun"
                        class="w-28 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none"
                        onchange="this.form.submit()">
                    <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                        <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="py-2.5 px-2 font-semibold text-center w-10">No</th>
                        <th class="py-2.5 px-2 font-semibold text-center">Hari / Tanggal</th>
                        <th class="py-2.5 px-2 font-semibold text-center w-16">Hadir</th>
                        <th class="py-2.5 px-2 font-semibold text-center w-16">Alpha</th>
                        <th class="py-2.5 px-2 font-semibold text-center w-20">Izin/Sakit</th>
                        <th class="py-2.5 px-2 font-semibold text-center w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($rekapHarian)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Tidak ada data presensi di bulan ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rekapHarian as $index => $rh): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-2 text-center text-slate-500 text-xs">
                                    <?= (($page - 1) * 10) + $index + 1 ?>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <div class="font-bold text-slate-800 text-xs md:text-sm whitespace-nowrap">
                                        <?= date('d M Y', strtotime($rh['tanggal'])) ?>
                                    </div>
                                    <div class="text-[10px] md:text-xs text-slate-500"><?= $rh['hari'] ?></div>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="font-bold text-green-600"><?= $rh['summary']['hadir'] ?></span>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="font-bold text-red-600"><?= $rh['summary']['alpha'] ?></span>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="font-bold text-blue-600"><?= $rh['summary']['izin'] + $rh['summary']['sakit'] ?></span>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <a href="<?= url('guru/rekap/siswa/pdf?tanggal=' . $rh['tanggal']) ?>" target="_blank"
                                       class="inline-flex items-center justify-center p-1.5 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 rounded-lg transition-colors"
                                       title="Unduh PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($lastPage > 1): ?>
            <div class="flex justify-between items-center bg-white p-3 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs text-slate-500">
                    Menampilkan <?= min(($page - 1) * 10 + 1, $totalHari) ?>–<?= min($page * 10, $totalHari) ?> dari <?= $totalHari ?> hari
                </span>
                <div class="flex gap-1">
                    <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                        <a href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&page=<?= $i ?>"
                           class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-semibold transition-colors
                           <?= $i == $page ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>