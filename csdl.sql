-- CHẠY LỆNH NÀY ĐỂ XÓA MỌI DỮ LIỆU CŨ VÀ BẮT ĐẦU SẠCH
DROP DATABASE IF EXISTS quanlybanhang;

-- 1. TẠO SCHEMA VÀ SỬ DỤNG
CREATE SCHEMA quanlybanhang;
USE quanlybanhang;

-- 2. Bảng DanhMuc
CREATE TABLE DanhMuc (
    MaDM INT PRIMARY KEY AUTO_INCREMENT,
    TenDM VARCHAR(255) NOT NULL,
    MoTa TEXT
);
INSERT INTO DanhMuc (TenDM, MoTa) VALUES 
('Gấu Bông Hoạt Hình', 'Gấu bông mô phỏng nhân vật hoạt hình.'),
('Gấu Bông Teddy', 'Các loại gấu Teddy truyền thống.'),
('Gối Ôm/Thú Bông Lớn', 'Gối ôm và thú bông kích thước lớn.');


-- 3. Bảng NguoiDung (User)
CREATE TABLE NguoiDung (
    MaND INT PRIMARY KEY AUTO_INCREMENT,
    TenDangNhap VARCHAR(50) NOT NULL UNIQUE,
    MatKhau VARCHAR(255) NOT NULL, 
    HoTen VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    DienThoai VARCHAR(15),
    LoaiND VARCHAR(50) DEFAULT 'KhachHang'
);
-- TẠO TÀI KHOẢN ADMIN MẪU
INSERT INTO NguoiDung (TenDangNhap, MatKhau, HoTen, LoaiND) VALUES 
('NGAN', '123456', 'Thu Ngân', 'Admin'); 


-- 4. Bảng SanPham (PRODUCT) - ĐÃ CÓ CỘT DỊCH VỤ CHÍNH XÁC
CREATE TABLE SanPham (
    MaSP INT PRIMARY KEY AUTO_INCREMENT,
    TenSP VARCHAR(255) NOT NULL,
    Gia DECIMAL(10, 2) NOT NULL,
    SoLuongTon INT NOT NULL DEFAULT 0,
    MoTa TEXT,
    HinhAnh VARCHAR(255),
    MaDM INT,
    
    -- CÁC CỘT DỊCH VỤ CẦN THIẾT
    is_free_gift TINYINT(1) DEFAULT 0, 
    is_ship_fast TINYINT(1) DEFAULT 0, 

    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM)
);
-- DỮ LIỆU SẢN PHẨM MẪU ĐÃ ĐƯỢC ĐÁNH DẤU LỌC
INSERT INTO SanPham (MaSP, TenSP, Gia, SoLuongTon, MoTa, MaDM, HinhAnh, is_free_gift, is_ship_fast) VALUES 
(1, 'Gấu Bông Pikachu', 150000, 50, 'Gấu bông hình Pikachu.', 1, 'gaubong_pikachu.jpg', 1, 0), -- Có Gói Quà
(2, 'Gấu Teddy Lớn', 350000, 30, 'Gấu Teddy cao 1m2.', 2, 'gauteddy_lon.png', 1, 1), -- Có cả 2 (Gói quà & Giao nhanh)
(3, 'Gối Ôm Cá Mập', 200000, 70, 'Gối ôm hình cá mập.', 3, 'goi_om_ca_map.jpg', 0, 1); -- Có Giao nhanh


-- 5. Bảng DonHang (Order)
CREATE TABLE DonHang (
    MaDH INT PRIMARY KEY AUTO_INCREMENT,
    MaND INT NOT NULL,
    NgayDat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    TongTien DECIMAL(12, 2) NOT NULL,
    TrangThai VARCHAR(50) NOT NULL, 
    DiaChiGiaoHang VARCHAR(255),
    FOREIGN KEY (MaND) REFERENCES NguoiDung(MaND)
);

