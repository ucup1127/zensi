<?php
declare(strict_types=1);

$currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
$base = trim(parse_url(url(''), PHP_URL_PATH) ?? '', '/');
if ($base && str_starts_with($currentPath, $base)) {
    $currentPath = trim(substr($currentPath, strlen($base)), '/');
}

$isActive = function (string $path) use ($currentPath): bool {
    $path = trim($path, '/');
    if ($path === '') {
        return $currentPath === '' || $currentPath === 'siswa';
    }
    return $currentPath === $path || str_starts_with($currentPath, $path . '/');
};

$isHome   = $isActive('siswa') && !$isActive('siswa/scan') && !$isActive('siswa/profile');
$isScan   = $isActive('siswa/scan');
$isProfile = $isActive('siswa/profile');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Dashboard Siswa') ?> — Zensi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pb-safe { padding-bottom: calc(6rem + env(safe-area-inset-bottom)); }
        ::-webkit-scrollbar { width: 0; height: 0; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <main class="pb-safe min-h-screen">
        <?= $content ?? '' ?>
    </main>

    <!-- Bottom Navigation -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[92%] max-w-sm z-50 md:hidden">
        <div class="bg-white border border-slate-200 shadow-lg rounded-2xl flex items-center justify-around px-2 py-2 relative">

            <!-- Home -->
            <a href="<?= url('siswa') ?>"
               class="flex flex-col items-center justify-center w-16 h-12 rounded-xl transition-colors
                      <?= $isHome ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' ?>">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>
                </svg>
                <span class="text-[10px] mt-0.5 <?= $isHome ? 'font-bold' : 'font-medium' ?>">Home</span>
            </a>

            <!-- Scan (tengah, menonjol) -->
            <a href="<?= url('siswa/scan') ?>"
               class="absolute left-1/2 -translate-x-1/2 -top-6 flex flex-col items-center">
                <div class="w-14 h-14 rounded-full bg-blue-600 shadow-md flex items-center justify-center
                            ring-4 ring-slate-50 active:scale-95 transition-transform
                            <?= $isScan ? 'bg-blue-700' : '' ?>">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 8V6a2 2 0 012-2h2M4 16v2a2 2 0 002 2h2M20 8V6a2 2 0 00-2-2h-2M20 16v2a2 2 0 01-2 2h-2M8 12h8"/>
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-blue-600 mt-1">Scan</span>
            </a>

            <!-- Profil -->
            <a href="<?= url('siswa/profile') ?>"
               class="flex flex-col items-center justify-center w-16 h-12 rounded-xl transition-colors
                      <?= $isProfile ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' ?>">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] mt-0.5 <?= $isProfile ? 'font-bold' : 'font-medium' ?>">Profil</span>
            </a>

        </div>
    </div>
    <!-- Form Logout Tersembunyi -->
    <form id="logoutForm" method="post" action="<?= url('logout') ?>" class="hidden">
        <?= \App\Core\CSRF::field() ?>
    </form>

    <!-- Script Logout dengan SweetAlert -->
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Yakin mau keluar?',
                text: 'Anda akan keluar dari sistem.',
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
    </script>

    <!-- Pastikan SweetAlert sudah di-load di <head> -->
</body>
</html>
