-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Nov 2026 pada 13.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_presensi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `aksi` varchar(100) NOT NULL,
  `entitas` varchar(100) NOT NULL,
  `entitas_id` bigint(20) UNSIGNED NOT NULL,
  `data_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_lama`)),
  `data_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_baru`)),
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `delegasi_mengajar`
--

CREATE TABLE `delegasi_mengajar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `guru_asli_id` bigint(20) UNSIGNED NOT NULL,
  `guru_pengganti_id` bigint(20) UNSIGNED NOT NULL,
  `diset_oleh` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id`, `user_id`, `school_id`, `nip`, `external_id`, `aktif`) VALUES
(1, 2, 1, '198501012010011001', NULL, 1),
(2, 3, 1, '199002022015012002', NULL, 1),
(3, 6, 1, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `hari_libur`
--

CREATE TABLE `hari_libur` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `izin_guru`
--

CREATE TABLE `izin_guru` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('terlambat','tidak_hadir') NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `catatan_approver` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `izin_siswa`
--

CREATE TABLE `izin_siswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('izin','sakit') NOT NULL,
  `alasan` text NOT NULL,
  `surat_path` varchar(255) DEFAULT NULL,
  `dicatat_oleh` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `pengampu_id` bigint(20) UNSIGNED NOT NULL,
  `hari` enum('senin','selasa','rabu','kamis','jumat','sabtu') NOT NULL,
  `slot_jam_id` bigint(20) UNSIGNED NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id`, `school_id`, `tahun_ajaran_id`, `pengampu_id`, `hari`, `slot_jam_id`, `jam_mulai`, `jam_selesai`) VALUES
