SET NAMES utf8mb4;

-- izin_guru
CREATE TABLE izin_guru (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    guru_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jenis ENUM('terlambat','tidak_hadir') NOT NULL,
    alasan TEXT NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    approver_id BIGINT UNSIGNED NULL,
    approved_at DATETIME NULL,
    catatan_approver VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_izin_guru_status (school_id, status, tanggal),
    KEY idx_izin_guru_guru (guru_id, tanggal),
    CONSTRAINT fk_izin_guru_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_izin_guru_guru FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- presensi_guru_event
CREATE TABLE presensi_guru_event (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    guru_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    tipe ENUM('datang','pulang') NOT NULL,
    metode ENUM('foto','qr_lobi','manual_admin','izin') NOT NULL,
    waktu DATETIME NOT NULL,
    foto_path VARCHAR(255) NULL,
    izin_id BIGINT UNSIGNED NULL,
    dicatat_oleh BIGINT UNSIGNED NOT NULL,
    catatan VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pge (school_id, guru_id, tanggal, tipe),
    KEY idx_pge_tgl (school_id, tanggal),
    CONSTRAINT fk_pge_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_pge_guru FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    CONSTRAINT fk_pge_izin FOREIGN KEY (izin_id) REFERENCES izin_guru(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sesi_presensi
CREATE TABLE sesi_presensi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    tahun_ajaran_id BIGINT UNSIGNED NOT NULL,
    jadwal_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    guru_pemilik_jadwal_id BIGINT UNSIGNED NOT NULL,
    dibuka_oleh BIGINT UNSIGNED NOT NULL,
    guru_pengganti_id BIGINT UNSIGNED NULL,
    is_pengganti TINYINT(1) NOT NULL DEFAULT 0,
    mode ENUM('manual','scan') NOT NULL DEFAULT 'manual',
    metode_buka ENUM('normal','remote_approved','pengganti') NOT NULL DEFAULT 'normal',
    qr_secret VARCHAR(64) NULL,
    dibuka_at DATETIME NOT NULL,
    ditutup_at DATETIME NULL,
    closed_by ENUM('guru','system_auto_next_session','system_timeout','admin') NULL,
    UNIQUE KEY uq_sesi (school_id, jadwal_id, tanggal),
    KEY idx_sesi_tgl (school_id, tanggal),
    KEY idx_sesi_aktif (school_id, dibuka_oleh, ditutup_at),
    CONSTRAINT fk_sesi_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_sesi_ta FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE,
    CONSTRAINT fk_sesi_jadwal FOREIGN KEY (jadwal_id) REFERENCES jadwal(id) ON DELETE CASCADE,
    CONSTRAINT fk_sesi_guru_pemilik FOREIGN KEY (guru_pemilik_jadwal_id) REFERENCES guru(id) ON DELETE RESTRICT,
    CONSTRAINT fk_sesi_guru_pengganti FOREIGN KEY (guru_pengganti_id) REFERENCES guru(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- presensi_siswa (metode + manual_admin)
CREATE TABLE presensi_siswa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    sesi_id BIGINT UNSIGNED NOT NULL,
    siswa_id BIGINT UNSIGNED NOT NULL,
    status ENUM('hadir','izin','sakit','alpha') NOT NULL,
    metode ENUM('manual','scan','manual_override','manual_izin','manual_admin') NOT NULL,
    keterangan VARCHAR(255) NULL,
    waktu DATETIME NOT NULL,
    dicatat_oleh BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY uq_ps (sesi_id, siswa_id),
    KEY idx_ps_siswa (school_id, siswa_id),
    KEY idx_ps_status (school_id, status),
    CONSTRAINT fk_ps_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_ps_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_presensi(id) ON DELETE CASCADE,
    CONSTRAINT fk_ps_siswa FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- izin_siswa
CREATE TABLE izin_siswa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    siswa_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jenis ENUM('izin','sakit') NOT NULL,
    alasan TEXT NOT NULL,
    surat_path VARCHAR(255) NULL,
    dicatat_oleh BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_izin_siswa (school_id, siswa_id, tanggal),
    CONSTRAINT fk_izin_siswa_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_izin_siswa_siswa FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;