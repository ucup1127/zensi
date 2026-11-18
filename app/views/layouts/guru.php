<?php
$user = \App\Core\Auth::user();
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$isActive = function ($path) use ($currentUri) {
    $url = parse_url(url($path), PHP_URL_PATH);
    return $currentUri === $url;
};

$isHome      = $isActive('guru');
$isPresensi  = $isActive('guru/presensi-saya') || $isActive('guru/buka-sesi');
$isProfile   = $isActive('guru/profile');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= \App\Core\CSRF::token() ?>">
    <title><?= e($title ?? 'Guru') ?> — Zensi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 0; height: 0; }
        .pb-safe { padding-bottom: calc(6rem + env(safe-area-inset-bottom)); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <main class="pb-safe min-h-screen w-full">
        <?= $content ?>
    </main>

    <!-- Bottom Nav -->
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[92%] max-w-sm z-50 md:hidden">
        <div class="bg-white border border-slate-200 shadow-lg rounded-2xl flex items-center justify-around px-2 py-2 relative">

            <a href="<?= url('guru') ?>"
               class="flex flex-col items-center justify-center w-16 h-12 rounded-xl transition-colors
                      <?= $isHome ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' ?>">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>
                </svg>
                <span class="text-[10px] mt-0.5 <?= $isHome ? 'font-bold' : 'font-medium' ?>">Home</span>
            </a>

            <a href="<?= url('guru/buka-sesi') ?>"
               class="absolute left-1/2 -translate-x-1/2 -top-6 flex flex-col items-center">
                <div class="w-14 h-14 rounded-full bg-blue-600 shadow-md flex items-center justify-center
                            ring-4 ring-slate-50 active:scale-95 transition-transform
                            <?= $isPresensi ? 'bg-blue-700' : '' ?>">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-blue-600 mt-1">Presensi</span>
            </a>

            <a href="<?= url('guru/profile') ?>"
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

    <form id="logoutForm" method="post" action="<?= url('logout') ?>" class="hidden">
        <?= \App\Core\CSRF::field() ?>
    </form>

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
                if (result.isConfirmed) document.getElementById('logoutForm').submit();
            });
        }
    </script>

    <?php if ($toast = \App\Core\Session::flash('toast')): ?>
    <script>
        Swal.fire({
            toast: true, position: 'top-end', icon: '<?= $toast['type'] ?>', title: '<?= $toast['message'] ?>',
            showConfirmButton: false, timer: 3000, timerProgressBar: true,
            customClass: { popup: 'rounded-xl shadow-lg border border-slate-200' }
        });
    </script>
    <?php endif; ?>
</body>
</html>