(1, 1, 1, 1, 'selasa', 1, '07:00:00', '07:40:00'),
(2, 1, 1, 1, 'senin', 1, '07:00:00', '07:40:00'),
(3, 1, 1, 2, 'selasa', 4, '09:00:00', '09:40:00'),
(4, 1, 1, 3, 'senin', 2, '07:40:00', '08:20:00'),
(5, 1, 1, 4, 'senin', 3, '08:20:00', '09:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(30) NOT NULL,
  `tingkat` tinyint(3) UNSIGNED NOT NULL,
  `wali_kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `school_id`, `nama`, `tingkat`, `wali_kelas_id`, `aktif`) VALUES
(1, 1, '7A', 7, 1, 1),
(2, 1, '7B', 7, 2, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mapel`
--

CREATE TABLE `mapel` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kode` varchar(20) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `external_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mapel`
--

INSERT INTO `mapel` (`id`, `school_id`, `nama`, `kode`, `aktif`, `external_id`) VALUES
(1, 1, 'Matematika', 'MTK', 1, NULL),
(2, 1, 'Bahasa Indonesia', 'BIN', 1, NULL),
(3, 1, 'IPA', 'IPA', 1, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengampu`
--

CREATE TABLE `pengampu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `mapel_id` bigint(20) UNSIGNED NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengampu`
--

INSERT INTO `pengampu` (`id`, `school_id`, `tahun_ajaran_id`, `guru_id`, `mapel_id`, `kelas_id`) VALUES
(1, 1, 1, 3, 2, 1),
(2, 1, 1, 3, 2, 2),
(3, 1, 1, 3, 3, 1),
(4, 1, 1, 3, 1, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi_guru_event`
--

CREATE TABLE `presensi_guru_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `tipe` enum('datang','pulang') NOT NULL,
  `metode` enum('foto','qr_lobi','manual_admin','izin') NOT NULL,
  `waktu` datetime NOT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `izin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dicatat_oleh` bigint(20) UNSIGNED NOT NULL,
  `catatan` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `presensi_guru_event`
--

INSERT INTO `presensi_guru_event` (`id`, `school_id`, `guru_id`, `tanggal`, `tipe`, `metode`, `waktu`, `foto_path`, `izin_id`, `dicatat_oleh`, `catatan`, `created_at`) VALUES
(1, 1, 3, '2026-11-16', 'datang', 'foto', '2026-11-16 18:16:52', 'storage/presensi/2026-11/3_datang_20261116_181652.jpg', NULL, 6, NULL, '2026-11-16 18:16:52'),
(2, 1, 3, '2026-11-16', 'pulang', 'foto', '2026-11-16 18:17:04', 'storage/presensi/2026-11/3_pulang_20261116_181704.jpg', NULL, 6, NULL, '2026-11-16 18:17:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi_siswa`
--

CREATE TABLE `presensi_siswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `sesi_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('hadir','izin','sakit','alpha') NOT NULL,
  `metode` enum('manual','scan','manual_override','manual_izin','manual_admin') NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `waktu` datetime NOT NULL,
  `dicatat_oleh` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `presensi_siswa`
--

INSERT INTO `presensi_siswa` (`id`, `school_id`, `sesi_id`, `siswa_id`, `status`, `metode`, `keterangan`, `waktu`, `dicatat_oleh`) VALUES
(34, 1, 1, 1, 'hadir', 'scan', 'Scan QR', '2026-11-16 21:12:45', 4),
(35, 1, 1, 2, 'alpha', 'manual', NULL, '2026-11-16 21:12:54', 6),
(36, 1, 1, 3, 'alpha', 'manual', NULL, '2026-11-16 21:12:54', 6),
(37, 1, 2, 1, 'hadir', 'scan', 'Scan QR', '2026-11-16 21:13:12', 4),
(38, 1, 2, 2, 'alpha', 'manual', NULL, '2026-11-16 21:13:17', 6),
(39, 1, 2, 3, 'alpha', 'manual', NULL, '2026-11-16 21:13:17', 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `qr_token_usage`
--

CREATE TABLE `qr_token_usage` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sesi_id` bigint(20) UNSIGNED NOT NULL,
  `window` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `used_by_siswa_id` bigint(20) UNSIGNED NOT NULL,
  `used_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `qr_token_usage`
--

INSERT INTO `qr_token_usage` (`id`, `sesi_id`, `window`, `token_hash`, `used_by_siswa_id`, `used_at`) VALUES
(1, 1, 1794838369, 'af6bb17932d8728a73ab588bce05f4f6d98147ad1c6ef93fe84cae8b709a6b7b', 1, '2026-11-16 21:12:45'),
(2, 2, 1794838397, '93295d26f851178f5710d4ec9e43f8809128f777c5a9a8d9f453e81727769482', 1, '2026-11-16 21:13:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `remote_mengajar`
--

CREATE TABLE `remote_mengajar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Jakarta',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `schools`
--

INSERT INTO `schools` (`id`, `nama`, `alamat`, `timezone`, `created_at`) VALUES
(1, 'SMP Negeri 1 Contoh', 'Jl. Pendidikan No. 1', 'Asia/Jakarta', '2026-11-15 15:50:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sesi_presensi`
--

CREATE TABLE `sesi_presensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `guru_pemilik_jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `dibuka_oleh` bigint(20) UNSIGNED NOT NULL,
  `guru_pengganti_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_pengganti` tinyint(1) NOT NULL DEFAULT 0,
  `mode` enum('manual','scan') NOT NULL DEFAULT 'manual',
  `metode_buka` enum('normal','remote_approved','pengganti') NOT NULL DEFAULT 'normal',
  `qr_secret` varchar(64) DEFAULT NULL,
  `dibuka_at` datetime NOT NULL,
  `ditutup_at` datetime DEFAULT NULL,
  `closed_by` enum('guru','system_auto_next_session','system_timeout','admin') DEFAULT NULL,
  `qr_token` varchar(100) DEFAULT NULL,
  `qr_expired_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sesi_presensi`
--

INSERT INTO `sesi_presensi` (`id`, `school_id`, `tahun_ajaran_id`, `jadwal_id`, `tanggal`, `guru_pemilik_jadwal_id`, `dibuka_oleh`, `guru_pengganti_id`, `is_pengganti`, `mode`, `metode_buka`, `qr_secret`, `dibuka_at`, `ditutup_at`, `closed_by`, `qr_token`, `qr_expired_at`) VALUES
(1, 1, 1, 2, '2026-11-16', 3, 6, NULL, 0, 'manual', 'normal', NULL, '2026-11-16 21:12:38', '2026-11-16 21:12:54', 'guru', '46537b908f686b8378cae91c9962b011', '2026-11-16 21:12:57'),
(2, 1, 1, 4, '2026-11-16', 3, 6, NULL, 0, 'manual', 'normal', NULL, '2026-11-16 21:13:06', '2026-11-16 21:13:17', 'guru', '3c288139eb5e7fbb68702e7b308fdd31', '2026-11-16 21:13:25'),
(3, 1, 1, 5, '2026-11-16', 3, 6, NULL, 0, 'manual', 'normal', NULL, '2026-11-16 17:50:53', '2026-11-16 17:50:57', 'guru', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `nis` varchar(30) NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id`, `user_id`, `school_id`, `nis`, `kelas_id`, `external_id`, `aktif`) VALUES
(1, 4, 1, '25001', 1, NULL, 1),
(2, 5, 1, '25002', 1, NULL, 1),
(3, 7, 1, '25003', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `slot_jam`
--

CREATE TABLE `slot_jam` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `jam_ke` tinyint(3) UNSIGNED NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `slot_jam`
--

INSERT INTO `slot_jam` (`id`, `school_id`, `jam_ke`, `jam_mulai`, `jam_selesai`) VALUES
(1, 1, 1, '07:00:00', '07:40:00'),
(2, 1, 2, '07:40:00', '08:20:00'),
(3, 1, 3, '08:20:00', '09:00:00'),
(4, 1, 4, '09:00:00', '09:40:00'),
(5, 1, 5, '10:00:00', '10:40:00'),
(6, 1, 6, '10:40:00', '11:20:00'),
(7, 1, 7, '11:20:00', '12:00:00'),
(8, 1, 8, '12:00:00', '12:40:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(50) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id`, `school_id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `aktif`) VALUES
(1, 1, '2025/2026', '2025-07-01', '2026-06-30', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('admin','guru','siswa') NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `external_id` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `school_id`, `role`, `username`, `password_hash`, `nama_lengkap`, `no_hp`, `aktif`, `must_change_password`, `external_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'admin', '$2y$10$qbt/.f4ULCTUFB7ElrlxK.E8g3H6s0M81pY/2.cXe8AwrVfUDLU8u', 'Administrator', NULL, 1, 0, NULL, '2026-11-15 15:50:04', '2026-11-15 17:33:50'),
(2, 1, 'guru', 'guru1', '$2y$10$HIQwnqzTqG2Ov.2RrxLWIu68J2QUWIQjqWtx.7NSBDvHdXPveO4Hq', 'Budi Santoso, S.Pd.', NULL, 1, 0, NULL, '2026-11-15 15:50:04', '2026-11-16 14:48:48'),
(3, 1, 'guru', 'guru2', '$2y$10$HIQwnqzTqG2Ov.2RrxLWIu68J2QUWIQjqWtx.7NSBDvHdXPveO4Hq', 'Siti Aminah, S.Pd.', NULL, 1, 1, NULL, '2026-11-15 15:50:04', '2026-11-15 17:33:50'),
(4, 1, 'siswa', 'siswa1', '$2y$10$6pU99gi38QWfR/L5X4VZcOESw27V9Bma59Gh2FMNSbElhTT.Hn29y', 'Andi Pratama', NULL, 1, 0, NULL, '2026-11-15 15:50:04', '2026-11-16 20:57:22'),
(5, 1, 'siswa', 'siswa2', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB4Zz7f3YfV8VbHi', 'Bunga Lestari', NULL, 1, 0, NULL, '2026-11-15 15:50:04', '2026-11-16 20:50:30'),
(6, 1, 'guru', 'guru', '$2y$10$Ncqyw5UpoAB79Q6d1g1RbuEhknq1PnObvJMp2AtcFFmWQk7zTH0YC', 'guru', NULL, 1, 0, NULL, '2026-11-16 15:18:23', '2026-11-16 15:20:21'),
(7, 1, 'siswa', 'siswa3', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdB4Zz7f3YfV8VbHi', 'Citra Dewi', NULL, 1, 0, NULL, '2026-11-16 20:51:14', '2026-11-16 20:51:14');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_entitas` (`school_id`,`entitas`,`entitas_id`),
  ADD KEY `idx_audit_created` (`school_id`,`created_at`);

--
-- Indeks untuk tabel `delegasi_mengajar`
--
ALTER TABLE `delegasi_mengajar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_delegasi` (`school_id`,`jadwal_id`,`tanggal`),
  ADD KEY `fk_delegasi_jadwal` (`jadwal_id`),
  ADD KEY `fk_delegasi_asli` (`guru_asli_id`),
  ADD KEY `fk_delegasi_pengganti` (`guru_pengganti_id`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_guru_user` (`user_id`),
  ADD KEY `idx_guru_school` (`school_id`,`aktif`);

--
-- Indeks untuk tabel `hari_libur`
--
ALTER TABLE `hari_libur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_libur` (`school_id`,`tanggal`),
  ADD KEY `idx_libur_tgl` (`school_id`,`tanggal`);

--
-- Indeks untuk tabel `izin_guru`
--
ALTER TABLE `izin_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_izin_guru_status` (`school_id`,`status`,`tanggal`),
  ADD KEY `idx_izin_guru_guru` (`guru_id`,`tanggal`);

--
-- Indeks untuk tabel `izin_siswa`
--
ALTER TABLE `izin_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_izin_siswa` (`school_id`,`siswa_id`,`tanggal`),
  ADD KEY `fk_izin_siswa_siswa` (`siswa_id`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jadwal_hari` (`school_id`,`tahun_ajaran_id`,`hari`),
  ADD KEY `idx_jadwal_slot` (`slot_jam_id`),
  ADD KEY `fk_jadwal_ta` (`tahun_ajaran_id`),
  ADD KEY `fk_jadwal_pengampu` (`pengampu_id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_kelas` (`school_id`,`nama`),
  ADD KEY `idx_kelas_school` (`school_id`,`aktif`),
  ADD KEY `fk_kelas_wali` (`wali_kelas_id`);

--
-- Indeks untuk tabel `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mapel` (`school_id`,`kode`);

--
-- Indeks untuk tabel `pengampu`
--
ALTER TABLE `pengampu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pengampu` (`school_id`,`tahun_ajaran_id`,`mapel_id`,`kelas_id`),
  ADD KEY `idx_pengampu_guru` (`guru_id`,`tahun_ajaran_id`),
  ADD KEY `fk_pengampu_ta` (`tahun_ajaran_id`),
  ADD KEY `fk_pengampu_mapel` (`mapel_id`),
  ADD KEY `fk_pengampu_kelas` (`kelas_id`);

--
-- Indeks untuk tabel `presensi_guru_event`
--
ALTER TABLE `presensi_guru_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pge` (`school_id`,`guru_id`,`tanggal`,`tipe`),
  ADD KEY `idx_pge_tgl` (`school_id`,`tanggal`),
  ADD KEY `fk_pge_guru` (`guru_id`),
  ADD KEY `fk_pge_izin` (`izin_id`);

--
-- Indeks untuk tabel `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ps` (`sesi_id`,`siswa_id`),
  ADD KEY `idx_ps_siswa` (`school_id`,`siswa_id`),
  ADD KEY `idx_ps_status` (`school_id`,`status`),
  ADD KEY `fk_ps_siswa` (`siswa_id`);

--
-- Indeks untuk tabel `qr_token_usage`
--
ALTER TABLE `qr_token_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_qr_sesi` (`sesi_id`,`window`),
  ADD KEY `fk_qr_siswa` (`used_by_siswa_id`);

--
-- Indeks untuk tabel `remote_mengajar`
--
ALTER TABLE `remote_mengajar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_remote` (`school_id`,`jadwal_id`,`tanggal`),
  ADD KEY `idx_remote_status` (`school_id`,`status`),
  ADD KEY `fk_remote_guru` (`guru_id`),
  ADD KEY `fk_remote_jadwal` (`jadwal_id`);

--
-- Indeks untuk tabel `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sesi_presensi`
--
ALTER TABLE `sesi_presensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sesi_tgl` (`school_id`,`tanggal`),
  ADD KEY `idx_sesi_aktif` (`school_id`,`dibuka_oleh`,`ditutup_at`),
  ADD KEY `fk_sesi_ta` (`tahun_ajaran_id`),
  ADD KEY `fk_sesi_jadwal` (`jadwal_id`),
  ADD KEY `fk_sesi_guru_pemilik` (`guru_pemilik_jadwal_id`),
  ADD KEY `fk_sesi_guru_pengganti` (`guru_pengganti_id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_siswa_user` (`user_id`),
  ADD UNIQUE KEY `uq_siswa_nis` (`school_id`,`nis`),
  ADD KEY `idx_siswa_kelas` (`kelas_id`,`aktif`);

--
-- Indeks untuk tabel `slot_jam`
--
ALTER TABLE `slot_jam`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slot` (`school_id`,`jam_ke`);

--
-- Indeks untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ta` (`school_id`,`nama`),
  ADD KEY `idx_ta_school_aktif` (`school_id`,`aktif`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`school_id`,`username`),
  ADD KEY `idx_users_role` (`school_id`,`role`,`aktif`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `delegasi_mengajar`
--
ALTER TABLE `delegasi_mengajar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `hari_libur`
--
ALTER TABLE `hari_libur`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `izin_guru`
--
ALTER TABLE `izin_guru`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `izin_siswa`
--
ALTER TABLE `izin_siswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pengampu`
--
ALTER TABLE `pengampu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `presensi_guru_event`
--
ALTER TABLE `presensi_guru_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT untuk tabel `qr_token_usage`
--
ALTER TABLE `qr_token_usage`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `remote_mengajar`
--
ALTER TABLE `remote_mengajar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `sesi_presensi`
--
ALTER TABLE `sesi_presensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `slot_jam`
--
ALTER TABLE `slot_jam`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_audit_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `delegasi_mengajar`
--
ALTER TABLE `delegasi_mengajar`
  ADD CONSTRAINT `fk_delegasi_asli` FOREIGN KEY (`guru_asli_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_delegasi_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_delegasi_pengganti` FOREIGN KEY (`guru_pengganti_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_delegasi_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `fk_guru_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_guru_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hari_libur`
--
ALTER TABLE `hari_libur`
  ADD CONSTRAINT `fk_libur_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `izin_guru`
--
ALTER TABLE `izin_guru`
  ADD CONSTRAINT `fk_izin_guru_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_izin_guru_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `izin_siswa`
--
ALTER TABLE `izin_siswa`
  ADD CONSTRAINT `fk_izin_siswa_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_izin_siswa_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `fk_jadwal_pengampu` FOREIGN KEY (`pengampu_id`) REFERENCES `pengampu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_jadwal_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_jadwal_slot` FOREIGN KEY (`slot_jam_id`) REFERENCES `slot_jam` (`id`),
  ADD CONSTRAINT `fk_jadwal_ta` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `fk_kelas_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`wali_kelas_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `mapel`
--
ALTER TABLE `mapel`
  ADD CONSTRAINT `fk_mapel_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengampu`
--
ALTER TABLE `pengampu`
  ADD CONSTRAINT `fk_pengampu_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengampu_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengampu_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengampu_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengampu_ta` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `presensi_guru_event`
--
ALTER TABLE `presensi_guru_event`
  ADD CONSTRAINT `fk_pge_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pge_izin` FOREIGN KEY (`izin_id`) REFERENCES `izin_guru` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pge_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  ADD CONSTRAINT `fk_ps_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_sesi` FOREIGN KEY (`sesi_id`) REFERENCES `sesi_presensi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `qr_token_usage`
--
ALTER TABLE `qr_token_usage`
  ADD CONSTRAINT `fk_qr_sesi` FOREIGN KEY (`sesi_id`) REFERENCES `sesi_presensi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qr_siswa` FOREIGN KEY (`used_by_siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `remote_mengajar`
--
ALTER TABLE `remote_mengajar`
  ADD CONSTRAINT `fk_remote_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_remote_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_remote_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sesi_presensi`
--
ALTER TABLE `sesi_presensi`
  ADD CONSTRAINT `fk_sesi_guru_pemilik` FOREIGN KEY (`guru_pemilik_jadwal_id`) REFERENCES `guru` (`id`),
  ADD CONSTRAINT `fk_sesi_guru_pengganti` FOREIGN KEY (`guru_pengganti_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sesi_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sesi_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sesi_ta` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`),
  ADD CONSTRAINT `fk_siswa_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_siswa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `slot_jam`
--
ALTER TABLE `slot_jam`
  ADD CONSTRAINT `fk_slot_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD CONSTRAINT `fk_ta_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
