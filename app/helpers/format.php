<?php
declare(strict_types=1);

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('tanggal_id')) {
    function tanggal_id(?string $date, bool $withDay = false): string
    {
        if (!$date) return '-';
        $ts = strtotime($date);
        if ($ts === false) return '-';
        $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $d = $hari[(int) date('w', $ts)] ?? '';
        $out = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
        return $withDay ? "{$d}, {$out}" : $out;
    }
}

if (!function_exists('jam_id')) {
    function jam_id(?string $datetime): string
    {
        if (!$datetime) return '-';
        $ts = strtotime($datetime);
        return $ts ? date('H:i', $ts) : '-';
    }
}

if (!function_exists('nama_hari')) {
    function nama_hari(string $hari): string
    {
        return ucfirst($hari);
    }
}

if (!function_exists('status_label')) {
    function status_label(string $status): string
    {
        return match ($status) {
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Tidak Hadir',
            default => ucfirst($status),
        };
    }
}