<div class="bg-slate-50 min-h-screen pb-32 md:pb-8">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-5xl mx-auto px-4 md:px-8 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="<?= url('guru') ?>"
                   class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-slate-800 truncate">Ceklis Presensi</h1>
                    <p class="text-xs text-slate-500 truncate">
                        <?= e($sesi['mapel_nama']) ?> — <?= e($sesi['kelas_nama']) ?>
                    </p>
                </div>
            </div>
            <button type="button" onclick="setAllHadir()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 md:px-4 rounded-lg transition-colors shrink-0">
                Hadir Semua
            </button>
        </div>
    </header>

    <form method="post" action="<?= url("guru/presensi/{$sesi['id']}/simpan") ?>" id="formPresensi">
        
        <!-- TAMBAHKAN CSRF TOKEN DI SINI -->
        <?= \App\Core\CSRF::field() ?>

        <div class="max-w-5xl mx-auto px-4 md:px-8 pt-5">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wider">
                            <th class="py-2.5 px-3 font-semibold w-20">NIS</th>
                            <th class="py-2.5 px-3 font-semibold">Nama Lengkap</th>
                            <th class="py-2.5 px-3 font-semibold w-28 text-center">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach ($siswa as $s): ?>
                            <?php
                                $isIzin = isset($izinMap[$s['id']]);
                                $izinAlasan = $isIzin ? $izinMap[$s['id']]['alasan'] : '';
                                $selectedStatus = $isIzin ? 'izin' : 'hadir';
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors align-top">
                                <td class="py-3 px-3 text-slate-500 font-mono text-xs break-all">
                                    <?= e($s['nis'] ?? '-') ?>
                                </td>

                                <td class="py-3 px-3 font-semibold text-slate-800">
                                    <span class="line-clamp-2 leading-tight"><?= e($s['nama_lengkap']) ?></span>

                                    <div id="ket_container_<?= $s['id'] ?>" class="mt-2 <?= $isIzin ? 'block' : 'hidden' ?>">
                                        <input type="text" name="presensi[<?= $s['id'] ?>][keterangan]" id="ket_<?= $s['id'] ?>"
                                               value="<?= e($izinAlasan) ?>" placeholder="Alasan izin..."
                                               class="w-full px-2 py-1.5 border border-slate-200 rounded-md text-xs font-normal bg-white focus:border-blue-400 focus:ring-1 focus:ring-blue-400 outline-none"
                                               <?= $isIzin ? 'required' : '' ?>>
                                    </div>
                                </td>

                                <td class="py-3 px-3">
                                    <div class="flex flex-col gap-2 items-start justify-center">
                                        <label class="flex items-center gap-2 cursor-pointer w-full">
                                            <input type="radio" name="presensi[<?= $s['id'] ?>][status]" value="hadir"
                                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300 shrink-0"
                                                   <?= $selectedStatus === 'hadir' ? 'checked' : '' ?>
                                                   onchange="updateCard(<?= $s['id'] ?>, 'hadir')">
                                            <span class="text-slate-700 text-xs font-medium">Hadir</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer w-full">
                                            <input type="radio" name="presensi[<?= $s['id'] ?>][status]" value="alpha"
                                                   class="w-4 h-4 text-red-600 focus:ring-red-500 border-slate-300 shrink-0"
                                                   <?= $selectedStatus === 'alpha' ? 'checked' : '' ?>
                                                   onchange="updateCard(<?= $s['id'] ?>, 'alpha')">
                                            <span class="text-slate-700 text-xs font-medium">Alpha</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer w-full">
                                            <input type="radio" name="presensi[<?= $s['id'] ?>][status]" value="izin"
                                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300 shrink-0"
                                                   <?= $selectedStatus === 'izin' ? 'checked' : '' ?>
                                                   onchange="updateCard(<?= $s['id'] ?>, 'izin')">
                                            <span class="text-slate-700 text-xs font-medium">Izin</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol simpan -->
        <div class="fixed bottom-28 left-4 right-4 md:static md:bottom-auto md:left-auto md:right-auto md:max-w-5xl md:mx-auto md:px-8 md:mt-5 md:mb-8">
            <button type="button" id="btnSimpanSesi" onclick="konfirmasiSimpan()"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-md transition-colors text-base flex justify-center items-center gap-2">
                <span>Simpan &amp; Tutup Sesi</span>
            </button>
        </div>

    </form>
</div>

<script>
    // --- Logika Form Ceklis ---
    function updateCard(siswaId, status) {
        const container = document.getElementById('ket_container_' + siswaId);
        const input = document.getElementById('ket_' + siswaId);
        if (!container || !input) return;

        if (status === 'izin') {
            container.classList.remove('hidden');
            input.required = true;
            input.focus();
        } else {
            container.classList.add('hidden');
            input.required = false;
            input.value = '';
        }
    }

    function setAllHadir() {
        document.querySelectorAll('input[type="radio"][value="hadir"]').forEach((r) => {
            r.checked = true;
            const m = r.name.match(/\[(\d+)\]/);
            if (m) updateCard(m[1], 'hadir');
        });
    }

    // --- Logika Simpan via AJAX (Mencegah 419 & Double Submit) ---
    function konfirmasiSimpan() {
        Swal.fire({
            title: 'Simpan dan tutup sesi?',
            text: 'Siswa yang belum memiliki status akan otomatis ditandai Alpha.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                prosesSimpan();
            }
        });
    }

    async function prosesSimpan() {
        const form = document.getElementById('formPresensi');
        const btn = document.getElementById('btnSimpanSesi');
        const originalBtnHtml = btn.innerHTML;

        // Ubah tombol jadi loading
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...`;

        try {
            // Ambil semua data form (termasuk _token)
            const formData = new FormData(form);

            // Kirim via Fetch
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Jika server merespon redirect (karena controller melakukan redirect setelah simpan)
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            // Jika server merespon 200 OK tapi tidak redirect, reload halaman
            if (response.ok) {
                window.location.reload();
                return;
            }

            // Jika status bukan 2xx (misal 419, 500, dll)
            throw new Error('HTTP ' + response.status);

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menyimpan data. Silakan refresh halaman dan coba lagi.',
                confirmButtonColor: '#2563eb',
                customClass: { popup: 'rounded-2xl' }
            });
            
            // Kembalikan tombol ke semula
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    }
</script>