-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 27, 2026 lúc 06:37 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanly_nganhang`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giao_dich`
--

CREATE TABLE `giao_dich` (
  `MaGD` varchar(50) NOT NULL,
  `SoTK` varchar(20) DEFAULT NULL,
  `MaNV` varchar(20) DEFAULT NULL,
  `SoTK_Nhan` varchar(20) DEFAULT NULL,
  `LoaiGD` enum('Nạp tiền','Rút tiền','Chuyển khoản') NOT NULL,
  `SoTien` decimal(15,2) NOT NULL,
  `ThoiGian` datetime DEFAULT current_timestamp(),
  `NoiDung` text DEFAULT NULL,
  `TrangThaiXoa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `giao_dich`
--

INSERT INTO `giao_dich` (`MaGD`, `SoTK`, `MaNV`, `SoTK_Nhan`, `LoaiGD`, `SoTien`, `ThoiGian`, `NoiDung`, `TrangThaiXoa`) VALUES
('GD_69eee75ec30d0', '1031203105', 'NV001', '1031203104', 'Chuyển khoản', 36000.00, '2026-04-27 11:34:38', 'abc-tod', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khach_hang`
--

CREATE TABLE `khach_hang` (
  `MaKH` varchar(20) NOT NULL,
  `TenKH` varchar(100) NOT NULL,
  `CCCD` varchar(20) NOT NULL,
  `SDT` varchar(20) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DiaChi` text DEFAULT NULL,
  `NgayDangKy` datetime DEFAULT current_timestamp(),
  `TrangThaiXoa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khach_hang`
--

INSERT INTO `khach_hang` (`MaKH`, `TenKH`, `CCCD`, `SDT`, `Email`, `DiaChi`, `NgayDangKy`, `TrangThaiXoa`) VALUES
('001', 'Nguyen Manh Long ', '033214012400', '0976312463', 'long@gmail.com', 'hanoi', '2026-04-27 10:41:53', 0),
('KH002', 'Nguyễn Văn A', '031041014301', '0123456789', 'a@gmail.com', 'ha tay', '2026-04-27 11:25:26', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_the`
--

CREATE TABLE `loai_the` (
  `MaLoaiThe` varchar(20) NOT NULL,
  `TenLoai` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `TrangThaiXoa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_vien`
--

CREATE TABLE `nhan_vien` (
  `MaNV` varchar(20) NOT NULL,
  `TenNV` varchar(100) NOT NULL,
  `ChucVu` varchar(50) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `TrangThaiXoa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_vien`
--

INSERT INTO `nhan_vien` (`MaNV`, `TenNV`, `ChucVu`, `SDT`, `TrangThaiXoa`) VALUES
('NV001', 'Nguyễn Văn Admin', 'Giao dịch viên', '0901234567', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tai_khoan`
--

CREATE TABLE `tai_khoan` (
  `SoTK` varchar(20) NOT NULL,
  `MaKH` varchar(20) DEFAULT NULL,
  `SoDu` decimal(15,2) DEFAULT 0.00,
  `NgayMo` datetime DEFAULT current_timestamp(),
  `TrangThai` varchar(20) DEFAULT 'Hoạt động',
  `TrangThaiXoa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tai_khoan`
--

INSERT INTO `tai_khoan` (`SoTK`, `MaKH`, `SoDu`, `NgayMo`, `TrangThai`, `TrangThaiXoa`) VALUES
('1031203104', '001', 36000.00, '2026-04-27 10:43:41', 'Hoạt động', 0),
('1031203105', 'KH002', 64000.00, '2026-04-27 11:26:48', 'Hoạt động', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `the`
--

CREATE TABLE `the` (
  `SoThe` varchar(20) NOT NULL,
  `SoTK` varchar(20) DEFAULT NULL,
  `MaLoaiThe` varchar(20) DEFAULT NULL,
  `NgayPhatHanh` datetime DEFAULT current_timestamp(),
  `NgayHetHan` datetime DEFAULT NULL,
  `MaPIN` varchar(255) NOT NULL,
  `TrangThaiXoa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `giao_dich`
--
ALTER TABLE `giao_dich`
  ADD PRIMARY KEY (`MaGD`),
  ADD KEY `SoTK` (`SoTK`),
  ADD KEY `MaNV` (`MaNV`),
  ADD KEY `SoTK_Nhan` (`SoTK_Nhan`);

--
-- Chỉ mục cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`MaKH`),
  ADD UNIQUE KEY `CCCD` (`CCCD`);

--
-- Chỉ mục cho bảng `loai_the`
--
ALTER TABLE `loai_the`
  ADD PRIMARY KEY (`MaLoaiThe`);

--
-- Chỉ mục cho bảng `nhan_vien`
--
ALTER TABLE `nhan_vien`
  ADD PRIMARY KEY (`MaNV`);

--
-- Chỉ mục cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD PRIMARY KEY (`SoTK`),
  ADD KEY `MaKH` (`MaKH`);

--
-- Chỉ mục cho bảng `the`
--
ALTER TABLE `the`
  ADD PRIMARY KEY (`SoThe`),
  ADD KEY `SoTK` (`SoTK`),
  ADD KEY `MaLoaiThe` (`MaLoaiThe`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `giao_dich`
--
ALTER TABLE `giao_dich`
  ADD CONSTRAINT `giao_dich_ibfk_1` FOREIGN KEY (`SoTK`) REFERENCES `tai_khoan` (`SoTK`),
  ADD CONSTRAINT `giao_dich_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `nhan_vien` (`MaNV`),
  ADD CONSTRAINT `giao_dich_ibfk_3` FOREIGN KEY (`SoTK_Nhan`) REFERENCES `tai_khoan` (`SoTK`);

--
-- Các ràng buộc cho bảng `tai_khoan`
--
ALTER TABLE `tai_khoan`
  ADD CONSTRAINT `tai_khoan_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khach_hang` (`MaKH`);

--
-- Các ràng buộc cho bảng `the`
--
ALTER TABLE `the`
  ADD CONSTRAINT `the_ibfk_1` FOREIGN KEY (`SoTK`) REFERENCES `tai_khoan` (`SoTK`),
  ADD CONSTRAINT `the_ibfk_2` FOREIGN KEY (`MaLoaiThe`) REFERENCES `loai_the` (`MaLoaiThe`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
