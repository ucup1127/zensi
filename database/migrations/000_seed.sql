SET NAMES utf8mb4;

-- School
INSERT INTO schools (id, nama, alamat, timezone) VALUES
(1, 'SMP Negeri 1 Contoh', 'Jl. Pendidikan No. 1', 'Asia/Jakarta');

-- Tahun ajaran
INSERT INTO tahun_ajaran (id, school_id, nama, tanggal_mulai, tanggal_selesai, aktif) VALUES
(1, 1, '2025/2026', '2025-07-01', '2026-06-30', 1);

-- Admin (password: admin123)
INSERT INTO users (id, school_id, role, username, password_hash, nama_lengkap, aktif, must_change_password) VALUES
(1, 1, 'admin', 'admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZJ18JuywdRMRXzqMgzGqP0nW', 'Administrator', 1, 0);

-- Guru (password: guru123)
INSERT INTO users (id, school_id, role, username, password_hash, nama_lengkap, aktif, must_change_password) VALUES
(2, 1, 'guru', 'guru1', '$2y$10$V8J8nN1H7J9G5K3L2M4P6O8Q0R2S4T6U8V0W2X4Y6Z8A0B2C4D6E', 'Budi Santoso, S.Pd.', 1, 1),
(3, 1, 'guru', 'guru2', '$2y$10$V8J8nN1H7J9G5K3L2M4P6O8Q0R2S4T6U8V0W2X4Y6Z8A0B2C4D6E', 'Siti Aminah, S.Pd.', 1, 1);

INSERT INTO guru (id, user_id, school_id, nip, aktif) VALUES
(1, 2, 1, '198501012010011001', 1),
(2, 3, 1, '199002022015012002', 1);

-- Siswa (password: 01012010 -> DDMMYYYY)
INSERT INTO users (id, school_id, role, username, password_hash, nama_lengkap, aktif, must_change_password) VALUES
(4, 1, 'siswa', 'siswa1', '$2y$10$V8J8nN1H7J9G5K3L2M4P6O8Q0R2S4T6U8V0W2X4Y6Z8A0B2C4D6E', 'Andi Pratama', 1, 1),
(5, 1, 'siswa', 'siswa2', '$2y$10$V8J8nN1H7J9G5K3L2M4P6O8Q0R2S4T6U8V0W2X4Y6Z8A0B2C4D6E', 'Bunga Lestari', 1, 1);

-- Kelas
INSERT INTO kelas (id, school_id, nama, tingkat, wali_kelas_id, aktif) VALUES
(1, 1, '7A', 7, 1, 1),
(2, 1, '7B', 7, 2, 1);

INSERT INTO siswa (id, user_id, school_id, nis, kelas_id, aktif) VALUES
(1, 4, 1, '25001', 1, 1),
(2, 5, 1, '25002', 1, 1);

-- Mapel
INSERT INTO mapel (id, school_id, nama, kode, aktif) VALUES
(1, 1, 'Matematika', 'MTK', 1),
(2, 1, 'Bahasa Indonesia', 'BIN', 1),
(3, 1, 'IPA', 'IPA', 1);

-- Slot jam (contoh 8 jam)
INSERT INTO slot_jam (id, school_id, jam_ke, jam_mulai, jam_selesai) VALUES
(1, 1, 1, '07:00:00', '07:40:00'),
(2, 1, 2, '07:40:00', '08:20:00'),
(3, 1, 3, '08:20:00', '09:00:00'),
(4, 1, 4, '09:00:00', '09:40:00'),
(5, 1, 5, '10:00:00', '10:40:00'),
(6, 1, 6, '10:40:00', '11:20:00'),
(7, 1, 7, '11:20:00', '12:00:00'),
(8, 1, 8, '12:00:00', '12:40:00');