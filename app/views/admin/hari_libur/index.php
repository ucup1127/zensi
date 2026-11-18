<style>
    .fc { font-family: 'Inter', sans-serif; }
    
    /* === PAKSA TOOLBAR SEJAJAR (GRID) === */
    .fc .fc-toolbar { 
        display: grid !important; 
        grid-template-columns: 1fr auto 1fr; /* Kiri, Tengah, Kanan */
        align-items: center !important;
        margin-bottom: 1rem !important;
        gap: 8px !important;
    }
    
    /* Paksa chunk berada di kolom yang tepat */
    .fc .fc-toolbar-chunk:nth-child(1) { justify-self: start !important; display: flex !important; justify-content: flex-start !important; }
    .fc .fc-toolbar-chunk:nth-child(2) { justify-self: center !important; display: flex !important; justify-content: center !important; }
    .fc .fc-toolbar-chunk:nth-child(3) { justify-self: end !important; display: flex !important; justify-content: flex-end !important; }

    /* Tombol */
    .fc .fc-button-primary { 
        background-color: #2563eb !important; border-color: #2563eb !important; 
        font-weight: 500 !important; text-transform: capitalize !important; 
        padding: 0.35rem 0.75rem !important; font-size: 0.8rem !important;
        border-radius: 0.375rem !important; box-shadow: none !important;
    }
    .fc .fc-button-primary:hover { background-color: #1d4ed8 !important; border-color: #1d4ed8 !important; }
    .fc .fc-button-primary:disabled { background-color: #93c5fd !important; border-color: #93c5fd !important; }
    .fc .fc-button-group > .fc-button { margin-left: -1px !important; }
    
    /* Judul Bulan */
    .fc .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 700 !important; color: #1e293b; margin: 0 !important; white-space: nowrap !important; }
    
    /* Grid dan Border */
    .fc-theme-standard td, .fc-theme-standard th { border-color: #e2e8f0; }
    .fc-theme-standard .fc-scrollgrid { border-radius: 0.75rem; overflow: hidden; border: 1px solid #e2e8f0; }
    .fc-col-header-cell { background-color: #f8fafc; padding: 0.5rem 0 !important; }
    .fc-col-header-cell-cushion { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; }
    .fc-daygrid-day-number { color: #334155; font-weight: 500; font-size: 0.8rem; padding: 0.4rem !important; }
    .fc-day-today { background-color: #eff6ff !important; }
    .fc-day-today .fc-daygrid-day-number { color: #2563eb; font-weight: 700; }
    .fc-event { cursor: pointer; border-radius: 0.25rem; padding: 2px 4px; font-size: 0.65rem; font-weight: 600; border: none; }
    
    /* === KHUSUS HP (Responsive) === */
    @media (max-width: 768px) {
        .fc .fc-toolbar { grid-template-columns: auto 1fr auto; gap: 4px !important; } /* Kolom tengah lebih fleksibel */
        .fc .fc-button { padding: 0.25rem 0.5rem !important; font-size: 0.7rem !important; }
        .fc .fc-toolbar-title { font-size: 0.9rem !important; }
        .fc-col-header-cell-cushion { font-size: 0.6rem !important; }
        .fc-daygrid-day-number { font-size: 0.7rem !important; padding: 0.25rem !important; }
    }
</style>

<div id="liburApp">
    <!-- Header Filter & Tombol Tambah -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h3 class="text-xl font-bold text-slate-800 hidden md:block">Hari Libur</h3>
        
        <div class="flex flex-row items-center justify-between w-full md:w-auto gap-2">
            <!-- Input Pencarian -->
            <div class="relative w-full md:w-56 flex-1">
                <input type="text" id="searchInput" onkeyup="debounceSearch()" placeholder="Cari keterangan..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <!-- Tombol Tambah -->
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm whitespace-nowrap transition shrink-0">+ Tambah</button>
        </div>
    </div>

    <!-- KALENDER -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 md:p-6 mb-6">
        <div id="calendar"></div>
    </div>

    <!-- TABEL -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead><tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm"><th class="py-3 px-6 font-medium">Tanggal</th><th class="py-3 px-6 font-medium">Keterangan</th><th class="py-3 px-6 font-medium w-24">Aksi</th></tr></thead>
            <tbody id="tableBody" class="divide-y divide-slate-100 text-sm"></tbody>
        </table>
    </div>
    
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 text-sm text-slate-500 gap-4">
        <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select id="perPage" onchange="loadData(1)" class="px-2 py-1 border rounded-md bg-white focus:ring-2 focus:ring-blue-500 outline-none"><option value="5">5</option><option value="10" selected>10</option><option value="15">15</option><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>
            <span>data</span><span id="infoText" class="ml-4 hidden md:inline"></span>
        </div>
        <div id="paginationContainer"></div>
    </div>

    <!-- Modal -->
    <div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center opacity-0 transition-opacity duration-200">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 transform scale-95 transition-transform duration-200" id="modalContent">
            <div class="flex justify-between items-center p-6 border-b border-slate-200">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Hari Libur</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="modalForm" method="post" action="<?= url('admin/hari-libur') ?>" class="p-6 space-y-5">
                <?= \App\Core\CSRF::field() ?>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Libur</label><input type="date" name="tanggal" id="form_tanggal" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label><input type="text" name="keterangan" id="form_keterangan" required placeholder="Contoh: Libur Idul Fitri" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"></div>
                <div class="pt-4 border-t flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    let searchTimeout;
    function debounceSearch() { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => { loadData(1); }, 500); }
    
    function loadData(page = 1) {
        const q = document.getElementById('searchInput').value; const perPage = document.getElementById('perPage').value;
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="3" class="py-8 text-center text-slate-500">Memuat...</td></tr>';
        fetch(`?q=${q}&per_page=${perPage}&page=${page}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json()).then(data => {
            document.getElementById('tableBody').innerHTML = data.html; document.getElementById('paginationContainer').innerHTML = data.pagination_html; document.getElementById('infoText').innerText = data.info;
        });
    }

    const modalOverlay = document.getElementById('modalOverlay'), modalContent = document.getElementById('modalContent');
    function openAddModal() { 
        document.getElementById('modalTitle').innerText = 'Tambah Hari Libur'; document.getElementById('modalForm').reset(); 
        modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10); 
    }
    function closeModal() { modalOverlay.classList.add('opacity-0'); modalContent.classList.add('scale-95'); setTimeout(() => { modalOverlay.classList.add('hidden'); modalOverlay.classList.remove('flex'); }, 200); }
    
    document.addEventListener('DOMContentLoaded', () => {
        loadData(1);

        // Inisialisasi Kalender dengan Susunan Toolbar Baru
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listYear'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                list: 'Daftar'
            },
            events: '<?= url('admin/hari-libur/events') ?>',
            height: 'auto',
            eventClick: function(info) {
                alert('Hari Libur: ' + info.event.title);
            }
        });
        calendar.render();
    });
</script>