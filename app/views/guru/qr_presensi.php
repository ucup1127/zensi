<div class="bg-slate-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center gap-3">
            <a href="<?= url('guru') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Scan QR Presensi</h1>
                <p class="text-xs text-slate-500 truncate">
                    <?= e($sesi['mapel_nama']) ?> — <?= e($sesi['kelas_nama']) ?>
                </p>
            </div>
        </div>
    </header>

    <div class="flex-1 flex flex-col items-center justify-center px-4 py-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center max-w-sm w-full relative overflow-hidden">

            <div id="loadingOverlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center hidden">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <p class="text-sm font-semibold text-slate-500 mb-3">Minta siswa scan QR ini</p>

            <div class="w-64 h-64 mx-auto bg-slate-100 rounded-xl flex items-center justify-center mb-4 border border-slate-200 overflow-hidden relative">
                <img id="qrImage" src="" alt="QR Code" class="w-full h-full object-contain hidden">
                <p id="qrPlaceholder" class="text-slate-400 text-sm">Memuat QR...</p>
            </div>

            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Diperbarui dalam: <span id="countdown">8</span>s
            </div>
            <p class="text-xs text-slate-400 mt-3">Jangan tutup halaman ini sampai presensi selesai.</p>
        </div>

        <div class="mt-6 flex gap-3 w-full max-w-sm">
            <a href="<?= url('guru/presensi/' . $sesi['id']) ?>"
               class="flex-1 bg-white border border-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl text-center hover:bg-slate-50 transition-colors text-sm">
                Ceklis Manual
            </a>
            <form method="post" action="<?= url('guru/presensi/' . $sesi['id'] . '/simpan') ?>" class="flex-1" id="formTutupSesi">
                <?= \App\Core\CSRF::field() ?>
                <button type="button" onclick="konfirmasiTutupSesi()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm">
                    Tutup Sesi
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const sesiId = <?= (int) $sesi['id'] ?>;
    const qrImage = document.getElementById('qrImage');
    const qrPlaceholder = document.getElementById('qrPlaceholder');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const countdownEl = document.getElementById('countdown');

    let countdown = 8;
    let timerInterval;

    function fetchNewQr() {
        loadingOverlay.classList.remove('hidden');
        fetch(`<?= url('guru/sesi-qr') ?>/${sesiId}/token`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    qrImage.src = data.qr_image;
                    qrImage.classList.remove('hidden');
                    qrPlaceholder.classList.add('hidden');
                    resetCountdown();
                }
                loadingOverlay.classList.add('hidden');
            })
            .catch((err) => {
                console.error('Gagal ambil QR:', err);
                loadingOverlay.classList.add('hidden');
            });
    }

    function resetCountdown() {
        countdown = 8;
        countdownEl.innerText = countdown;
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            countdown--;
            countdownEl.innerText = countdown;
            if (countdown <= 0) fetchNewQr();
        }, 1000);
    }

        function konfirmasiTutupSesi() {
        Swal.fire({
            title: 'Tutup sesi sekarang?',
            text: 'Siswa yang belum scan akan otomatis ditandai Alpha.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tutup',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formTutupSesi');
                if (!form.dataset.submitting) {
                    form.dataset.submitting = '1';
                    form.submit();
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => fetchNewQr());
</script>