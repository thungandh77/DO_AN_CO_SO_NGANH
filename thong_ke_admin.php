<?php
session_start();
include 'db_connect.php';

// Bảo mật: Chỉ cho phép Admin truy cập
if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] != 'Admin') {
    header("Location: index.php");
    exit();
}

// --- 1. TRUY VẤN LẤY DỮ LIỆU THỐNG KÊ ---

// 1. Tổng Doanh Thu (từ các đơn hàng Đã xử lý hoặc Đã giao)
// Giả sử chỉ tính doanh thu từ đơn hàng không bị hủy
$sql_doanhthu = "SELECT SUM(TongTien) AS TotalRevenue FROM DonHang WHERE TrangThai != 'Đã hủy'";
$result_doanhthu = $conn->query($sql_doanhthu);
$doanh_thu = $result_doanhthu->fetch_assoc()['TotalRevenue'] ?? 0;

// 2. Tổng Số Đơn Hàng
$sql_tongdh = "SELECT COUNT(MaDH) AS TotalOrders FROM DonHang";
$result_tongdh = $conn->query($sql_tongdh);
$tong_don_hang = $result_tongdh->fetch_assoc()['TotalOrders'] ?? 0;

// 3. Số Đơn Hàng Chờ Xử Lý
$sql_chodh = "SELECT COUNT(MaDH) AS PendingOrders FROM DonHang WHERE TrangThai = 'Chờ xử lý'";
$result_chodh = $conn->query($sql_chodh);
$don_hang_cho_xu_ly = $result_chodh->fetch_assoc()['PendingOrders'] ?? 0;

// 4. Tổng Số Sản Phẩm (Đã thêm vào hệ thống)
$sql_tongsp = "SELECT COUNT(MaSP) AS TotalProducts FROM SanPham";
$result_tongsp = $conn->query($sql_tongsp);
$tong_san_pham = $result_tongsp->fetch_assoc()['TotalProducts'] ?? 0;

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Thống Kê (Admin)</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* CSS nội bộ để tạo giao diện Card Thống kê đẹp mắt */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            /* Thêm hover để có hiệu ứng đẹp hơn */
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .stat-card h3 {
            color: #6c757d;
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 2.2em;
            font-weight: bold;
            color: #28a745; /* Màu xanh lá cây cho số liệu tích cực */
        }
        .pending .value {
            color: #ffc107; /* Màu vàng cho số liệu cần chú ý */
        }
    </style>
</head>
<body>
    
    <header class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo">🐻 BONG STORE</a>
            <nav class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="gio_hang.php">Giỏ Hàng</a>
                <a href="lich_su_don_hang.php">Lịch Sử Đơn Hàng</a>
                <a href="danh_sach_san_pham.php">Quản Lý Sản Phẩm</a>
                <a href="thong_ke_admin.php" style="font-weight: bold; color: #FFC107;">Thống Kê</a>
                <a href="xu_ly_dang_xuat.php" class="logout-btn">Đăng Xuất</a>
            </nav>
        </div>
    </header>

    <div class="content-container">
        <h2>📊 BÁO CÁO TỔNG QUAN HỆ THỐNG</h2>

        <div class="stats-grid">
            
            <div class="stat-card">
                <h3>💰 TỔNG DOANH THU</h3>
                <div class="value" style="color: #28a745;">
                    <?php echo number_format($doanh_thu, 0, ',', '.'); ?> VNĐ
                </div>
            </div>

            <div class="stat-card">
                <h3>🛒 TỔNG SỐ ĐƠN HÀNG</h3>
                <div class="value" style="color: #007bff;">
                    <?php echo $tong_don_hang; ?>
                </div>
            </div>

            <div class="stat-card pending">
                <h3>⏳ CHỜ XỬ LÝ</h3>
                <div class="value">
                    <?php echo $don_hang_cho_xu_ly; ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>📦 TỔNG SẢN PHẨM</h3>
                <div class="value" style="color: #6610f2;">
                    <?php echo $tong_san_pham; ?>
                </div>
            </div>

        </div>
        
        <p style="text-align: center; margin-top: 50px;">
            <a href="danh_sach_san_pham.php" class="add-to-cart" style="width: 250px; display: inline-block;">Quản lý Sản phẩm</a>
        </p>
    </div>
</body>
</html>