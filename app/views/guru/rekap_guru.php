<div class="bg-slate-50 min-h-screen pb-24 md:pb-8">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 md:px-8 py-4 flex items-center gap-3">
            <a href="<?= url('guru') ?>"
               class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base font-bold text-slate-800">Rekap Presensi Saya</h1>
                <p class="text-xs text-slate-500 truncate">Riwayat kehadiran Anda</p>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 md:px-8 pt-5 space-y-4">

        <!-- Filter -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3">
            <form method="GET" class="flex flex-col md:flex-row gap-2">
                <select name="bulan"
                        class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none"
                        onchange="this.form.submit()">
                    <?php
                    $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    foreach ($namaBulan as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="tahun"
                        class="w-full md:w-28 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none"
                        onchange="this.form.submit()">
                    <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                        <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <div class="flex gap-2">
                    <input type="date" name="tanggal" value="<?= e($searchTanggal) ?>"
                           class="w-full md:w-40 px-3 py-2 border border-slate-200 rounded-lg text-sm bg-slate-50 focus:bg-white focus:border-blue-400 outline-none">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shrink-0">
                        Cari
                    </button>
                    <?php if ($searchTanggal): ?>
                        <a href="<?= url('guru/rekap/saya?bulan=' . $bulan . '&tahun=' . $tahun) ?>"
                           class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shrink-0">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                        <th class="py-2.5 px-4 font-semibold w-16">Foto</th>
                        <th class="py-2.5 px-4 font-semibold">Tanggal</th>
                        <th class="py-2.5 px-4 font-semibold">Jam</th>
                        <th class="py-2.5 px-4 font-semibold">Tipe</th>
                        <th class="py-2.5 px-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($presensi)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                Tidak ada riwayat presensi di periode ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($presensi as $p): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-2 px-4">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 cursor-pointer relative group"
                                         onclick="showImage('<?= url('guru/file-presensi/' . basename($p['foto_path'])) ?>')">
                                         
                                         <!-- Gambar Asli -->
                                         <img src="<?= url('guru/file-presensi/' . basename($p['foto_path'])) ?>"
                                              onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                              class="w-full h-full object-cover" alt="Foto">
                                              
                                         <!-- Fallback Jika Gambar Gagal Dimuat -->
                                         <div class="w-full h-full hidden items-center justify-center text-slate-400 bg-slate-100">
                                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                         </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 font-medium text-slate-700 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($p['tanggal'])) ?>
                                </td>

                                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                                    <?= date('H:i', strtotime($p['waktu'])) ?> WIB
                                </td>

                                <td class="py-3 px-4">
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-md
                                        <?= $p['tipe'] === 'datang' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= strtoupper($p['tipe']) ?>
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-slate-600">
                                    <?= ucfirst($p['status'] ?? 'Hadir') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Foto Presensi',
            showConfirmButton: false,
            showCloseButton: true,
            width: '90%',
            padding: '1em',
            customClass: { popup: 'rounded-2xl', image: 'rounded-lg' }
        });
    }
</script>