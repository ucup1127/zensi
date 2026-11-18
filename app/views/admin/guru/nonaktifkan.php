<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?= url('admin/guru') ?>" class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h3 class="text-xl font-bold text-slate-800">Nonaktifkan Guru</h3>
            <p class="text-sm text-slate-500"><?= e($guru['nama_lengkap']) ?></p>
        </div>
    </div>

    <form method="POST" action="<?= url("admin/guru/{$guru['id']}/nonaktifkan") ?>">
        <?= \App\Core\CSRF::field() ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Kiri: Form Status -->
            <div class="md:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-200 p-5 h-fit">
                <h4 class="font-bold text-slate-800 mb-4 border-b pb-2">Detail Status</h4>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status Baru</label>
                        <select name="status" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Status --</option>
                            <option value="pensiun">Pensiun</option>
                            <option value="pindah">Pindah Tugas</option>
                            <option value="berhenti">Berhenti / Mengundurkan Diri</option>
                            <option value="meninggal">Meninggal Dunia</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Berlaku</label>
                        <input type="date" name="tanggal_status" value="<?= date('Y-m-d') ?>" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="2" placeholder="Misal: Pindah ke SMPN 2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t flex justify-end">
                    <button type="button" id="btnNonaktifkan" onclick="konfirmasiNonaktifkan()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl shadow transition">
                        Proses Nonaktifkan
                    </button>
                </div>
            </div>

            <!-- Kanan: Daftar Jadwal -->
            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50">
                    <h4 class="font-bold text-slate-800">Jadwal Mengajar Aktif</h4>
                    <p class="text-xs text-slate-500 mt-1">Guru ini masih memiliki <?= count($jadwalAktif) ?> jadwal. Silakan pilih guru pengganti.</p>
                </div>

                <?php if (empty($jadwalAktif)): ?>
                    <div class="p-8 text-center text-slate-500 text-sm">
                        Guru ini tidak memiliki jadwal aktif di tahun ajaran ini. Aman untuk dinonaktifkan.
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($jadwalAktif as $j): ?>
                            <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50">
                                <div>
                                    <p class="font-bold text-slate-800 text-sm"><?= e($j['mapel_nama']) ?> - <?= e($j['kelas_nama']) ?></p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        <?= ucfirst($j['hari']) ?>, Jam ke-<?= $j['jam_ke'] ?> (<?= substr($j['jam_mulai'], 0, 5) ?>-<?= substr($j['jam_selesai'], 0, 5) ?>)
                                    </p>
                                </div>
                                <div class="w-48 shrink-0">
                                    <select name="reassign[<?= $j['jadwal_id'] ?>]" class="w-full px-3 py-1.5 border border-amber-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 outline-none bg-amber-50" required>
                                        <option value="">-- Pilih Pengganti --</option>
                                        <?php foreach ($guruPengganti as $gp): ?>
                                            <option value="<?= $gp['id'] ?>"><?= e($gp['nama_lengkap']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <script>
    function konfirmasiNonaktifkan() {
        Swal.fire({
            title: 'Nonaktifkan Guru?',
            text: 'Akun guru ini akan dinonaktifkan dan tidak bisa login lagi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Warna merah (red-600)
            cancelButtonColor: '#64748b',  // Warna slate (slate-500)
            confirmButtonText: 'Ya, Nonaktifkan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form secara manual setelah klik OK
                document.querySelector('form[action*="nonaktifkan"]').submit();
            }
        });
    }
</script>
</div>