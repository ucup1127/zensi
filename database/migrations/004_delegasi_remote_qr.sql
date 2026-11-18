SET NAMES utf8mb4;

-- delegasi_mengajar
CREATE TABLE delegasi_mengajar (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    jadwal_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    guru_asli_id BIGINT UNSIGNED NOT NULL,
    guru_pengganti_id BIGINT UNSIGNED NOT NULL,
    diset_oleh BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_delegasi (school_id, jadwal_id, tanggal),
    CONSTRAINT fk_delegasi_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_delegasi_jadwal FOREIGN KEY (jadwal_id) REFERENCES jadwal(id) ON DELETE CASCADE,
    CONSTRAINT fk_delegasi_asli FOREIGN KEY (guru_asli_id) REFERENCES guru(id) ON DELETE CASCADE,
    CONSTRAINT fk_delegasi_pengganti FOREIGN KEY (guru_pengganti_id) REFERENCES guru(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- remote_mengajar
CREATE TABLE remote_mengajar (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    guru_id BIGINT UNSIGNED NOT NULL,
    jadwal_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    alasan TEXT NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    approver_id BIGINT UNSIGNED NULL,
    approved_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_remote (school_id, jadwal_id, tanggal),
    KEY idx_remote_status (school_id, status),
    CONSTRAINT fk_remote_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_remote_guru FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    CONSTRAINT fk_remote_jadwal FOREIGN KEY (jadwal_id) REFERENCES jadwal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- qr_token_usage (fase 2, siap)
CREATE TABLE qr_token_usage (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sesi_id BIGINT UNSIGNED NOT NULL,
    window INT NOT NULL,
    token_hash VARCHAR(64) NOT NULL,
    used_by_siswa_id BIGINT UNSIGNED NOT NULL,
    used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_qr_sesi (sesi_id, window),
    CONSTRAINT fk_qr_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_presensi(id) ON DELETE CASCADE,
    CONSTRAINT fk_qr_siswa FOREIGN KEY (used_by_siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;