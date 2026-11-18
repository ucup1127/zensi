<div class="max-w-6xl mx-auto">
    <!-- Header & Tombol Back -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?= url('admin/kenaikan-kelas') ?>" class="w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Preview Kenaikan Kelas</h3>
                <p class="text-sm text-slate-500">Petakan kelas tujuan untuk setiap siswa.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= url('admin/kenaikan-kelas/proses') ?>" id="formKenaikan">
        <?= \App\Core\CSRF::field() ?>
        <input type="hidden" name="ta_tujuan_id" value="<?= $taTujuanId ?>">

        <!-- Action Bar Bulk -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-4 flex flex-wrap items-center gap-3">
            <span class="text-sm font-semibold text-slate-700">Aksi Cepat (Pilih Siswa Dulu):</span>
            <button type="button" onclick="applyBulk('naik')" class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">Set Naik Kelas (+1 Tingkat)</button>
            <button type="button" onclick="applyBulk('lulus')" class="bg-green-50 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">Luluskan</button>
            <button type="button" onclick="applyBulk('tinggal')" class="bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 rounded-lg text-xs font-semibold transition">Tinggal Kelas</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                        <th class="py-3 px-4 w-12 text-center"><input type="checkbox" id="checkAll" onchange="toggleAll(this)" class="w-4 h-4 text-blue-600 rounded"></th>
                        <th class="py-3 px-4 font-medium">NIS</th>
                        <th class="py-3 px-4 font-medium">Nama Lengkap</th>
                        <th class="py-3 px-4 font-medium">Kelas Asal</th>
                        <th class="py-3 px-4 font-medium">Kelas Tujuan</th>
                        <th class="py-3 px-4 font-medium">Status Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($siswaList)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">Tidak ada siswa di tahun ajaran asal.</td></tr>
                    <?php else: ?>
                        <?php foreach ($siswaList as $s): ?>
                            <tr class="hover:bg-slate-50 transition" data-tingkat="<?= $s['tingkat_asal'] ?>" data-kelas-asal="<?= $s['kelas_asal_id'] ?>">
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" name="selected[]" value="<?= $s['siswa_id'] ?>" class="row-checkbox w-4 h-4 text-blue-600 rounded">
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-mono text-xs"><?= e($s['nis']) ?></td>
                                <td class="py-3 px-4 font-semibold text-slate-800">
                                    <?= e($s['nama_lengkap']) ?>
                                    <input type="hidden" name="mapping[<?= $s['siswa_id'] ?>][status]" id="status_<?= $s['siswa_id'] ?>" value="naik">
                                </td>
                                <td class="py-3 px-4 text-slate-600"><?= e($s['kelas_asal_nama']) ?> (Tk. <?= $s['tingkat_asal'] ?>)</td>
                                <td class="py-3 px-4">
                                    <select name="mapping[<?= $s['siswa_id'] ?>][kelas_id]" id="kelas_<?= $s['siswa_id'] ?>" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-white" onchange="manualChange(<?= $s['siswa_id'] ?>)">
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($kelasTujuan as $k): ?>
                                            <option value="<?= $k['id'] ?>" data-tingkat="<?= $k['tingkat'] ?>"><?= e($k['nama']) ?> (Tk. <?= $k['tingkat'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="py-3 px-4">
                                    <span id="label_status_<?= $s['siswa_id'] ?>" class="text-xs font-semibold text-blue-600">Naik Kelas</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Simpan -->
        <div class="mt-6 flex justify-end gap-3">
            <a href="<?= url('admin/kenaikan-kelas') ?>" class="px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Batal</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition flex items-center gap-2" onclick="return confirm('Proses kenaikan kelas ini? Data yang disimpan tidak dapat dibatalkan.')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Proses & Simpan
            </button>
        </div>
    </form>
</div>

<script>
    // Toggle semua checkbox
    function toggleAll(source) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = source.checked);
    }

    // Fungsi Aksi Cepat
    function applyBulk(action) {
        const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'));
        if (selected.length === 0) return alert('Pilih minimal satu siswa.');

        selected.forEach(cb => {
            const row = cb.closest('tr');
            const siswaId = cb.value;
            const tingkatAsal = parseInt(row.dataset.tingkat);
            const selectKelas = document.getElementById('kelas_' + siswaId);
            const inputStatus = document.getElementById('status_' + siswaId);
            const labelStatus = document.getElementById('label_status_' + siswaId);

            if (action === 'lulus') {
                inputStatus.value = 'lulus';
                labelStatus.innerText = 'Lulus';
                labelStatus.className = 'text-xs font-semibold text-green-600';
                selectKelas.required = false;
            } 
            else if (action === 'tinggal') {
                inputStatus.value = 'tinggal';
                labelStatus.innerText = 'Tinggal Kelas';
                labelStatus.className = 'text-xs font-semibold text-amber-600';
                selectKelas.value = row.dataset.kelasAsal; // Kembali ke kelas asal
                selectKelas.required = true;
            } 
            else {
                // Naik Kelas (+1 tingkat)
                inputStatus.value = 'naik';
                labelStatus.innerText = 'Naik Kelas';
                labelStatus.className = 'text-xs font-semibold text-blue-600';
                
                // Cari opsi kelas dengan tingkat (tingkatAsal + 1)
                const targetTingkat = tingkatAsal + 1;
                let found = false;
                for (let i = 0; i < selectKelas.options.length; i++) {
                    if (selectKelas.options[i].dataset.tingkat == targetTingkat) {
                        selectKelas.selectedIndex = i;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    alert('Kelas tingkat ' + targetTingkat + ' tidak ditemukan. Silakan pilih manual untuk siswa ' + siswaId);
                }
                selectKelas.required = true;
            }
        });
    }

    // Jika admin memilih manual dari dropdown
    function manualChange(siswaId) {
        const selectKelas = document.getElementById('kelas_' + siswaId);
        const inputStatus = document.getElementById('status_' + siswaId);
        const labelStatus = document.getElementById('label_status_' + siswaId);
        
        // Jika kelas dipilih, pastikan status aktif (naik/tinggal)
        if (selectKelas.value !== '') {
            if (inputStatus.value === 'lulus') {
                inputStatus.value = 'naik'; // Reset if previously lulus
            }
            labelStatus.innerText = 'Disimpan (Aktif)';
            labelStatus.className = 'text-xs font-semibold text-slate-600';
        }
    }
</script>