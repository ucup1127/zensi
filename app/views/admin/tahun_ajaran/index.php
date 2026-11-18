<div id="taApp">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800">Tahun Ajaran</h3>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-56">
                <input type="text" id="searchInput" onkeyup="debounceSearch()" placeholder="Cari tahun ajaran..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm whitespace-nowrap">+ Tambah</button>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm"><th class="py-3 px-6 font-medium">Nama</th><th class="py-3 px-6 font-medium">Mulai</th><th class="py-3 px-6 font-medium">Selesai</th><th class="py-3 px-6 font-medium">Status</th><th class="py-3 px-6 font-medium w-32">Aksi</th></tr></thead>
            <tbody id="tableBody" class="divide-y divide-slate-100 text-sm"></tbody>
        </table>
    </div>
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 text-sm text-slate-500 gap-4">
        <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select id="perPage" onchange="loadData(1)" class="px-2 py-1 border rounded-md bg-white"><option value="5">5</option><option value="10" selected>10</option><option value="15">15</option><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>
            <span>data</span><span id="infoText" class="ml-4 hidden md:inline"></span>
        </div>
        <div id="paginationContainer"></div>
    </div>

    <!-- Modal -->
    <div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center opacity-0 transition-opacity duration-200">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 transform scale-95 transition-transform duration-200" id="modalContent">
            <div class="flex justify-between items-center p-6 border-b border-slate-200">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Tahun Ajaran</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="modalForm" method="post" action="" class="p-6 space-y-5">
                <?= \App\Core\CSRF::field() ?>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Tahun Ajaran</label><input type="text" name="nama" id="form_nama" required placeholder="2025/2026" class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label><input type="date" name="tanggal_mulai" id="form_mulai" required class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label><input type="date" name="tanggal_selesai" id="form_selesai" required class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                <div class="flex items-start gap-3 p-4 bg-slate-50 border rounded-lg">
                    <input type="checkbox" name="aktif" id="form_aktif" value="1" class="w-5 h-5 text-blue-600 rounded mt-0.5">
                    <label for="form_aktif" class="text-sm font-semibold text-slate-800 cursor-pointer">Jadikan Tahun Ajaran Aktif</label>
                </div>
                <div class="pt-4 border-t flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let searchTimeout;
    function debounceSearch() { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => { loadData(1); }, 500); }
    function loadData(page = 1) {
        const q = document.getElementById('searchInput').value; const perPage = document.getElementById('perPage').value;
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" class="py-8 text-center text-slate-500">Memuat...</td></tr>';
        fetch(`?q=${q}&per_page=${perPage}&page=${page}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json()).then(data => {
            document.getElementById('tableBody').innerHTML = data.html; document.getElementById('paginationContainer').innerHTML = data.pagination_html; document.getElementById('infoText').innerText = data.info;
        });
    }
    const modalOverlay = document.getElementById('modalOverlay'), modalContent = document.getElementById('modalContent');
    function openAddModal() { 
        document.getElementById('modalTitle').innerText = 'Tambah Tahun Ajaran'; document.getElementById('modalForm').action = '<?= url("admin/tahun-ajaran") ?>'; document.getElementById('modalForm').reset(); 
        modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10); 
    }
    function openEditModal(id) {
        fetch(`<?= url('admin/tahun-ajaran') ?>/${id}/detail`).then(res => res.json()).then(data => {
            document.getElementById('modalTitle').innerText = 'Edit Tahun Ajaran'; document.getElementById('modalForm').action = `<?= url('admin/tahun-ajaran') ?>/${id}`;
            document.getElementById('form_nama').value = data.nama; document.getElementById('form_mulai').value = data.tanggal_mulai; document.getElementById('form_selesai').value = data.tanggal_selesai; document.getElementById('form_aktif').checked = data.aktif == 1;
            modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
        });
    }
    function closeModal() { modalOverlay.classList.add('opacity-0'); modalContent.classList.add('scale-95'); setTimeout(() => { modalOverlay.classList.add('hidden'); modalOverlay.classList.remove('flex'); }, 200); }
    document.addEventListener('DOMContentLoaded', () => loadData(1));
</script>