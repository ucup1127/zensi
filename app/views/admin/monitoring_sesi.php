<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Monitoring Sesi</h3>
            <p class="text-sm text-slate-500 mt-1">Pantau sesi presensi yang sedang berlangsung saat ini.</p>
        </div>
        <button onclick="location.reload()" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh Data
        </button>
    </div>

    <!-- Info Banner -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">Informasi:</p>
            <p>Jika ada guru yang lupa menutup sesi, Anda dapat menutupnya secara paksa dari halaman ini. Siswa yang belum melakukan presensi akan otomatis ditandai <b>Alpha</b>.</p>
        </div>
    </div>

    <!-- Tabel Sesi -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-4 font-semibold">Guru</th>
                    <th class="py-3 px-4 font-semibold">Mata Pelajaran</th>
                    <th class="py-3 px-4 font-semibold">Kelas</th>
                    <th class="py-3 px-4 font-semibold">Waktu Dibuka</th>
                    <th class="py-3 px-4 font-semibold">Mode</th>
                    <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if (empty($sesiAktif)): ?>
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-500">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tidak ada sesi presensi yang sedang aktif saat ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sesiAktif as $s): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-semibold text-slate-800"><?= e($s['guru_nama']) ?></td>
                            <td class="py-3 px-4 text-slate-700"><?= e($s['mapel_nama']) ?></td>
                            <td class="py-3 px-4 text-slate-600"><?= e($s['kelas_nama']) ?></td>
                            <td class="py-3 px-4 text-slate-600 text-xs">
                                <?= date('H:i', strtotime($s['dibuka_at'])) ?> WIB
                                <div class="text-[10px] text-slate-400 mt-0.5">Jam ke-<?= $s['jam_ke'] ?></div>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($s['mode'] === 'scan'): ?>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-md bg-blue-100 text-blue-700 uppercase">Scan QR</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-md bg-amber-100 text-amber-700 uppercase">Manual</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="konfirmasiTutup(<?= $s['sesi_id'] ?>)" class="bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    Tutup Paksa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function konfirmasiTutup(id) {
        Swal.fire({
            title: 'Tutup Paksa Sesi?',
            text: 'Siswa yang belum melakukan presensi akan otomatis ditandai Alpha.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tutup Sesi',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= url('admin/monitoring-sesi') ?>/${id}/tutup`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '<?= \App\Core\CSRF::token() ?>';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>