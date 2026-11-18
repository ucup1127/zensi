<?php
declare(strict_types=1);

if (!function_exists('current_school_id')) {
    function current_school_id(): int
    {
        return (int) (\App\Core\Session::get('school_id')
            ?? ($_ENV['DEFAULT_SCHOOL_ID'] ?? 1));
    }
}

if (!function_exists('current_school')) {
    function current_school(): ?array
    {
        static $cache = null;
        if ($cache !== null) return $cache;
        $cache = \App\Core\DB::table('schools')
            ->where('id', current_school_id())
            ->first();
        return $cache;
    }
    if (!function_exists('get_active_ta')) {
        function get_active_ta(): ?array
        {
            // 1. Cek dari session (hasil dropdown sidebar)
            $sessionTaId = \App\Core\Session::get('tahun_ajaran_id');
            
            if ($sessionTaId) {
                return \App\Core\DB::table('tahun_ajaran')
                    ->where('school_id', current_school_id())
                    ->where('id', $sessionTaId)
                    ->first();
            }

            // 2. Kalau di session belum ada, fallback ke DB yang aktif = 1
            return \App\Core\DB::table('tahun_ajaran')
                ->where('school_id', current_school_id())
                ->where('aktif', 1)
                ->first();
        }
    }
}