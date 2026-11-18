<!-- Kontainer Utama -->
<div x-data="{ showModal: false, modalTitle: '', formAction: '', modalData: {} }" id="siswaApp">
    
    <!-- Header & Filter -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Data Siswa</h3>
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-2 w-full">
            
            <!-- Baris Pencarian & Filter Utama -->
            <div class="flex flex-row items-center gap-2 w-full lg:w-auto flex-1">
                <div class="relative flex-1 lg:max-w-xs">
                    <input type="text" id="searchInput" onkeyup="debounceSearch()" placeholder="Cari nama / NIS..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                
                <!-- Filter Status (Aktif/Alumni) -->
                <div class="relative w-36 shrink-0">
                    <select id="filterStatus" onchange="loadData(1)" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="aktif" <?= (($paginator['status'] ?? 'aktif') === 'aktif') ? 'selected' : '' ?>>Siswa Aktif</option>
                        <option value="alumni" <?= (($paginator['status'] ?? '') === 'alumni') ? 'selected' : '' ?>>Alumni / Keluar</option>
                    </select>
                </div>

                <!-- Filter Kelas -->
                <div class="relative w-10 h-10 shrink-0">
                    <select id="filterKelas" onchange="loadData(1)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelasList as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= e($k['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="w-10 h-10 bg-white border border-slate-300 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-50 transition pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Baris Tombol Aksi -->
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto mt-2 lg:mt-0">
                <a href="<?= url('admin/kenaikan-kelas') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Kenaikan Kelas
                </a>
                <a href="<?= url('admin/siswa/import') ?>" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import
                </a>
                <button onclick="openAddModal()" class="w-10 h-10 shrink-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                    <th class="py-3 px-6 font-medium">NIS</th>
                    <th class="py-3 px-6 font-medium">Nama Siswa</th>
                    <th class="py-3 px-6 font-medium">Kelas</th>
                    <th class="py-3 px-6 font-medium w-32">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-slate-100 text-sm">
                <?php 
                // Render awal (server-side) agar tidak blank saat load pertama
                echo (function() use ($paginator) {
                    ob_start();
                    if (empty($paginator['data'])): ?>
                        <tr><td colspan="4" class="py-8 text-center text-slate-500">Belum ada data siswa.</td></tr>
                    <?php else: ?>
                        <?php foreach ($paginator['data'] as $s): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-6 text-slate-600 font-mono text-xs"><?= e($s['nis']) ?></td>
                                <td class="py-3 px-6 font-semibold text-slate-800"><?= e($s['nama_lengkap']) ?></td>
                                <td class="py-3 px-6 text-slate-600"><?= e($s['kelas_nama']) ?></td>
                                <td class="py-3 px-6 flex gap-3">
                                    <?php if (($paginator['status'] ?? 'aktif') === 'aktif'): ?>
                                        <button onclick="openEditModal(<?= $s['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                                        <form method="post" action="<?= url("admin/siswa/{$s['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Nonaktifkan siswa ini?');">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-xs">Arsip</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif;
                    return ob_get_clean();
                })();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Footer: Pagination & Limit -->
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 text-sm text-slate-500 gap-4">
        <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select id="perPage" onchange="loadData(1)" class="px-2 py-1 border rounded-md bg-white focus:outline-none">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>data</span>
            <span id="infoText" class="ml-4 hidden md:inline">Menampilkan <?= $paginator['from'] ?> - <?= $paginator['to'] ?> dari <?= $paginator['total'] ?> data</span>
        </div>
        
        <div id="paginationContainer">
            <!-- Render pagination awal -->
            <?php if ($paginator['last_page'] > 1): ?>
                <div class="flex gap-1">
                    <?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?>
                        <button onclick="loadData(<?= $i ?>)" class="px-3 py-1 border rounded-lg text-sm <?= $i == $paginator['current_page'] ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL (Tambah / Edit) -->
    <div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center opacity-0 transition-opacity duration-200">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 transform scale-95 transition-transform duration-200" id="modalContent">
            <div class="flex justify-between items-center p-6 border-b border-slate-200">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Siswa</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
           <form id="modalForm" method="POST" action="" class="p-6 space-y-5">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" name="_method" value="POST">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap Siswa</label>
                    <input type="text" name="nama_lengkap" id="form_nama" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NIS</label>
                    <input type="text" name="nis" id="form_nis" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                    <select name="kelas_id" id="form_kelas" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelasList as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= e($k['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="tglLahirContainer">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir (Untuk Password Default)</label>
                    <input type="date" name="tanggal_lahir" id="form_tgl" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <p class="text-xs text-slate-500 mt-1">Format Password: DDMMYYYY</p>
                </div>

                    <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="...">Batal</button>
                        <!-- UBAH TYPE MENJADI BUTTON DAN TAMBAHKAN ONCLICK -->
                        <button type="button" id="btnSubmitSiswa" onclick="simpanSiswa()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition text-sm">Simpan Data</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    let searchTimeout;

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadData(1);
        }, 500);
    }

    function loadData(page = 1) {
        const q = document.getElementById('searchInput').value;
        const kelasId = document.getElementById('filterKelas').value;
        const status = document.getElementById('filterStatus').value;
        const perPage = document.getElementById('perPage').value;
        
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-slate-500">Memuat data...</td></tr>';

        fetch(`?q=${q}&kelas_id=${kelasId}&status=${status}&per_page=${perPage}&page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = data.html;
            document.getElementById('paginationContainer').innerHTML = data.pagination_html;
            document.getElementById('infoText').innerText = data.info;
            document.getElementById('infoText').classList.remove('hidden');
        })
        .catch(err => console.error(err));
    }

    // Modal Logic
    const modalOverlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');

    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Siswa';
        document.getElementById('modalForm').action = '<?= url("admin/siswa") ?>';
        document.getElementById('modalForm').reset();
        document.getElementById('tglLahirContainer').style.display = 'block';
        
        modalOverlay.classList.remove('hidden');
        modalOverlay.classList.add('flex');
        setTimeout(() => {
            modalOverlay.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

        function openEditModal(id) {
        fetch(`<?= url('admin/siswa') ?>/${id}/detail`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modalTitle').innerText = 'Edit Siswa';
                
                // PASTIKAN URL ACTION BENAR
                document.getElementById('modalForm').action = `<?= url('admin/siswa') ?>/${id}`;
                
                document.getElementById('form_nama').value = data.nama_lengkap;
                document.getElementById('form_nis').value = data.nis;
                document.getElementById('form_kelas').value = data.kelas_id;
                
                document.getElementById('tglLahirContainer').style.display = 'none';
                
                modalOverlay.classList.remove('hidden');
                modalOverlay.classList.add('flex');
                setTimeout(() => {
                    modalOverlay.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-95');
                }, 10);
            })
            .catch(err => {
                console.error(err);
                alert('Gagal mengambil data siswa.');
            });
    }

    function closeModal() {
        modalOverlay.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modalOverlay.classList.add('hidden');
            modalOverlay.classList.remove('flex');
        }, 200);
    }

        async function simpanSiswa() {
        const form = document.getElementById('modalForm');
        const btn = document.getElementById('btnSubmitSiswa');
        const originalText = btn.innerHTML;

        // Ubah tombol jadi loading
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            // Jika server melakukan redirect (sukses simpan), browser akan mengikuti redirect
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            // Jika server balas 200 tapi tidak redirect (berarti ada error validasi dari controller)
            if (response.ok) {
                window.location.reload();
                return;
            }

            throw new Error('Terjadi kesalahan pada server');
        } catch (err) {
            console.error(err);
            alert('Gagal menyimpan. Cek koneksi atau refresh halaman.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>