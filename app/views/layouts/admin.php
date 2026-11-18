<?php 
$user = \App\Core\Auth::user(); 
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$isActive = function($path) use ($currentUri) {
    $url = parse_url(url($path), PHP_URL_PATH);
    return $currentUri === $url;
};

$activeClass = 'bg-blue-50 text-blue-600 font-semibold md:border-r-4 md:border-blue-600';
$inactiveClass = 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin') ?> — Presensi Sekolah</title>
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased h-screen overflow-hidden flex">

    <!-- ================= SIDEBAR ================= -->
    <aside id="sidebar" class="bg-white border-r border-slate-200 flex flex-col shrink-0 transition-all duration-300 ease-in-out z-40 fixed md:relative h-full w-64 -translate-x-full md:translate-x-0">
        
        <!-- Logo & Toggle -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100 shrink-0">
            <a href="<?= url('admin') ?>" id="sidebarLogo" class="flex items-center gap-2 transition-opacity duration-200">
                <span class="text-2xl font-extrabold text-blue-600 tracking-tight">Zensi<span class="text-yellow-400">.</span></span>
            </a>
            <button onclick="toggleSidebar()" class="text-slate-400 hover:text-slate-600 hidden md:block">
                <svg id="iconToggle" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
            </button>
        </div>

        <!-- Dropdown Tahun Ajaran di Sidebar -->
        <?php 
            // Ambil list TA untuk dropdown
            $listTa = \App\Core\DB::table('tahun_ajaran')->where('school_id', current_school_id())->orderBy('nama', 'DESC')->get();
            $activeTaId = \App\Core\Session::get('tahun_ajaran_id') ?? (\App\Core\DB::table('tahun_ajaran')->where('school_id', current_school_id())->where('aktif', 1)->first()['id'] ?? 0);
        ?>
        <div class="px-4 py-4 border-b border-slate-100 shrink-0">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 sidebar-text">Tahun Ajaran Aktif</label>
            <form action="<?= url('admin/ganti-tahun-ajaran') ?>" method="POST" id="formGantiTa">
                <?= \App\Core\CSRF::field() ?>
                <select name="tahun_ajaran_id" onchange="document.getElementById('formGantiTa').submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-semibold cursor-pointer">
                    <?php foreach ($listTa as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= $ta['id'] == $activeTaId ? 'selected' : '' ?>>
                            <?= e($ta['nama']) ?> <?= $ta['aktif'] ? '(Aktif)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        
        <div id="sidebarMenuTitle" class="px-6 mb-2 mt-4 text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap transition-opacity duration-200 shrink-0">Admin Tool</div>
        
        <!-- Menu Navigasi -->
        <nav class="flex-1 overflow-y-auto py-2 overflow-x-hidden">
            <ul class="space-y-1">
                <li>
                    <a href="<?= url('admin') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/tahun-ajaran') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/tahun-ajaran') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Tahun Ajaran</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/kelas') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/kelas') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/mapel') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/mapel') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Mata Pelajaran</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/slot-jam') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/slot-jam') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Slot Jam</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/guru') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/guru') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Data Guru</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/siswa') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/siswa') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Data Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/monitoring-sesi') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/monitoring-sesi') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Monitoring Sesi</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/rekap-global') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/rekap-global') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Rekap Global</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/jadwal') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/jadwal') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Jadwal Pelajaran</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/hari-libur') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('admin/hari-libur') ? $activeClass : $inactiveClass ?>">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-text whitespace-nowrap">Hari Libur</span>
                    </a>
                </li>
                <?php if (\App\Core\Auth::role() === 'guru'): ?>
                    <li>
                        <a href="<?= url('guru/buka-sesi') ?>" class="flex items-center gap-4 px-6 py-3 text-sm transition <?= $isActive('guru/buka-sesi') ? $activeClass : $inactiveClass ?>">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span class="sidebar-text whitespace-nowrap">Buka Sesi</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Logout Button -->
        <div class="p-6 border-t border-slate-100 shrink-0">
            <form id="logoutForm" method="post" action="<?= url('logout') ?>" class="m-0">
                <?= \App\Core\CSRF::field() ?>
                <button type="button" onclick="confirmLogout()" class="flex items-center gap-4 text-sm font-medium text-slate-500 hover:text-red-600 transition w-full text-left" title="Log Out">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span class="sidebar-text whitespace-nowrap">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay Gelap untuk Mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden md:hidden transition-opacity" onclick="toggleMobileSidebar()"></div>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="flex-1 flex flex-col min-w-0 h-full w-full">
        
        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8 shrink-0 z-10">
            <div class="flex items-center gap-4">
                <!-- Burger untuk HP -->
                <button onclick="toggleMobileSidebar()" class="md:hidden text-slate-600 hover:text-slate-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-xl font-semibold text-slate-800 hidden md:block"><?= e($title ?? 'Dashboard') ?></h2>
            </div>
            
            <!-- Profil Kanan Atas (Link ke Halaman Profil) -->
            <a href="<?= url('admin/profil') ?>" class="flex items-center gap-4 hover:opacity-80 transition cursor-pointer">
                <div class="text-right hidden md:block">
                    <div class="text-sm font-bold text-slate-800"><?= e($user['nama_lengkap'] ?? '') ?></div>
                    <div class="text-xs text-slate-500 font-medium capitalize"><?= e($user['role'] ?? '') ?></div>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 border-2 border-white shadow-sm flex items-center justify-center text-blue-600 font-bold text-lg">
                    <?= strtoupper(substr($user['nama_lengkap'] ?? 'A', 0, 1)) ?>
                </div>
            </a>
        </header>

        <!-- Konten Halaman -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            <!-- Flash Message Manual (Jika tidak pakai toast) -->
            <?php if ($msg = \App\Core\Session::flash('success')): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?= e($msg) ?>
                </div>
            <?php endif; ?>
            
            <?= $content ?>
        </main>
    </div>

    <!-- ================= SCRIPTS ================= -->
    <script>
        // --- Sidebar Toggle Desktop ---
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const logo = document.getElementById('sidebarLogo');
            const menuTitle = document.getElementById('sidebarMenuTitle');
            const texts = document.querySelectorAll('.sidebar-text');
            const icon = document.getElementById('iconToggle');

            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.replace('w-64', 'w-20');
                logo.style.opacity = '0'; logo.style.width = '0'; logo.style.overflow = 'hidden';
                menuTitle.style.opacity = '0';
                texts.forEach(t => { t.style.opacity = '0'; t.style.width = '0'; t.style.overflow = 'hidden'; });
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>';
                document.querySelectorAll('#sidebar a, #sidebar button').forEach(el => {
                    el.classList.replace('px-6', 'px-[1.15rem]'); el.classList.replace('gap-4', 'gap-0');
                });
            } else {
                sidebar.classList.replace('w-20', 'w-64');
                logo.style.opacity = '1'; logo.style.width = 'auto';
                menuTitle.style.opacity = '1';
                texts.forEach(t => { t.style.opacity = '1'; t.style.width = 'auto'; });
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>';
                document.querySelectorAll('#sidebar a, #sidebar button').forEach(el => {
                    el.classList.replace('px-[1.15rem]', 'px-6'); el.classList.replace('gap-0', 'gap-4');
                });
            }
        }

        // --- Sidebar Toggle Mobile ---
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // --- Konfirmasi Logout (SweetAlert) ---
        function confirmLogout() {
            Swal.fire({
                title: 'Yakin mau keluar?',
                text: "Anda akan keluar dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // --- Konfirmasi Hapus (SweetAlert) ---
        function confirmDelete(formId, url) {
            Swal.fire({
                title: 'Yakin mau hapus?',
                text: "Data akan terhapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>

    <!-- --- Toast Notification (SweetAlert) --- -->
    <?php if ($toast = \App\Core\Session::flash('toast')): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: '<?= $toast['type'] ?>',
            title: '<?= $toast['message'] ?>',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'rounded-xl shadow-lg border border-slate-200' }
        });
    </script>
    <?php endif; ?>

</body>
</html>