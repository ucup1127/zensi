<?php
$avatarFile = $user['avatar'] ?? null;
$avatarUrl  = $avatarFile ? url('assets/img/avatars/' . $avatarFile) : null;
$avatarList = \App\Controllers\Web\DashboardController::AVATARS;
?>
<div class="bg-slate-50 min-h-screen pb-24">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-lg mx-auto px-4 pt-8 pb-8 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center
                        text-3xl font-extrabold ring-4 ring-blue-100 overflow-hidden">
                <?php if ($avatarUrl): ?>
                    <img src="<?= e($avatarUrl) ?>" alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <h1 class="mt-3 text-lg font-bold text-slate-800"><?= e($user['nama_lengkap']) ?></h1>
            <p class="text-xs text-slate-500 font-mono mt-0.5">@<?= e($user['username']) ?></p>

            <button type="button" onclick="document.getElementById('modalAvatar').classList.remove('hidden')"
                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700
                           bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Ganti Karakter
            </button>
        </div>
    </header>

    <div class="max-w-lg mx-auto px-4 pt-5 space-y-4">

        <!-- Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100 overflow-hidden">

            <!-- Nama -->
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

            <!-- NIS -->
            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">NIS</p>
                    <p class="text-sm text-slate-700 font-mono mt-0.5"><?= e($siswa['nis'] ?? '-') ?></p>
                </div>
            </div>

            <!-- Kelas -->
            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kelas</p>
                    <p class="text-sm text-slate-700 font-medium mt-0.5">
                        <?= e($siswa['kelas_nama'] ?? '-') ?>
                        <span class="text-slate-400">· Tingkat <?= e($siswa['tingkat'] ?? '-') ?></span>
                    </p>
                </div>
            </div>

            <!-- Wali Kelas -->
            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</p>
                    <p class="text-sm text-slate-700 font-medium mt-0.5"><?= e($waliKelas ?? '-') ?></p>
                </div>
            </div>

            <!-- Kontak -->
            <div class="p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">No. HP</p>
                    <p class="text-sm text-slate-700 font-medium mt-0.5"><?= e($siswa['no_hp'] ?? '-') ?></p>
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
<!-- Modal Pilih Avatar -->
<div id="modalAvatar" class="hidden fixed inset-0 z-[60] bg-slate-900/50 flex items-end md:items-center justify-center p-0 md:p-4">
    <div class="bg-white w-full md:max-w-md rounded-t-2xl md:rounded-2xl shadow-xl max-h-[85vh] flex flex-col
                pb-[env(safe-area-inset-bottom)] md:pb-0">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0">
            <h2 class="font-bold text-slate-800 text-sm">Pilih Karakter</h2>
            <button type="button" onclick="document.getElementById('modalAvatar').classList.add('hidden')"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="<?= url('siswa/profile/avatar') ?>" class="flex-1 min-h-0 flex flex-col">
            <?= \App\Core\CSRF::field() ?>

            <!-- Area scroll: grid avatar -->
            <div class="flex-1 overflow-y-auto">
                <div class="grid grid-cols-4 gap-3 p-5">
                    <?php foreach ($avatarList as $file): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="avatar" value="<?= e($file) ?>"
                                   class="peer sr-only" <?= $avatarFile === $file ? 'checked' : '' ?>>
                            <div class="aspect-square rounded-xl bg-slate-50 border-2 border-transparent
                                        peer-checked:border-blue-600 peer-checked:bg-blue-50
                                        hover:border-slate-300 transition-colors overflow-hidden p-1.5">
                                <img src="<?= url('assets/img/avatars/' . $file) ?>" alt="" class="w-full h-full object-contain">
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tombol aksi: sticky di bawah, tidak ikut scroll -->
            <div class="px-5 pb-5 pt-3 border-t border-slate-100 bg-white flex gap-2 shrink-0">
                <button type="button" onclick="document.getElementById('modalAvatar').classList.add('hidden')"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-sm font-semibold text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>