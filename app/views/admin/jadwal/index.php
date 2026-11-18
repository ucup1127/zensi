<div id="jadwalApp">
    <!-- Header & Filter -->
    <div class="mb-6">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-slate-800">Jadwal Pelajaran</h3>
            <p class="text-sm text-slate-500 mt-1">Tahun Ajaran: <strong><?= e($taAktif['nama']) ?></strong></p>
        </div>
        
        <!-- Baris Action (Search, Filter, Tambah, PDF) -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 w-full">
            
            <!-- Kiri: Search & Filter & Tambah -->
            <div class="flex flex-row items-center gap-2 w-full lg:w-auto flex-1">
                <!-- Search Bar -->
                <div class="relative flex-1 lg:max-w-xs">
                    <input type="text" id="searchInput" onkeyup="debounceSearch()" placeholder="Cari mapel / guru..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
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

                <!-- Tombol Tambah -->
                <button onclick="openAddModal()" class="w-10 h-10 shrink-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center shadow-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>

            <!-- Kanan: Tombol Export PDF -->
            <div class="flex items-center gap-2 w-full lg:w-auto shrink-0 mt-2 lg:mt-0">
                <button onclick="exportPdf()" class="w-full lg:w-auto bg-white border border-red-200 text-red-600 hover:bg-red-50 font-semibold py-2 px-4 rounded-lg text-sm transition flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel (Ditambahkan overflow-x-auto untuk mobile scroll) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                    <th class="py-3 px-4 font-medium">Jam</th>
                    <th class="py-3 px-4 font-medium">Hari</th>
                    <th class="py-3 px-4 font-medium">Kelas</th>
                    <th class="py-3 px-4 font-medium">Mapel</th>
                    <th class="py-3 px-4 font-medium">Guru</th>
                    <th class="py-3 px-4 font-medium w-20">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-slate-100 text-sm whitespace-nowrap">
                <!-- Data dimuat via JS -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 text-sm text-slate-500 gap-4">
        <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select id="perPage" onchange="loadData(1)" class="px-2 py-1 border rounded-md bg-white">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>data</span>
            <span id="infoText" class="ml-4 hidden md:inline"></span>
        </div>
        <div id="paginationContainer"></div>
    </div>
</div>

<!-- MODAL TAMBAH JADWAL (Sama seperti sebelumnya) -->
<div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center opacity-0 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 transform scale-95 transition-transform duration-200" id="modalContent">
        <div class="flex justify-between items-center p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Tambah Jadwal Pelajaran</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        
        <form method="post" action="<?= url('admin/jadwal') ?>" class="p-6 space-y-4">
            <?= \App\Core\CSRF::field() ?>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hari</label>
                    <select name="hari" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Pelajaran</label>
                    <select name="slot_jam_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">-- Pilih Jam --</option>
                        <?php foreach ($slotList as $s): ?>
                            <option value="<?= $s['id'] ?>">Jam <?= $s['jam_ke'] ?> (<?= substr($s['jam_mulai'],0,5) ?> - <?= substr($s['jam_selesai'],0,5) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Guru Pengampu</label>
                <select name="guru_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach ($guruList as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= e($g['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran</label>
                    <select name="mapel_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">-- Pilih Mapel --</option>
                        <?php foreach ($mapelList as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= e($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                    <select name="kelas_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelasList as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= e($k['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition text-sm">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script>
    let searchTimeout;
    function debounceSearch() { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => { loadData(1); }, 500); }
    
    function loadData(page = 1) {
        const q = document.getElementById('searchInput').value; 
        const perPage = document.getElementById('perPage').value; 
        const filterKelas = document.getElementById('filterKelas').value;
        
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" class="py-8 text-center text-slate-500">Memuat...</td></tr>';
        
        fetch(`?q=${q}&filter_kelas=${filterKelas}&per_page=${perPage}&page=${page}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json()).then(data => {
            document.getElementById('tableBody').innerHTML = data.html; 
            document.getElementById('paginationContainer').innerHTML = data.pagination_html; 
            document.getElementById('infoText').innerText = data.info;
        });
    }
    
    document.addEventListener('DOMContentLoaded', () => loadData(1));

    const modalOverlay = document.getElementById('modalOverlay'), modalContent = document.getElementById('modalContent');
    function openAddModal() {
        modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); 
        setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
    }
    function closeModal() {
        modalOverlay.classList.add('opacity-0'); modalContent.classList.add('scale-95');
        setTimeout(() => { modalOverlay.classList.add('hidden'); modalOverlay.classList.remove('flex'); }, 200);
    }

    // Fungsi Export PDF
    function exportPdf() {
        const filterKelas = document.getElementById('filterKelas').value;
        // Arahkan ke route PDF dengan parameter kelas_id (jika ada)
        window.open(`<?= url('admin/jadwal/pdf') ?>?kelas_id=${filterKelas}`, '_blank');
    }
</script>