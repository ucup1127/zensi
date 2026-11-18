<!-- Header Welcome -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Welcome to Dashboard</h1>
        <p class="text-slate-500 text-sm mt-1">Ringkasan aktivitas sistem presensi hari ini.</p>
    </div>
    
     <!-- Kotak Tanggal & Tahun Ajaran -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-5 py-4 flex flex-col min-w-[300px]">
        
        <!-- Baris Atas: Sejajar (Tahun Ajaran & Tanggal) -->
        <div class="flex items-center justify-between gap-4 mb-2">
            <!-- Tahun Ajaran -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">Tahun Ajaran:</span>
                <span class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                    <?= e($namaTa) ?>
                </span>
            </div>
            
            <!-- Hari & Tanggal (Tanpa Jam) -->
            <div id="realtimeDate" class="text-sm font-semibold text-slate-700">
                <!-- Javascript akan mengisi ini -->
            </div>
        </div>

        <!-- Baris Bawah: Jam Real-time -->
        <div class="text-right border-t border-slate-100 pt-2">
            <div id="realtimeClock" class="text-xs font-medium text-slate-500">
                <!-- Javascript akan mengisi ini -->
            </div>
        </div>
    </div>
</div>

<!-- Kartu Statistik -->
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
    
    <!-- Card 1 -->
    <div class="bg-white rounded-xl md:rounded-2xl p-3 md:p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-[10px] md:text-sm font-medium mb-0.5 md:mb-1">Total Guru</p>
            <h3 class="text-xl md:text-3xl font-bold text-slate-800"><?= number_format($stats['guru']) ?></h3>
        </div>
        <div class="w-8 h-8 md:w-12 md:h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
            <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-xl md:rounded-2xl p-3 md:p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-[10px] md:text-sm font-medium mb-0.5 md:mb-1">Total Siswa</p>
            <h3 class="text-xl md:text-3xl font-bold text-slate-800"><?= number_format($stats['siswa']) ?></h3>
        </div>
        <div class="w-8 h-8 md:w-12 md:h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
            <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-xl md:rounded-2xl p-3 md:p-6 shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 text-[10px] md:text-sm font-medium mb-0.5 md:mb-1">Total Kelas</p>
            <h3 class="text-xl md:text-3xl font-bold text-slate-800"><?= number_format($stats['kelas']) ?></h3>
        </div>
        <div class="w-8 h-8 md:w-12 md:h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
            <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-xl md:rounded-2xl p-3 md:p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-[10px] md:text-sm font-medium mb-0.5 md:mb-1">Kehadiran Hari Ini</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-xl md:text-3xl font-bold text-slate-800"><?= number_format($stats['hadir_hari_ini']) ?></h3>
                    <span class="text-xs text-slate-500 font-medium">Guru</span>
                </div>
            </div>
            <div class="w-8 h-8 md:w-12 md:h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="mt-2 text-xs text-slate-500 border-t border-slate-100 pt-2 flex justify-between">
            <span>Siswa Hadir:</span>
            <span class="font-bold text-slate-800"><?= number_format($chartSiswa['hadir']) ?> Siswa</span>
        </div>
    </div>
</div>

<!-- Chart Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Line Chart: Trend Guru -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 lg:col-span-2">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Trend Kehadiran Guru (7 Hari)</h3>
        <div class="h-64 w-full">
            <canvas id="lineChartGuru"></canvas>
        </div>
    </div>

    <!-- Pie Chart: Siswa -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Status Siswa (Global)</h3>
        <div class="h-64 w-full flex justify-center">
            <canvas id="pieChartSiswa"></canvas>
        </div>
    </div>
</div>

<!-- Script Inisialisasi Chart -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart
    const ctxLine = document.getElementById('lineChartGuru').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartGuru['labels']) ?>,
            datasets: [{
                label: 'Guru Hadir',
                data: <?= json_encode($chartGuru['data']) ?>,
                borderColor: '#3b82f6', // blue-500
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4, // Membuat garis melengkung smooth
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4], color: '#f1f5f9' },
                    ticks: { stepSize: 1 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Pie Chart
    const ctxPie = document.getElementById('pieChartSiswa').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [
                    <?= $chartSiswa['hadir'] ?>, 
                    <?= $chartSiswa['izin'] ?>, 
                    <?= $chartSiswa['sakit'] ?>, 
                    <?= $chartSiswa['alpha'] ?>
                ],
                backgroundColor: [
                    '#22c55e', // green-500
                    '#3b82f6', // blue-500
                    '#f59e0b', // amber-500
                    '#ef4444'  // red-500
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 20
                    }
                }
            }
        }
    });

    // 3. Script Jam Real-time (WIB)
    function updateClock() {
        const now = new Date();
        
        // Format Hari (Indonesia)
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const dayName = days[now.getDay()];
        
        // Format Tanggal, Bulan, Tahun
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        
        // Format Jam:Menit:Detik
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        // 1. Isi bagian Hari & Tanggal
        document.getElementById('realtimeDate').innerHTML = `${dayName}, ${date} ${month} ${year}`;
        
        // 2. Isi bagian Jam
        document.getElementById('realtimeClock').innerHTML = `${hours}:${minutes}:${seconds} WIB`;
    }

    // Jalankan langsung saat halaman dimuat
    updateClock();
    // Update setiap 1 detik
    setInterval(updateClock, 1000);
});
</script>