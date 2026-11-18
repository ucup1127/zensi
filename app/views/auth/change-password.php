<div class="w-full max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Ganti Password</h2>
            <p class="text-slate-500 text-sm mt-2">
                <?= $wajib ? 'Anda wajib mengganti password default sebelum melanjutkan.' : 'Amankan akun Anda dengan password baru.' ?>
            </p>
        </div>

        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-6 border border-red-100">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('ganti-password') ?>" class="space-y-5">
            <?= \App\Core\CSRF::field() ?>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Saat Ini</label>
                <input type="password" name="current_password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                <input type="password" name="new_password" required minlength="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required minlength="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition shadow-md mt-2">
                Simpan Password
            </button>
        </form>

        <?php if (!$wajib): ?>
            <div class="mt-4 text-center">
                <a href="<?= url('dashboard') ?>" class="text-sm text-blue-600 hover:underline">Kembali ke Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>