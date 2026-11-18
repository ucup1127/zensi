<div id="mapelApp">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Mata Pelajaran</h3>
        <div class="flex flex-row items-center gap-2 w-full">
            <div class="relative flex-1">
                <input type="text" id="searchInput" onkeyup="debounceSearch()" placeholder="Cari mapel..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button onclick="openAddModal()" class="w-10 h-10 shrink-0 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center justify-center shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm"><th class="py-3 px-6 font-medium">Kode</th><th class="py-3 px-6 font-medium">Nama Mapel</th><th class="py-3 px-6 font-medium w-32">Aksi</th></tr></thead>
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
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Mapel</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="modalForm" method="post" action="" class="p-6 space-y-5">
                <?= \App\Core\CSRF::field() ?>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Kode Mapel</label><input type="text" name="kode" id="form_kode" required class="w-full px-4 py-2 border rounded-lg outline-none uppercase"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Nama Mapel</label><input type="text" name="nama" id="form_nama" required class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
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
        const q = document.getElementById('searchInput').value;
        const perPage = document.getElementById('perPage').value;
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="3" class="py-8 text-center text-slate-500">Memuat...</td></tr>';
        fetch(`?q=${q}&per_page=${perPage}&page=${page}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json()).then(data => {
            document.getElementById('tableBody').innerHTML = data.html;
            document.getElementById('paginationContainer').innerHTML = data.pagination_html;
            document.getElementById('infoText').innerText = data.info;
        });
    }
    const modalOverlay = document.getElementById('modalOverlay'), modalContent = document.getElementById('modalContent');
    function openAddModal() { document.getElementById('modalTitle').innerText = 'Tambah Mapel'; document.getElementById('modalForm').action = '<?= url("admin/mapel") ?>'; document.getElementById('modalForm').reset(); modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10); }
    function openEditModal(id) {
        fetch(`<?= url('admin/mapel') ?>/${id}/detail`).then(res => res.json()).then(data => {
            document.getElementById('modalTitle').innerText = 'Edit Mapel'; document.getElementById('modalForm').action = `<?= url('admin/mapel') ?>/${id}`;
            document.getElementById('form_kode').value = data.kode; document.getElementById('form_nama').value = data.nama;
            modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
        });
    }
    function closeModal() { modalOverlay.classList.add('opacity-0'); modalContent.classList.add('scale-95'); setTimeout(() => { modalOverlay.classList.add('hidden'); modalOverlay.classList.remove('flex'); }, 200); }
    document.addEventListener('DOMContentLoaded', () => loadData(1));
</script>