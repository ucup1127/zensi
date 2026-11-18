<?php
return [
    'env'         => $_ENV['APP_ENV'] ?? 'local',
    'debug'       => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url'         => rtrim($_ENV['APP_URL'] ?? 'http://localhost/presensi/public', '/'),
    'timezone'    => $_ENV['APP_TIMEZONE'] ?? 'Asia/Jakarta',
    'name'        => 'Presensi Sekolah',
    'session'     => [
        'name'     => $_ENV['SESSION_NAME'] ?? 'presensi_session',
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
    ],
    'school'      => [
        'default_id' => (int) ($_ENV['DEFAULT_SCHOOL_ID'] ?? 1),
    ],
];