<?php $user = \App\Core\Auth::user(); ?>

<div class="bg-slate-50 min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 md:px-8 py-6 md:py-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Kiri: Info -->
            <div class="flex items-center gap-4">
                <!-- Avatar Desktop -->
                <div class="hidden md:flex w-16 h-16 rounded-full bg-blue-600 text-white items-center justify-center font-bold text-2xl shadow-sm shrink-0">
                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Selamat datang kembali,</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                        <?= e($user['nama_lengkap']) ?>
                    </h1>
                    <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span><?= $hari ?>, <?= $tanggal ?></span>
                    </div>
                </div>
            </div>

            <!-- Kanan: Status & Avatar Mobile -->
            <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto mt-2 md:mt-0 border-t md:border-t-0 border-slate-100 pt-4 md:pt-0">
                <div class="flex-1 md:flex-none">
                    <?php if ($sesiAktif): ?>
                        <div class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 bg-blue-50 px-4 py-2 rounded-xl border border-blue-100">
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            Sesi Presensi Aktif
                        </div>
                    <?php else: ?>
                        <div class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 bg-slate-100 px-4 py-2 rounded-xl border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Tidak ada sesi aktif
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Avatar Mobile -->
                <div class="md:hidden w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm shrink-0">
                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- KOLOM KIRI (8/12) - Menu & Jadwal -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Menu Grid -->
                <section>
                    <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        Menu Cepat
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <!-- Presensi Saya -->
                        <a href="<?= url('guru/presensi-saya') ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between h-full">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Presensi Saya</h3>
                                <p class="text-xs text-slate-500 mt-1">Foto kehadiran</p>
                            </div>
                        </a>

                        <!-- Presensi Siswa -->
                        <a href="<?= url('guru/buka-sesi') ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between h-full">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Presensi Siswa</h3>
                                <p class="text-xs text-slate-500 mt-1">Buka sesi kelas</p>
                            </div>
                        </a>

                        <!-- Jadwal -->
                        <a href="<?= url('guru/jadwal') ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between h-full">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Jadwal Mengajar</h3>
                                <p class="text-xs text-slate-500 mt-1">Lihat semua jadwal</p>
                            </div>
                        </a>

                        <!-- Rekap Siswa -->
                        <a href="<?= url('guru/rekap/siswa') ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between h-full">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Rekap Siswa</h3>
                                <p class="text-xs text-slate-500 mt-1">Kehadiran per kelas</p>
                            </div>
                        </a>

                        <!-- Rekap Saya -->
                        <a href="<?= url('guru/rekap/saya') ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between h-full">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Rekap Saya</h3>
                                <p class="text-xs text-slate-500 mt-1">Riwayat kehadiran</p>
                            </div>
                        </a>

                        <!-- Profil -->
                        <a href="<?= url('guru/profile') ?>" class="group bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between h-full">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Profil</h3>
                                <p class="text-xs text-slate-500 mt-1">Data & akun</p>
                            </div>
                        </a>
                    </div>
                </section>

                <!-- Jadwal Hari Ini -->
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="font-bold text-slate-800">Jadwal Hari Ini</h2>
                        <a href="<?= url('guru/jadwal') ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat Semua</a>
                    </div>

                    <?php if (empty($jadwalHariIni)): ?>
                        <div class="p-10 text-center">
                            <p class="text-sm text-slate-500">Tidak ada jadwal mengajar hari ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-slate-100">
                            <?php foreach ($jadwalHariIni as $j): ?>
                                <div class="p-4 md:px-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex flex-col items-center justify-center shrink-0">
                                            <span class="text-[9px] uppercase font-bold leading-none">Jam</span>
                                            <span class="text-base font-extrabold leading-none mt-0.5"><?= $j['jam_ke'] ?></span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800"><?= e($j['mapel_nama']) ?></p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                Kelas <?= e($j['kelas_nama']) ?> &middot; <?= substr($j['jam_mulai'], 0, 5) ?>–<?= substr($j['jam_selesai'], 0, 5) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <form method="post" action="<?= url('guru/buka-sesi') ?>" class="shrink-0 w-full md:w-auto">
                                        <?= \App\Core\CSRF::field() ?>
                                        <input type="hidden" name="jadwal_id" value="<?= $j['jadwal_id'] ?>">
                                        <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-5 rounded-lg transition-colors">
                                            Buka Sesi
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- KOLOM KANAN (4/12) - Info & Aksi -->
            <aside class="lg:col-span-4 space-y-6">
                
                <!-- Sesi Aktif Card -->
                <?php if ($sesiAktif): ?>
                <div class="bg-blue-600 rounded-2xl shadow-md p-6 text-white relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                    <p class="text-blue-100 text-xs font-semibold mb-1 uppercase tracking-wider">Sesi Berlangsung</p>
                    <h3 class="text-xl font-bold leading-tight"><?= e($sesiAktif['mapel_nama']) ?></h3>
                    <p class="text-sm text-blue-100 mt-1">Kelas <?= e($sesiAktif['kelas_nama']) ?></p>
                    <p class="text-xs text-blue-200 mt-3">Dibuka pukul <?= date('H:i', strtotime($sesiAktif['dibuka_at'])) ?> WIB</p>
                    
                    <div class="mt-6">
                        <a href="<?= url("guru/presensi/{$sesiAktif['id']}") ?>" class="block w-full text-center bg-white text-blue-700 font-bold py-2.5 rounded-xl hover:bg-slate-50 transition-colors text-sm">
                            Lanjutkan Sesi &rarr;
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info Akademik -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Informasi Akademik</h3>
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <dt class="text-sm text-slate-500">Tahun ajaran</dt>
                            <dd class="text-sm font-bold text-slate-800"><?= date('Y') ?>/<?= (int)date('Y')+1 ?></dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-slate-500">Jadwal hari ini</dt>
                            <dd class="text-sm font-bold text-slate-800"><?= count($jadwalHariIni) ?> Sesi</dd>
                        </div>
                    </dl>
                </div>

                <!-- CTA Presensi Cepat -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Presensi Cepat</h3>
                    <p class="text-xs text-slate-500 mb-4">Catat kehadiran diri sendiri dengan foto.</p>
                    <a href="<?= url('guru/presensi-saya') ?>" class="block w-full text-center bg-blue-50 text-blue-700 hover:bg-blue-100 text-sm font-bold py-2.5 rounded-xl transition-colors">
                        Buka Presensi
                    </a>
                </div>
            </aside>

        </div>
    </div>
</div>