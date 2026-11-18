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
                <h1 class="text-base font-bold text-slate-800">Jadwal Mengajar</h1>
                <p class="text-xs text-slate-500 truncate">Pilih jadwal untuk membuka sesi presensi</p>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 md:px-8 pt-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <?php if (empty($jadwalGuru)): ?>
                <div class="p-8 text-center text-slate-500 text-sm">Belum ada jadwal.</div>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($jadwalGuru as $j): ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h2 class="font-bold text-slate-800 text-sm md:text-base truncate"><?= e($j['mapel_nama']) ?></h2>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">
                                            Kelas <?= e($j['kelas_nama']) ?> &middot; <?= e($j['hari']) ?>
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-1 font-mono">
                                            <?= substr($j['jam_mulai'], 0, 5) ?>–<?= substr($j['jam_selesai'], 0, 5) ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row gap-2 shrink-0">
                                    <form method="post" action="<?= url('guru/buka-sesi') ?>">
                                        <?= \App\Core\CSRF::field() ?>
                                        <input type="hidden" name="jadwal_id" value="<?= $j['jadwal_id'] ?>">
                                        <input type="hidden" name="metode" value="manual">
                                        <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 md:px-4 rounded-lg transition-colors flex items-center gap-1.5"
                                                title="Ceklis manual">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                            <span class="hidden md:inline">Ceklis</span>
                                        </button>
                                    </form>

                                    <form method="post" action="<?= url('guru/buka-sesi') ?>">
                                        <?= \App\Core\CSRF::field() ?>
                                        <input type="hidden" name="jadwal_id" value="<?= $j['jadwal_id'] ?>">
                                        <input type="hidden" name="metode" value="qr">
                                        <button type="submit"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-3 md:px-4 rounded-lg transition-colors flex items-center gap-1.5"
                                                title="Scan QR">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            <span class="hidden md:inline">QR Code</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>