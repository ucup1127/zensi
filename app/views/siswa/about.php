<div class="bg-slate-50 min-h-screen pb-24">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-lg mx-auto px-4 pt-8 pb-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                <span class="text-2xl font-extrabold">Z.</span>
            </div>
            <h1 class="mt-3 text-lg font-bold text-slate-800">Zensi</h1>
            <p class="text-xs text-slate-500 mt-0.5">Sistem Presensi Sekolah · v1.0.0</p>
        </div>
    </header>

    <div class="max-w-lg mx-auto px-4 pt-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 text-sm text-slate-600">

            <p>
                Zensi dirancang untuk memudahkan pencatatan presensi siswa dan guru secara
                <em>real-time</em>, transparan, dan terintegrasi.
            </p>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="font-bold text-slate-800 mb-2 text-sm">Panduan Siswa</h3>
                <ol class="list-decimal pl-5 space-y-1.5 text-slate-600">
                    <li>Tunggu guru membuka sesi presensi QR.</li>
                    <li>Buka menu <b>Scan</b> di navigasi bawah.</li>
                    <li>Arahkan kamera ke QR yang ditampilkan guru.</li>
                    <li>Presensi otomatis tercatat hadir.</li>
                    <li>Lihat riwayat pada menu <b>Rekap</b>.</li>
                </ol>
            </div>

            <div class="border-t border-slate-100 pt-4 text-center text-[11px] text-slate-400">
                &copy; <?= date('Y') ?> Luthfi Yusuf
            </div>
        </div>
    </div>
</div>