-- 6. Bảng ChiTietDonHang (Order Detail)
CREATE TABLE ChiTietDonHang (
    MaCTDH INT PRIMARY KEY AUTO_INCREMENT,
    MaDH INT NOT NULL,
    MaSP INT NOT NULL,
    SoLuong INT NOT NULL,
    DonGia DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (MaDH, MaSP), 
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH),
    FOREIGN KEY (MaSP) REFERENCES SanPham(MaSP)
);
USE quanlybanhang;

-- 1. Xóa bảng ChiTietDonHang (Do có khóa ngoại đến SanPham)
DROP TABLE IF EXISTS ChiTietDonHang; 

-- 2. Xóa sạch dữ liệu và đặt lại bộ đếm ID cho bảng SanPham
DELETE FROM SanPham;
ALTER TABLE SanPham AUTO_INCREMENT = 1;

-- 3. CHÈN LẠI DỮ LIỆU SẢN PHẨM CHUẨN ĐÃ ĐÁNH DẤU LỌC
INSERT INTO SanPham (MaSP, TenSP, Gia, SoLuongTon, MoTa, MaDM, HinhAnh, is_free_gift, is_ship_fast) VALUES 
(1, 'Gấu Bông Pikachu', 150000, 50, 'Gấu bông hình Pikachu.', 1, 'gaubong_pikachu.jpg', 1, 0), -- Có Gói Quà
(2, 'Gấu Teddy Lớn', 350000, 30, 'Gấu Teddy cao 1m2.', 2, 'gauteddy_lon.png', 1, 1), -- Có cả 2
(3, 'Gối Ôm Cá Mập', 200000, 70, 'Gối ôm hình cá mập.', 3, 'goi_om_ca_map.jpg', 0, 1); -- Có Giao Hàng Nhanh

-- 4. TẠO LẠI BẢNG ChiTietDonHang
CREATE TABLE ChiTietDonHang (
    MaCTDH INT PRIMARY KEY AUTO_INCREMENT,
    MaDH INT NOT NULL,
    MaSP INT NOT NULL,
    SoLuong INT NOT NULL,
    DonGia DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (MaDH, MaSP), 
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH),
    FOREIGN KEY (MaSP) REFERENCES SanPham(MaSP)
);

SET SQL_SAFE_UPDATES = 0;
USE quanlybanhang;

-- 1. Xóa bảng ChiTietDonHang 
DROP TABLE IF EXISTS ChiTietDonHang; 

-- 2. XÓA SẠCH DỮ LIỆU BẢNG SẢN PHẨM (Lệnh này sẽ chạy được sau khi tắt Safe Mode)
DELETE FROM SanPham;
ALTER TABLE SanPham AUTO_INCREMENT = 1;

-- 3. CHÈN LẠI DỮ LIỆU SẢN PHẨM CHUẨN (Sau khi xóa sạch, lệnh này sẽ không bị lỗi Duplicate Key)
INSERT INTO SanPham (MaSP, TenSP, Gia, SoLuongTon, MoTa, MaDM, HinhAnh, is_free_gift, is_ship_fast) VALUES 
(1, 'Gấu Bông Pikachu', 150000, 50, 'Gấu bông hình Pikachu.', 1, 'gaubong_pikachu.jpg', 1, 0), 
(2, 'Gấu Teddy Lớn', 350000, 30, 'Gấu Teddy cao 1m2.', 2, 'gauteddy_lon.png', 1, 1), 
(3, 'Gối Ôm Cá Mập', 200000, 70, 'Gối ôm hình cá mập.', 3, 'goi_om_ca_map.jpg', 0, 1); 

-- 4. TẠO LẠI BẢNG ChiTietDonHang
CREATE TABLE ChiTietDonHang (
    MaCTDH INT PRIMARY KEY AUTO_INCREMENT,
    MaDH INT NOT NULL,
    MaSP INT NOT NULL,
    SoLuong INT NOT NULL,
    DonGia DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (MaDH, MaSP), 
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH),
    FOREIGN KEY (MaSP) REFERENCES SanPham(MaSP)
);


