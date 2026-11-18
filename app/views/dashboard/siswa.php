<?php
$user = \App\Core\Auth::user();
$siswa = $siswa ?? [];
$sesiAktif = $sesiAktif ?? null;
$avatarFile = $user['avatar'] ?? null;
$avatarUrl  = $avatarFile ? url('assets/img/avatars/' . $avatarFile) : null;
?>

<div class="bg-slate-50 min-h-screen pb-8">

    <!-- Header -->
    <header class="bg-blue-600 text-white">
        <div class="max-w-4xl mx-auto px-5 pt-8 pb-7">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-blue-100 text-xs font-medium">Halo,</p>
                    <h1 class="text-xl md:text-2xl font-extrabold truncate mt-0.5">
                        <?= e($user['nama_lengkap'] ?? 'Siswa') ?>
                    </h1>
                    <div class="flex items-center gap-2 mt-2 text-[11px] font-semibold">
                        <span class="bg-white/15 px-2.5 py-1 rounded-md">
                            <?= e($siswa['kelas_nama'] ?? '-') ?>
                        </span>
                        <span class="bg-white/15 px-2.5 py-1 rounded-md font-mono">
                            NIS <?= e($siswa['nis'] ?? '-') ?>
                        </span>
                    </div>
                </div>

                <!-- Avatar -->
                <div class="w-14 h-14 md:w-16 md:h-16 shrink-0 rounded-full bg-blue-600 text-white
                            flex items-center justify-center text-2xl font-extrabold ring-4 ring-blue-100 overflow-hidden">
                    <?php if ($avatarUrl): ?>
                        <img src="<?= e($avatarUrl) ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($user['nama_lengkap'] ?? 'S', 0, 1)) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 md:px-6">

        <!-- Kartu Sesi Presensi -->
        <section class="-mt-4">
            <?php if ($sesiAktif): ?>
                <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-blue-600 shadow-sm p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-blue-600 uppercase tracking-wide">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                Sesi Aktif
                            </span>
                            <h2 class="mt-1.5 text-base font-bold text-slate-800 truncate">
                                <?= e($sesiAktif['mapel_nama']) ?>
                                <span class="text-slate-400 font-medium">· <?= e($sesiAktif['kelas_nama']) ?></span>
                            </h2>
                            <p class="text-sm text-slate-500 mt-0.5 truncate">
                                <?= e($sesiAktif['guru_nama']) ?>
                            </p>
                        </div>
                    </div>
                    <a href="<?= url('siswa/scan') ?>"
                       class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700
                              text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                        Scan QR Presensi
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-slate-800">Belum ada sesi presensi</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tunggu guru membuka sesi untuk kelasmu.</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Menu -->
        <section class="mt-8">
            <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3 px-1">Menu</h2>

            <div class="grid grid-cols-2 gap-3 md:gap-4">

                <a href="<?= url('siswa/scan') ?>"
                   class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5
                          hover:border-blue-300 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center
                                group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 8V6a2 2 0 012-2h2M4 16v2a2 2 0 002 2h2M20 8V6a2 2 0 00-2-2h-2M20 16v2a2 2 0 01-2 2h-2M8 12h8"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800 text-sm md:text-base">Scan QR</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Presensi kehadiran</p>
                </a>

                <a href="<?= url('siswa/jadwal') ?>"
                   class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5
                          hover:border-blue-300 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center
                                group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800 text-sm md:text-base">Jadwal</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pelajaran hari ini</p>
                </a>

                <a href="<?= url('siswa/rekap') ?>"
                   class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5
                          hover:border-blue-300 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center
                                group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800 text-sm md:text-base">Rekap</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Riwayat kehadiran</p>
                </a>

                <a href="<?= url('siswa/profile') ?>"
                   class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5
                          hover:border-blue-300 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center
                                group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800 text-sm md:text-base">Profil</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Data & akun saya</p>
                </a>

                <a href="#"  
                class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5 hover:border-blue-300 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800 text-sm md:text-base">Perpustakaan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Buku digital</p>
                </a>

            </div>
        </section>

    </div>
</div>