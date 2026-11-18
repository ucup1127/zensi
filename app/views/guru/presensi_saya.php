<div class="bg-slate-50 min-h-screen pb-24 md:pb-8">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-lg mx-auto px-4 md:px-8 py-4 flex items-center gap-3">
            <a href="<?= url('guru') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Presensi Saya</h1>
                <p class="text-xs text-slate-500 truncate"><?= date('l, d F Y') ?></p>
            </div>
        </div>
    </header>

    <div class="max-w-lg mx-auto px-4 md:px-8 pt-5">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <!-- Pilihan Tipe -->
            <div class="flex gap-1 mb-4 bg-slate-100 p-1 rounded-xl">
                <button onclick="setTipe('datang')" id="btn-datang"
                        class="flex-1 py-2 text-sm font-bold rounded-lg bg-white shadow text-blue-700 transition">
                    Presensi Datang
                </button>
                <button onclick="setTipe('pulang')" id="btn-pulang"
                        class="flex-1 py-2 text-sm font-bold rounded-lg text-slate-500 hover:text-slate-700 transition">
                    Presensi Pulang
                </button>
            </div>

            <!-- Area Kamera -->
            <div class="relative bg-slate-900 rounded-xl overflow-hidden aspect-[3/4] md:aspect-video flex flex-col items-center justify-center border border-slate-200">
                <video id="video" autoplay playsinline class="absolute inset-0 w-full h-full object-cover hidden"></video>
                <canvas id="canvas" class="hidden"></canvas>
                <img id="photoPreview" class="absolute inset-0 w-full h-full object-cover hidden">

                <div id="cameraPlaceholder" class="text-center text-slate-400">
                    <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-sm">Kamera belum aktif</p>
                </div>
            </div>

            <!-- Tombol Kontrol -->
            <div class="mt-5 flex flex-col gap-3">
                <button id="btnStartCam" onclick="startCamera()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors">
                    Nyalakan Kamera
                </button>

                <button id="btnCapture" onclick="capturePhoto()"
                        class="hidden w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors">
                    Ambil Foto
                </button>

                <div id="actionButtons" class="hidden grid grid-cols-2 gap-3">
                    <button onclick="retakePhoto()"
                            class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition-colors">
                        Ulangi
                    </button>
                    <button onclick="submitPresensi()" id="btnSubmit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-colors">
                        Kirim Presensi
                    </button>
                </div>
            </div>

            <div id="loadingText" class="hidden text-center text-blue-600 font-medium mt-4 animate-pulse">
                Memproses foto dan menyimpan...
            </div>
        </div>
    </div>
</div>

<script>
    let stream = null;
    let currentTipe = 'datang';
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const photoPreview = document.getElementById('photoPreview');
    const cameraPlaceholder = document.getElementById('cameraPlaceholder');

    function setTipe(tipe) {
        currentTipe = tipe;
        const btnDatang = document.getElementById('btn-datang');
        const btnPulang = document.getElementById('btn-pulang');

        if (tipe === 'datang') {
            btnDatang.className = "flex-1 py-2 text-sm font-bold rounded-lg bg-white shadow text-blue-700 transition";
            btnPulang.className = "flex-1 py-2 text-sm font-bold rounded-lg text-slate-500 hover:text-slate-700 transition";
        } else {
            btnPulang.className = "flex-1 py-2 text-sm font-bold rounded-lg bg-white shadow text-blue-700 transition";
            btnDatang.className = "flex-1 py-2 text-sm font-bold rounded-lg text-slate-500 hover:text-slate-700 transition";
        }
    }

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user", width: { ideal: 720 }, height: { ideal: 1280 } }
            });
            video.srcObject = stream;
            video.classList.remove('hidden');
            cameraPlaceholder.classList.add('hidden');

            document.getElementById('btnStartCam').classList.add('hidden');
            document.getElementById('btnCapture').classList.remove('hidden');
        } catch (err) {
            alert("Gagal mengakses kamera: " + err.message + "\nPastikan Anda mengizinkan akses kamera dan menggunakan HTTPS atau localhost.");
        }
    }

    function capturePhoto() {
        if (!stream) return;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        photoPreview.src = dataUrl;
        photoPreview.classList.remove('hidden');
        video.classList.add('hidden');
        stream.getTracks().forEach(t => t.stop());
        document.getElementById('btnCapture').classList.add('hidden');
        document.getElementById('actionButtons').classList.remove('hidden');
        document.getElementById('actionButtons').classList.add('grid');
    }

    function retakePhoto() {
        photoPreview.classList.add('hidden');
        document.getElementById('actionButtons').classList.add('hidden');
        document.getElementById('actionButtons').classList.remove('grid');
        document.getElementById('btnStartCam').classList.remove('hidden');
        startCamera();
    }

    function submitPresensi() {
        if (!canvas.width) return;
        document.getElementById('actionButtons').classList.add('hidden');
        document.getElementById('loadingText').classList.remove('hidden');
        const base64Image = canvas.toDataURL('image/jpeg', 0.8);

        fetch('<?= url('guru/presensi-saya/simpan') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                image: base64Image,
                tipe: currentTipe,
                csrf_token: '<?= \App\Core\CSRF::token() ?>'
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#2563eb'
                }).then(() => { window.location.href = '<?= url('guru') ?>'; });
            } else {
                Swal.fire('Gagal', data.message, 'error');
                document.getElementById('actionButtons').classList.remove('hidden');
                document.getElementById('loadingText').classList.add('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Cek Console Browser (F12) untuk detail error.', 'error');
        });
    }
</script>