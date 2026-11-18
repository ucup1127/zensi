<?php
// Pastikan layout auth sudah include SweetAlert2.
?>

<!-- KUNCI LAYAR: w-screen h-screen dan flex -->
<div class="w-screen h-screen flex bg-white font-sans antialiased overflow-hidden">
    
    <!-- ========================================== -->
    <!-- SISI KIRI: BANNER ABSTRAK BIRU (50%)       -->
    <!-- ========================================== -->
    <div class="hidden lg:flex w-1/2 h-full relative bg-[#f8f9fa] items-center justify-center overflow-hidden">
        
        <!-- Gambar Abstrak Biru -->
        <img src="<?= asset('img/blue-abstract.png') ?>" alt="Abstract Background" class="absolute inset-0 w-full h-full object-cover">

        <!-- Card "Hey! Welcome" -->
        <div class="relative z-20 bg-white/95 backdrop-blur-md p-8 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] w-[320px] border border-white/40">
            <h2 class="text-2xl font-bold text-blue-500 mb-1">Hallo!</h2>
            <h3 class="text-2xl font-extrabold text-slate-900 leading-snug">
                Selamat Datang<br>di Presensi Digital Zensi<span class="text-yellow-400">.</span>
            </h3>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SISI KANAN: FORM LOGIN (50%)               -->
    <!-- ========================================== -->
    <div class="w-full lg:w-1/2 h-full flex flex-col justify-center items-center bg-white relative z-10">
        <div class="w-full max-w-md px-10">
            
            <!-- Logo Zensi (Tengah di Mobile, Kiri di Desktop) -->
            <div class="mb-5 flex items-center gap-2 justify-center lg:justify-start">
                <div class="w-10 h-14">
                    <img src="<?= asset('img/logo_zensi.png') ?>" alt="Zensi Logo" class="w-full h-full object-cover">
                </div>
                <span class="text-2xl font-extrabold text-blue-600 tracking-tight">Zensi<span class="text-yellow-400">.</span></span>
            </div>

            <!-- Teks Sambutan (Tengah di Mobile, Kiri di Desktop) -->
            <div class="mb-8 text-center lg:text-left">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Sign In</h1>
                <p class="text-slate-500 text-sm">Masukkan kredensial Anda untuk melanjutkan.</p>
            </div>

            <!-- Blok Error -->
            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg text-sm flex items-start gap-3 animate-pulse">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="text-left">
                        <p class="font-semibold">Login Gagal</p>
                        <p class="text-red-600 mt-0.5"><?= e($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="post" action="<?= url('login') ?>" class="space-y-5 text-left">
                <?= \App\Core\CSRF::field() ?>
                
                <!-- Input Username -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Username / NIP</label>
                    <input type="text" name="username" value="<?= old('username') ?>" required autofocus 
                        class="block w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all" 
                        placeholder="Masukkan Username">
                </div>

                <!-- Input Password -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput" required 
                            class="block w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all" 
                            placeholder="••••••••">
                        
                        <!-- Toggle Password -->
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none">
                            <svg id="eyeOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg id="eyeClosed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all mt-6 tracking-wide">
                    Masuk
                </button>
            </form>

        </div>
    </div>

</div>

<!-- Script Hide/Unhide Password & SweetAlert -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        toggleBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeOpen.classList.toggle('hidden');
            eyeClosed.classList.toggle('hidden');
        });

        // SweetAlert popup
        <?php if (!empty($error)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '<?= e(addslashes($error)) ?>',
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Coba Lagi',
            customClass: { popup: 'rounded-2xl' }
        });
        <?php endif; ?>
    });
</script>