-- BƯỚC 0: TẮT CHẾ ĐỘ BẢO VỆ (Quan trọng để lệnh DELETE chạy được)
SET SQL_SAFE_UPDATES = 0;

USE quanlybanhang;

-- 1. XÓA CÁC BẢNG CÓ KHÓA NGOẠI TỚI SanPham và DanhMuc
DROP TABLE IF EXISTS ChiTietDonHang; 
DROP TABLE IF EXISTS SanPham;

-- 2. KHÔI PHỤC BẢNG DANH MỤC
DROP TABLE IF EXISTS DanhMuc;
CREATE TABLE DanhMuc (
    MaDM INT PRIMARY KEY AUTO_INCREMENT,
    TenDM VARCHAR(255) NOT NULL,
    MoTa TEXT
);

-- CHÈN LẠI DANH MỤC GỐC (với MaDM 1, 2, 3)
INSERT INTO DanhMuc (MaDM, TenDM, MoTa) VALUES 
(1, 'Gấu Bông Hoạt Hình', 'Gấu bông mô phỏng nhân vật hoạt hình.'),
(2, 'Gấu Bông Teddy', 'Các loại gấu Teddy truyền thống.'),
(3, 'Gối Ôm/Thú Bông Lớn', 'Gối ôm và thú bông kích thước lớn.');


-- 3. KHÔI PHỤC BẢNG SẢN PHẨM (Có đủ cột dịch vụ)
CREATE TABLE SanPham (
    MaSP INT PRIMARY KEY AUTO_INCREMENT,
    TenSP VARCHAR(255) NOT NULL,
    Gia DECIMAL(10, 2) NOT NULL,
    SoLuongTon INT NOT NULL DEFAULT 0,
    MoTa TEXT,
    HinhAnh VARCHAR(255),
    MaDM INT,
    
    is_free_gift TINYINT(1) DEFAULT 0, 
    is_ship_fast TINYINT(1) DEFAULT 0, 

    FOREIGN KEY (MaDM) REFERENCES DanhMuc(MaDM)
);

-- CHÈN LẠI SẢN PHẨM MẪU VÀ GÁN ĐÚNG DANH MỤC (MaDM)
INSERT INTO SanPham (MaSP, TenSP, Gia, SoLuongTon, MoTa, MaDM, HinhAnh, is_free_gift, is_ship_fast) VALUES 
-- Gấu Bông Pikachu (Hoạt Hình) -> MaDM = 1
(1, 'Gấu Bông Pikachu', 150000, 50, 'Gấu bông hình Pikachu.', 1, 'gaubong_pikachu.jpg', 1, 0), 
-- Gấu Teddy Lớn (Teddy) -> MaDM = 2
(2, 'Gấu Teddy Lớn', 350000, 30, 'Gấu Teddy cao 1m2.', 2, 'gauteddy_lon.png', 1, 1), 
-- Gối Ôm Cá Mập (Gối Ôm/Thú Bông Lớn) -> MaDM = 3
(3, 'Gối Ôm Cá Mập', 200000, 70, 'Gối ôm hình cá mập.', 3, 'goi_om_ca_map.jpg', 0, 1); 

-- 4. TẠO LẠI BẢNG ChiTietDonHang
CREATE TABLE ChiTietDonHang (
    MaCTDH INT PRIMARY KEY AUTO_INCREMENT,
    MaDH INT NOT NULL,
    MaSP INT NOT NULL,
    SoLuong INT NOT NULL,
    DonGia DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (MaDH, MaSP), 
    FOREIGN KEY (MaDH) REFERENCES DonHang(MaDH),
    FOREIGN KEY (MaSP) REFERENCES SanPham(MaSP)
);

UPDATE NguoiDung 
SET MatKhau = '$2y$10$Mc/dErPpXkQHE/sDSo95zuypsT8n7zkWJ.jgoiAo.d91xaXu2hqhy' 
WHERE TenDangNhap = 'NGAN';

