<div class="max-w-5xl mx-auto">
    <!-- Tombol Kembali & Judul -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?= url('admin') ?>" class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-300 shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Profil Administrator</h3>
                <p class="text-sm text-slate-500">Kelola informasi akun dan keamanan Anda.</p>
            </div>
        </div>
    </div>

    <!-- Layout Grid: Kiri Kartu Profil, Kanan Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KIRI: Kartu Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex flex-col items-center text-center sticky top-6">
                <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-5xl shadow-lg mb-5">
                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                </div>
                <h4 class="text-xl font-bold text-slate-800"><?= e($user['nama_lengkap']) ?></h4>
                <span class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Role: <?= e($user['role']) ?>
                </span>
                
                <div class="w-full border-t border-slate-100 mt-6 pt-6">
                    <div class="flex items-center justify-between text-sm mb-3">
                        <span class="text-slate-500">Status Akun</span>
                        <span class="font-semibold text-green-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Aktif
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Username</span>
                        <span class="font-semibold text-slate-800">@<?= e($user['username']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN: Form Edit -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                <h5 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Informasi Akun
                </h5>

                <form method="post" action="<?= url('admin/profil') ?>" class="space-y-6">
                    <?= \App\Core\CSRF::field() ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= e($user['nama_lengkap']) ?>" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-slate-50 focus:bg-white">
                        </div>
                        
                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
                            <input type="text" name="username" value="<?= e($user['username']) ?>" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-slate-50 focus:bg-white">
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password Baru <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-slate-50 focus:bg-white pr-12">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Gunakan kombinasi huruf dan angka untuk keamanan ekstra.</p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="<?= url('admin') ?>" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md shadow-blue-200 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>