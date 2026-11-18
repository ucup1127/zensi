<div class="bg-slate-50 min-h-screen pb-24">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center gap-3">
            <a href="<?= url('siswa') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Scan QR Presensi</h1>
                <p class="text-xs text-slate-500 truncate">Arahkan kamera ke QR di layar guru</p>
            </div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 pt-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <?php if (!$sesiAktif): ?>
                <div class="text-center py-10">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="font-bold text-slate-700">Belum ada sesi presensi aktif</p>
                    <p class="text-sm text-slate-500 mt-1">Tunggu guru membuka sesi untuk kelasmu.</p>
                    <button onclick="location.reload()"
                            class="mt-5 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 rounded-xl text-sm transition-colors">
                        Cek Ulang
                    </button>
                </div>
            <?php else: ?>
                <div class="mb-4 p-3 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-[10px] text-blue-700 font-bold uppercase tracking-wide">Sesi Aktif</p>
                    <p class="font-bold text-slate-800 text-sm mt-0.5">
                        <?= e($sesiAktif['mapel_nama']) ?>
                        <span class="text-slate-400 font-medium">— <?= e($sesiAktif['guru_nama']) ?></span>
                    </p>
                </div>

                                <!-- Area Kamera -->
                <div id="reader-container" class="relative w-full rounded-xl overflow-hidden bg-slate-900 aspect-square flex items-center justify-center">
                    <div id="reader" class="w-full h-full object-cover transition-transform duration-300 ease-in-out"></div>
                    <div id="zoom-target" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <!-- Border fokus (opsional) -->
                        <div class="w-64 h-64 border-2 border-white/30 rounded-3xl"></div>
                    </div>
                </div>

                <!-- Kontrol Zoom -->
                <div class="mt-4 flex items-center justify-between gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-xs font-semibold text-slate-600">Zoom Kamera</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="adjustZoom(-1)" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center shadow-sm">-</button>
                        <span id="zoomLevel" class="text-sm font-bold text-blue-600 w-8 text-center">1x</span>
                        <button type="button" onclick="adjustZoom(1)" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center shadow-sm">+</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($sesiAktif): ?>
<script>
    const csrfToken = '<?= \App\Core\CSRF::token() ?>';
    const verifyUrl = '<?= url('siswa/scan/verify') ?>';
    let html5QrCode = null;
    let lastScanAt = 0;
    let isSubmitting = false;
    
    // Variabel Zoom
    let currentZoom = 1.0;
    let videoTrack = null;
    const maxZoom = 3.0;

    async function adjustZoom(delta) {
        let newZoom = currentZoom + (delta * 0.5);
        if (newZoom < 1.0) newZoom = 1.0;
        if (newZoom > maxZoom) newZoom = maxZoom;
        
        currentZoom = newZoom;
        document.getElementById('zoomLevel').innerText = currentZoom + 'x';

        // 1. Coba Hardware Zoom
        if (videoTrack) {
            try {
                const capabilities = videoTrack.getCapabilities();
                if (capabilities.zoom) {
                    await videoTrack.applyConstraints({
                        advanced: [{ zoom: currentZoom }]
                    });
                    return; 
                }
            } catch (err) {
                console.log("Hardware zoom not supported.");
            }
        }

        // 2. Fallback Digital Zoom (CSS)
        const videoElement = document.querySelector('#reader video');
        if (videoElement) {
            videoElement.style.transform = `scale(${currentZoom})`;
            videoElement.style.transformOrigin = 'center center';
        }
    }

    async function startScanner(cameraIdOrConfig) {
        document.getElementById('scanStatus').innerText = 'Memuat kamera...';
        html5QrCode = new Html5Qrcode("reader");
        
        try {
            // Konfigurasi untuk QR Proyektor (lebih sensitif & cepat)
            const config = { 
                fps: 15, 
                qrbox: { width: 300, height: 300 },
                aspectRatio: 1.0,
                disableFlip: false
            };

            await html5QrCode.start(cameraIdOrConfig, config, onScanSuccess);
            document.getElementById('scanStatus').innerText = 'Arahkan kamera ke QR...';
            
            // Simpan referensi video track untuk zoom
            setTimeout(() => {
                const video = document.querySelector('#reader video');
                if (video && video.srcObject) {
                    videoTrack = video.srcObject.getVideoTracks()[0];
                    // Set zoom awal ke 1x
                    document.getElementById('zoomLevel').innerText = '1x';
                }
            }, 500);

        } catch (err) {
            document.getElementById('scanStatus').innerText = 'Gagal akses kamera: ' + err;
        }
    }

    async function onScanSuccess(decodedText) {
        const now = Date.now();
        if (isSubmitting || now - lastScanAt < 3000) return;
        lastScanAt = now;
        isSubmitting = true;

        Swal.fire({
            title: 'Memverifikasi...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch(verifyUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ token: decodedText.trim(), csrf_token: csrfToken })
            });
            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonText: 'OK',
                    customClass: { popup: 'rounded-2xl' }
                }).then(() => { window.location.href = '<?= url('siswa') ?>'; });
                if (html5QrCode) await html5QrCode.stop();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message,
                    confirmButtonText: 'Coba Lagi',
                    customClass: { popup: 'rounded-2xl' }
                });
                isSubmitting = false;
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal terhubung ke server.' });
            isSubmitting = false;
        }
    }

    (async () => {
        try {
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras || cameras.length === 0) {
                document.getElementById('scanStatus').innerText = 'Tidak ada kamera terdeteksi.';
                return;
            }
            // Prioritaskan kamera belakang dengan resolusi tertinggi
            const backCam = cameras.find(c => /back|rear|environment/i.test(c.label)) || cameras[cameras.length - 1];
            await startScanner(backCam.id);

            if (cameras.length > 1) {
                const btn = document.getElementById('btnSwitch');
                btn.classList.remove('hidden');
                let idx = cameras.findIndex(c => c.id === backCam.id);
                btn.addEventListener('click', async () => {
                    if (html5QrCode) await html5QrCode.stop();
                    idx = (idx + 1) % cameras.length;
                    await startScanner(cameras[idx].id);
                });
            }
        } catch (e) {
            document.getElementById('scanStatus').innerText = 'Butuh izin kamera. Refresh & izinkan.';
        }
    })();
</script>
<?php endif; ?>