<div class="bg-slate-50 min-h-screen pb-24">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center gap-3">
            <a href="<?= url('siswa') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Rekap Kehadiran</h1>
                <p class="text-xs text-slate-500 truncate">
                    <?= e($siswa['nama_lengkap']) ?> · <?= e($siswa['kelas_nama']) ?>
                </p>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 pt-5 space-y-4">

        <!-- Summary -->
        <div class="grid grid-cols-4 gap-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center">
                <p class="text-green-600 font-bold text-lg"><?= $summary['hadir'] ?></p>
                <p class="text-[10px] text-slate-500 uppercase font-semibold mt-0.5">Hadir</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center">
                <p class="text-blue-600 font-bold text-lg"><?= $summary['izin'] ?></p>
                <p class="text-[10px] text-slate-500 uppercase font-semibold mt-0.5">Izin</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center">
                <p class="text-purple-600 font-bold text-lg"><?= $summary['sakit'] ?></p>
                <p class="text-[10px] text-slate-500 uppercase font-semibold mt-0.5">Sakit</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 text-center">
                <p class="text-red-600 font-bold text-lg"><?= $summary['alpha'] ?></p>
                <p class="text-[10px] text-slate-500 uppercase font-semibold mt-0.5">Alpha</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3">
            <form method="GET" class="flex gap-2">
                <select name="bulan" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none" onchange="this.form.submit()">
                    <?php
                    $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    foreach ($namaBulan as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun" class="w-28 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none" onchange="this.form.submit()">
                    <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                        <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="py-2.5 px-3 font-semibold">Tanggal</th>
                        <th class="py-2.5 px-3 font-semibold">Mapel</th>
                        <th class="py-2.5 px-3 font-semibold">Guru</th>
                        <th class="py-2.5 px-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($rekap)): ?>
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500">
                                Tidak ada riwayat presensi di periode ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rekap as $r): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="font-semibold text-xs text-slate-700"><?= date('d M Y', strtotime($r['waktu'])) ?></div>
                                    <div class="text-[10px] text-slate-500"><?= date('H:i', strtotime($r['waktu'])) ?> WIB</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-xs text-slate-700"><?= e($r['mapel_nama']) ?></div>
                                    <div class="text-[10px] text-slate-500"><?= e($r['kelas_nama']) ?></div>
                                </td>
                                <td class="py-3 px-3 text-slate-600 text-xs"><?= e($r['guru_nama']) ?></td>
                                <td class="py-3 px-3 text-center">
                                    <?php
                                    $statusColors = [
                                        'hadir' => 'bg-green-100 text-green-700',
                                        'izin'  => 'bg-blue-100 text-blue-700',
                                        'sakit' => 'bg-purple-100 text-purple-700',
                                        'alpha' => 'bg-red-100 text-red-700',
                                    ];
                                    $cls = $statusColors[$r['status']] ?? 'bg-slate-100 text-slate-700';
                                    ?>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-md <?= $cls ?> uppercase">
                                        <?= $r['status'] ?>
                                    </span>
                                    <?php if ($r['keterangan']): ?>
                                        <div class="text-[9px] text-slate-400 mt-1 max-w-[80px] truncate" title="<?= e($r['keterangan']) ?>"><?= e($r['keterangan']) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>