SELECT MD5('123456');
-- Kết quả sẽ là: e10adc3949ba59abbe56e057f20f883e

UPDATE NguoiDung 
SET MatKhau = 'e10adc3949ba59abbe56e057f20f883e' 
WHERE TenDangNhap = 'NGAN';

CREATE TABLE ThongBaoAdmin (
    MaTB INT PRIMARY KEY AUTO_INCREMENT,
    LoaiThongBao VARCHAR(50) NOT NULL, -- Ví dụ: 'cart', 'order'
    MaND INT NOT NULL, -- Mã người dùng gây ra sự kiện
    NoiDung TEXT NOT NULL, -- Nội dung thông báo (Ví dụ: "vừa thêm sản phẩm")
    ThoiGian DATETIME DEFAULT CURRENT_TIMESTAMP,
    DaXem BOOLEAN DEFAULT FALSE, -- Admin đã xem hay chưa
    -- Khóa ngoại, nếu cần
    FOREIGN KEY (MaND) REFERENCES NguoiDung(MaND)
);
-- 1. Thêm cột số lượng đã bán (sold)
ALTER TABLE SanPham
ADD DaBan INT DEFAULT 0;

-- 2. Thêm cột kích thước (size)
ALTER TABLE SanPham
ADD KichThuoc VARCHAR(20) DEFAULT 'N/A';

-- 3. Tạo bảng mới để lưu trữ Đánh giá của Khách hàng
CREATE TABLE DanhGia (
    MaDG INT PRIMARY KEY AUTO_INCREMENT,
    MaSP INT NOT NULL,
    MaND INT, -- Có thể NULL nếu đánh giá là ẩn danh (nhưng nên có FK tới NguoiDung)
    Diem float DEFAULT 0, -- Điểm đánh giá (ví dụ: 4.5, 5)
    NoiDung TEXT,
    ThoiGian DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaSP) REFERENCES SanPham(MaSP),
    FOREIGN KEY (MaND) REFERENCES NguoiDung(MaND)
);

-- 1. Thêm cột Đã Bán
ALTER TABLE SanPham ADD DaBan INT NOT NULL DEFAULT 0;

-- 2. Thêm cột Kích Thước
ALTER TABLE SanPham ADD KichThuoc VARCHAR(50) NOT NULL DEFAULT '';
SELECT AVG(Diem) as TrungBinh, COUNT(MaDG) as TongLuotDanhGia 
FROM DanhGia 
WHERE MaSP = 1; -- Thay 1 bằng mã sản phẩm cụ thể

ALTER TABLE DanhGia ADD NgayDanhGia DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE SanPham ADD is_free_gift INT DEFAULT 0;

--- Thêm cột mô tả sản phẩm
ALTER TABLE SanPham ADD MoTa TEXT;

-- Thêm cột dịch vụ giao nhanh
ALTER TABLE SanPham ADD is_ship_fast INT DEFAULT 0;

-- Thêm cột ngày đánh giá (để fix lỗi ảnh image_129c2d.png)
ALTER TABLE DanhGia ADD NgayDanhGia DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Xóa bớt dữ liệu cũ nếu muốn làm sạch (Tùy chọn)
-- DELETE FROM SanPham;

