<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Thống Kê</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* CSS riêng cho Thống kê */
        .stats-grid {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        .stat-card {
            background-color: #2a2a2a;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            padding: 20px;
            margin: 15px;
            width: 30%;
            min-width: 250px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card h3 {
            color: #ccc;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        .stat-card p {
            font-size: 2.2em;
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <header class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo">🐻 BONG STORE - ADMIN</a>
            <nav class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="danh_sach_san_pham.php">Quản Lý Sản Phẩm</a>
                <a href="admin_thong_ke.php" style="font-weight: bold;">Thống Kê</a>
                <a href="#" style="color: #FFC107;">Xin chào, <?php echo htmlspecialchars($admin_name); ?></a>
                <a href="xu_ly_dang_xuat.php" class="logout-btn">Đăng Xuất</a>
            </nav>
        </div>
    </header>

    <div class="content-container">
        <h2 style="color: white; text-align: center;">BẢNG ĐIỀU KHIỂN & THỐNG KÊ (ADMIN)</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>TỔNG DOANH THU</h3>
                <p><?php echo number_format($total_revenue, 0, ',', '.'); ?> VNĐ</p>
            </div>
            
            <div class="stat-card">
                <h3>TỔNG SỐ ĐƠN HÀNG</h3>
                <p style="color: #00BFFF;"><?php echo number_format($total_orders, 0, ',', '.'); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>ĐƠN HÀNG CHỜ XÁC NHẬN</h3>
                <p style="color: #FFC107;"><?php echo number_format($pending_orders, 0, ',', '.'); ?></p>
            </div>
        </div>

        <p style="text-align: center; margin-top: 40px;"><a href="danh_sach_san_pham.php">Quay lại Quản lý Sản phẩm</a></p>
    </div>
</body>
</html>