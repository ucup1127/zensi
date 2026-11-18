<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Rekap Global Kehadiran</h3>
            <p class="text-sm text-slate-500 mt-1">Ringkasan kehadiran seluruh siswa per kelas.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak / Export PDF
            </button>
        </div>
    </div>

    <!-- Header Laporan (Hanya Muncul saat Print) -->
    <div class="hidden print:block text-center mb-8">
        <h2 class="text-2xl font-bold">Laporan Rekap Kehadiran Global</h2>
        <p class="text-lg">Bulan: <?= date('F Y', strtotime("$tahun-$bulan-01")) ?></p>
        <p class="text-sm text-slate-500">Sistem Zensi</p>
    </div>

    <!-- Filter Bulan & Tahun -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 no-print">
        <form method="GET" class="flex gap-3">
            <select name="bulan" onchange="this.form.submit()" class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <?php 
                $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                foreach ($namaBulan as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tahun" onchange="this.form.submit()" class="w-32 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                    <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <!-- Tabel Rekap -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden print:shadow-none print:border-none">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider no-print">
                    <th class="py-3 px-4 font-semibold">Kelas</th>
                    <th class="py-3 px-4 font-semibold text-center">Hadir</th>
                    <th class="py-3 px-4 font-semibold text-center">Izin</th>
                    <th class="py-3 px-4 font-semibold text-center">Sakit</th>
                    <th class="py-3 px-4 font-semibold text-center">Alpha</th>
                    <th class="py-3 px-4 font-semibold text-center">Persentase</th>
                </tr>
                <tr class="hidden print:table-row bg-slate-100 border-b border-black text-black text-sm">
                    <th class="py-2 px-4 border border-black">Kelas</th>
                    <th class="py-2 px-4 border border-black text-center">Hadir</th>
                    <th class="py-2 px-4 border border-black text-center">Izin</th>
                    <th class="py-2 px-4 border border-black text-center">Sakit</th>
                    <th class="py-2 px-4 border border-black text-center">Alpha</th>
                    <th class="py-2 px-4 border border-black text-center">Persentase</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm print:divide-black">
                <?php if (empty($rekapData)): ?>
                    <tr><td colspan="6" class="py-8 text-center text-slate-500">Belum ada data kelas.</td></tr>
                <?php else: ?>
                    <?php foreach ($rekapData as $r): ?>
                        <tr class="hover:bg-slate-50 transition print:hover:bg-transparent">
                            <td class="py-3 px-4 font-semibold text-slate-800 print:border print:border-black print:py-1"><?= e($r['kelas_nama']) ?></td>
                            <td class="py-3 px-4 text-center text-green-600 font-medium print:text-black print:border print:border-black print:py-1"><?= $r['hadir'] ?></td>
                            <td class="py-3 px-4 text-center text-blue-600 font-medium print:text-black print:border print:border-black print:py-1"><?= $r['izin'] ?></td>
                            <td class="py-3 px-4 text-center text-purple-600 font-medium print:text-black print:border print:border-black print:py-1"><?= $r['sakit'] ?></td>
                            <td class="py-3 px-4 text-center text-red-600 font-medium print:text-black print:border print:border-black print:py-1"><?= $r['alpha'] ?></td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800 print:border print:border-black print:py-1">
                                <?= $r['persentase'] ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        main { padding: 0 !important; overflow: visible !important; }
        #sidebar, header, .bottom-nav { display: none !important; }
        .print\:block { display: block !important; }
        .print\:table-row { display: table-row !important; }
        .print\:border { border-width: 1px !important; }
        .print\:border-black { border-color: black !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:bg-transparent { background: transparent !important; }
        .print\:text-black { color: black !important; }
    }
</style>