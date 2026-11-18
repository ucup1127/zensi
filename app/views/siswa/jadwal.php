<div class="bg-slate-50 min-h-screen pb-24">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center gap-3">
            <a href="<?= url('siswa') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Jadwal Pelajaran</h1>
                <p class="text-xs text-slate-500 truncate">Kelas <?= e($siswa['kelas_nama']) ?></p>
            </div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <?php if (empty($groupedJadwal)): ?>
                <div class="p-10 text-center text-slate-500">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">Belum ada jadwal pelajaran.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-200">
                    <?php foreach ($groupedJadwal as $hari => $jadwals): ?>
                        <div class="bg-slate-50 px-4 py-2 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider"><?= $hari ?></h3>
                        </div>

                        <?php foreach ($jadwals as $j): ?>
                            <div class="p-4 hover:bg-slate-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="font-bold text-slate-800 text-sm"><?= e($j['mapel_nama']) ?></h4>
                                            <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md shrink-0">
                                                Jam <?= $j['jam_ke'] ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate"><?= e($j['guru_nama']) ?></p>
                                        <p class="text-[11px] text-slate-400 mt-1 font-mono">
                                            <?= substr($j['jam_mulai'], 0, 5) ?> – <?= substr($j['jam_selesai'], 0, 5) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>