<div class="bg-slate-50 min-h-screen pb-24 md:pb-8">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-lg mx-auto px-4 pt-8 pb-8 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center text-3xl font-extrabold ring-4 ring-blue-100">
                <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
            </div>
            <h1 class="mt-3 text-lg font-bold text-slate-800"><?= e($user['nama_lengkap']) ?></h1>
            <p class="text-xs text-slate-500 font-mono mt-0.5">@<?= e($user['username']) ?></p>
        </div>
    </header>

    <div class="max-w-lg mx-auto px-4 pt-5 space-y-4">

        <!-- Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100 overflow-hidden">

            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</p>
                    <p class="text-sm text-slate-700 font-medium mt-0.5"><?= e($user['nama_lengkap']) ?></p>
                </div>
            </div>

            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">NIP / NBM</p>
                    <p class="text-sm text-slate-700 font-mono mt-0.5"><?= e($nip) ?></p>
                </div>
            </div>

            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</p>
                    <p class="text-sm text-slate-700 font-medium mt-0.5"><?= e($waliKelas) ?></p>
                </div>
            </div>

            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        <?php if (empty($mapelDiampu)): ?>
                            <p class="text-slate-500 text-xs italic">Belum ada mapel</p>
                        <?php else: ?>
                            <?php foreach ($mapelDiampu as $m): ?>
                                <span class="bg-blue-50 text-blue-700 text-[11px] px-2 py-1 rounded-md font-semibold"><?= e($m['nama']) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">No. HP</p>
                    <p class="text-sm text-slate-700 font-medium mt-0.5"><?= e($noHp) ?></p>
                </div>
            </div>

        </div>

        <!-- Logout -->
        <button onclick="confirmLogout()"
                class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 font-semibold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar Akun
        </button>
    </div>
</div>