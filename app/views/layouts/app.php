<?php $user = \App\Core\Auth::user(); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Dashboard') ?> — Presensi Sekolah</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a href="<?= url('dashboard') ?>" class="brand">Presensi</a>
            <div class="topbar-right">
                <span class="user-name"><?= e($user['nama_lengkap'] ?? '') ?></span>
                <form method="post" action="<?= url('logout') ?>" class="inline">
                    <?= \App\Core\CSRF::field() ?>
                    <button type="submit" class="btn-link">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if ($msg = \App\Core\Session::flash('success')): ?>
            <div class="alert alert-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = \App\Core\Session::flash('error')): ?>
            <div class="alert alert-error"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = \App\Core\Session::flash('info')): ?>
            <div class="alert alert-info"><?= e($msg) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>