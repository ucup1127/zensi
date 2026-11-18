<div id="slotApp">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Slot Jam Pelajaran</h3>
            <p class="text-sm text-slate-500 mt-1">Atur jam berapa saja pelajaran dimulai dan berakhir.</p>
        </div>
        <div class="w-full md:w-auto">
            <button onclick="openAddModal()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm whitespace-nowrap transition">+ Tambah Jam</button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm">
                    <th class="py-3 px-6 font-medium w-32 whitespace-nowrap">Jam Ke</th>
                    <th class="py-3 px-6 font-medium">Waktu</th>
                    <th class="py-3 px-6 font-medium w-40 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php if (empty($slots)): ?>
                    <tr><td colspan="3" class="py-8 text-center text-slate-500">Belum ada slot jam.</td></tr>
                <?php else: ?>
                    <?php foreach ($slots as $row): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-6 font-semibold text-slate-800 whitespace-nowrap">Jam <?= $row['jam_ke'] ?></td>
                            <td class="py-3 px-6 text-slate-600 whitespace-nowrap">
                                <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?>
                            </td>
                            <td class="py-3 px-6 flex justify-center gap-3">
                                <button onclick='openEditModal(<?= json_encode($row) ?>)' class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                                <form method="post" action="<?= url("admin/slot-jam/{$row['id']}/delete") ?>" class="m-0" onsubmit="return confirm('Hapus slot jam ini?');">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center opacity-0 transition-opacity duration-200">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-200" id="modalContent">
            <div class="flex justify-between items-center p-6 border-b border-slate-200">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800">Tambah Slot Jam</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form id="modalForm" method="post" action="<?= url('admin/slot-jam') ?>" class="p-6 space-y-5">
                <?= \App\Core\CSRF::field() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Ke-</label>
                    <input type="number" name="jam_ke" id="form_jam_ke" required min="1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="form_jam_mulai" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="form_jam_selesai" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="pt-4 border-t flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modalOverlay = document.getElementById('modalOverlay'), modalContent = document.getElementById('modalContent');
    const form = document.getElementById('modalForm');

    function openAddModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Slot Jam';
        form.action = '<?= url("admin/slot-jam") ?>';
        form.reset();
        modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex'); 
        setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
    }

    // Terima parameter objek (row)
    function openEditModal(row) {
        document.getElementById('modalTitle').innerText = 'Edit Slot Jam';
        form.action = '<?= url("admin/slot-jam") ?>/' + row.id; // Pakai concat string biasa (+)
        document.getElementById('form_jam_ke').value = row.jam_ke;
        document.getElementById('form_jam_mulai').value = row.jam_mulai;
        document.getElementById('form_jam_selesai').value = row.jam_selesai;
        modalOverlay.classList.remove('hidden'); modalOverlay.classList.add('flex');
        setTimeout(() => { modalOverlay.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
    }

    function closeModal() {
        modalOverlay.classList.add('opacity-0'); modalContent.classList.add('scale-95');
        setTimeout(() => { modalOverlay.classList.add('hidden'); modalOverlay.classList.remove('flex'); }, 200);
    }
</script>