-- 1. DANH MỤC: GẤU BÔNG HOẠT HÌNH (MaDM = 1)
INSERT INTO SanPham (TenSP, Gia, SoLuongTon, MaDM, HinhAnh, KichThuoc, MoTa, DaBan) VALUES
('Pikachu Bông Đáng Yêu', 150000, 50, 1, 'pikachu.jpg', '35cm', 'Pikachu mềm mịn', 0),
('Mèo Máy Doraemon', 180000, 45, 1, 'doraemon.jpg', '40cm', 'Doraemon bản gốc', 0),
('Gấu Hồng Lotso', 220000, 30, 1, 'lotso.jpg', '45cm', 'Gấu dâu có mùi thơm', 0),
('Sâu Larva Vàng', 120000, 60, 1, 'larva.jpg', '30cm', 'Sâu Larva tinh nghịch', 0),
('Chó Cứu Hộ Paw', 190000, 25, 1, 'paw.jpg', '40cm', 'Đội chó cứu hộ', 0),
('Stitch Xanh Quậy Phá', 210000, 35, 1, 'stitch.jpg', '45cm', 'Stitch đáng yêu', 0),
('Vịt Vàng Đeo Nơ', 110000, 100, 1, 'vit_vang.jpg', '30cm', 'Vịt vàng hot trend', 0),
('Shin Cậu Bé Bút Chì', 250000, 20, 1, 'shin.jpg', '50cm', 'Shin mặc pijama', 0);

-- 2. DANH MỤC: GẤU TEDDY (MaDM = 2)
INSERT INTO SanPham (TenSP, Gia, SoLuongTon, MaDM, HinhAnh, KichThuoc, MoTa, DaBan) VALUES
('Teddy Ôm Tim Đỏ', 350000, 20, 2, 'teddy_tim.jpg', '80cm', 'Teddy quà tặng lễ tình nhân', 0),
('Teddy Áo Len Xám', 450000, 15, 2, 'teddy_ao_len.jpg', '1m', 'Teddy lông xù cao cấp', 0),
('Teddy Khổng Lồ 1m5', 850000, 10, 2, 'teddy_1m5.jpg', '1m5', 'Gấu khổng lồ siêu to', 0),
('Teddy Nơ Hồng', 280000, 30, 2, 'teddy_no.jpg', '60cm', 'Gấu bông cho bé gái', 0),
('Teddy Head & Tales', 320000, 25, 2, 'teddy_ht.jpg', '70cm', 'Teddy phong cách Anh', 0),
('Teddy Cử Nhân', 250000, 50, 2, 'teddy_cn.jpg', '40cm', 'Quà tốt nghiệp ý nghĩa', 0),
('Teddy Lông Xoắn xoáy', 380000, 18, 2, 'teddy_xoan.jpg', '90cm', 'Lông xoắn mềm mại', 0),
('Teddy Mặc Yếm', 300000, 22, 2, 'teddy_yem.jpg', '75cm', 'Teddy dễ thương', 0);

-- 3. DANH MỤC: THÚ BÔNG KHÁC (MaDM = 3)
INSERT INTO SanPham (TenSP, Gia, SoLuongTon, MaDM, HinhAnh, KichThuoc, MoTa, DaBan) VALUES
('Cá Mập Xanh Đại Dương', 200000, 40, 3, 'ca_map.jpg', '1m', 'Cá mập mềm mại', 0),
('Khủng Long Cổ Dài', 240000, 30, 3, 'khung_long.jpg', '80cm', 'Khủng long xanh lá', 0),
('Heo Hồng Mắt Híp', 150000, 55, 3, 'heo_hip.jpg', '40cm', 'Heo bông nằm ngủ', 0),
('Chó Husky Ngáo', 270000, 20, 3, 'husky.jpg', '60cm', 'Chó Husky đáng yêu', 0),
('Mèo Hoàng Thượng', 190000, 35, 3, 'meo.jpg', '45cm', 'Mèo bông hoàng gia', 0),
('Thỏ Tai Dài Hồng', 160000, 40, 3, 'tho.jpg', '50cm', 'Thỏ bông tai dài mềm', 0),
('Chim Cánh Cụt Pingu', 180000, 25, 3, 'chim_canh_cut.jpg', '35cm', 'Chim cánh cụt tròn trịa', 0),
('Voi Con Xám', 210000, 15, 3, 'voi.jpg', '40cm', 'Voi con thông minh', 0);

SELECT * FROM quanlybanhang.DanhGia;
ALTER TABLE SanPham ADD DaBan INT DEFAULT 0;
ALTER TABLE SanPham ADD KichThuoc VARCHAR(50) DEFAULT 'N/A';