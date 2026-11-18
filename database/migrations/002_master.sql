SET NAMES utf8mb4;

-- kelas
CREATE TABLE kelas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    nama VARCHAR(30) NOT NULL,
    tingkat TINYINT UNSIGNED NOT NULL,
    wali_kelas_id BIGINT UNSIGNED NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_kelas (school_id, nama),
    KEY idx_kelas_school (school_id, aktif),
    CONSTRAINT fk_kelas_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- mapel
CREATE TABLE mapel (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    nama VARCHAR(100) NOT NULL,
    kode VARCHAR(20) NOT NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    external_id VARCHAR(100) NULL,
    UNIQUE KEY uq_mapel (school_id, kode),
    CONSTRAINT fk_mapel_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- guru
CREATE TABLE guru (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    school_id BIGINT UNSIGNED NOT NULL,
    nip VARCHAR(50) NULL,
    external_id VARCHAR(100) NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_guru_user (user_id),
    KEY idx_guru_school (school_id, aktif),
    CONSTRAINT fk_guru_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_guru_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE kelas
    ADD CONSTRAINT fk_kelas_wali FOREIGN KEY (wali_kelas_id) REFERENCES guru(id) ON DELETE SET NULL;

-- siswa
CREATE TABLE siswa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    school_id BIGINT UNSIGNED NOT NULL,
    nis VARCHAR(30) NOT NULL,
    kelas_id BIGINT UNSIGNED NOT NULL,
    external_id VARCHAR(100) NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_siswa_user (user_id),
    UNIQUE KEY uq_siswa_nis (school_id, nis),
    KEY idx_siswa_kelas (kelas_id, aktif),
    CONSTRAINT fk_siswa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_siswa_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_siswa_kelas FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- slot_jam (BARU - untuk jadwal rapi)
CREATE TABLE slot_jam (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    jam_ke TINYINT UNSIGNED NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    UNIQUE KEY uq_slot (school_id, jam_ke),
    CONSTRAINT fk_slot_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- pengampu
CREATE TABLE pengampu (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    tahun_ajaran_id BIGINT UNSIGNED NOT NULL,
    guru_id BIGINT UNSIGNED NOT NULL,
    mapel_id BIGINT UNSIGNED NOT NULL,
    kelas_id BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY uq_pengampu (school_id, tahun_ajaran_id, mapel_id, kelas_id),
    KEY idx_pengampu_guru (guru_id, tahun_ajaran_id),
    CONSTRAINT fk_pengampu_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_pengampu_ta FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE,
    CONSTRAINT fk_pengampu_guru FOREIGN KEY (guru_id) REFERENCES guru(id) ON DELETE CASCADE,
    CONSTRAINT fk_pengampu_mapel FOREIGN KEY (mapel_id) REFERENCES mapel(id) ON DELETE CASCADE,
    CONSTRAINT fk_pengampu_kelas FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- jadwal (dengan slot_jam_id)
CREATE TABLE jadwal (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    tahun_ajaran_id BIGINT UNSIGNED NOT NULL,
    pengampu_id BIGINT UNSIGNED NOT NULL,
    hari ENUM('senin','selasa','rabu','kamis','jumat','sabtu') NOT NULL,
    slot_jam_id BIGINT UNSIGNED NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    KEY idx_jadwal_hari (school_id, tahun_ajaran_id, hari),
    KEY idx_jadwal_slot (slot_jam_id),
    CONSTRAINT fk_jadwal_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_ta FOREIGN KEY (tahun_ajaran_id) REFERENCES tahun_ajaran(id) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_pengampu FOREIGN KEY (pengampu_id) REFERENCES pengampu(id) ON DELETE CASCADE,
    CONSTRAINT fk_jadwal_slot FOREIGN KEY (slot_jam_id) REFERENCES slot_jam(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;