<?php
session_start();
include 'db_connect.php';

// Kiểm tra Session đăng nhập
if (!isset($_SESSION['MaND'])) {
    header("Location: dang_nhap.php");
    exit();
}
$ma_nd_session = $_SESSION['MaND']; 

// Lọc theo trạng thái
$trang_thai_filter = $_GET['trangthai'] ?? 'Tất cả';

$sql = "SELECT dh.MaDH, dh.NgayDat, dh.TongTien, dh.TrangThai, 
               ct.MaSP, ct.DonGia, ct.SoLuong, sp.TenSP
        FROM DonHang dh
        LEFT JOIN ChiTietDonHang ct ON dh.MaDH = ct.MaDH
        LEFT JOIN SanPham sp ON ct.MaSP = sp.MaSP
        WHERE dh.MaND = ?";

// Thêm điều kiện lọc trạng thái nếu không phải 'Tất cả'
if ($trang_thai_filter !== 'Tất cả') {
    $sql .= " AND dh.TrangThai = ?";
}

$sql .= " ORDER BY dh.NgayDat DESC";
$stmt = $conn->prepare($sql);

if ($trang_thai_filter !== 'Tất cả') {
    $stmt->bind_param("is", $ma_nd_session, $trang_thai_filter);
} else {
    $stmt->bind_param("i", $ma_nd_session);
}

$stmt->execute();
$result = $stmt->get_result();

$don_hangs = [];
while ($row = $result->fetch_assoc()) {
    $ma_dh = $row['MaDH'];
    
    if (!isset($don_hangs[$ma_dh])) {
        // Khởi tạo đơn hàng nếu chưa tồn tại
        $don_hangs[$ma_dh] = [
            'MaDH' => $ma_dh,
            'NgayDat' => $row['NgayDat'],
            'TongTien' => $row['TongTien'],
            'TrangThai' => $row['TrangThai'],
            'SanPham' => [],
        ];
    }
    
    // Thêm sản phẩm vào chi tiết đơn hàng
    $don_hangs[$ma_dh]['SanPham'][] = [
        'TenSP' => $row['TenSP'],
        'SoLuong' => $row['SoLuong'],
        'DonGia' => $row['DonGia'],
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Đơn Hàng</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #333;
            color: white;
        }
        .history-table th, .history-table td {
            border: 1px solid #555;
            padding: 12px;
            text-align: left;
        }
        .history-table th {
            background-color: #4CAF50;
            color: white;
        }
        .history-table tr:nth-child(even) {
            background-color: #3f3f3f;
        }
        .history-table td a {
            color: #4CAF50;
            text-decoration: none;
        }
        .history-table td a:hover {
            text-decoration: underline;
        }
        .filter-section {
            margin-bottom: 20px;
        }
        .filter-section label, .filter-section select {
            font-size: 1em;
            color: white;
        }
        .empty-message {
            margin-top: 50px;
            font-size: 1.2em;
            color: #ddd;
            text-align: center;
        }
        .chi-tiet-sp {
            font-size: 0.9em;
            color: #bbb;
            margin-top: 5px;
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
                <?php if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] == 'Admin'): ?>
                <a href="danh_sach_san_pham.php">Quản Lý Sản Phẩm</a> 
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="content-container">
        <h1 style="color: #4CAF50;">LỊCH SỬ ĐƠN HÀNG CỦA BẠN</h1>

        <div class="filter-section">
            <form method="GET" action="lich_su_don_hang.php">
                <label for="trangthai">Lọc theo Trạng Thái:</label>
                <select name="trangthai" id="trangthai" onchange="this.form.submit()">
                    <option value="Tất cả" <?php echo ($trang_thai_filter === 'Tất cả') ? 'selected' : ''; ?>>-- Tất cả --</option>
                    <option value="Chờ xử lý" <?php echo ($trang_thai_filter === 'Chờ xử lý') ? 'selected' : ''; ?>>Chờ xử lý</option>
                    <option value="Đã giao" <?php echo ($trang_thai_filter === 'Đã giao') ? 'selected' : ''; ?>>Đã giao</option>
                    <option value="Đã hủy" <?php echo ($trang_thai_filter === 'Đã hủy') ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </form>
        </div>

        <?php if (empty($don_hangs)): ?>
            <div class="empty-message">
                <p>Bạn chưa có đơn hàng nào hoặc không có đơn hàng phù hợp với điều kiện lọc.</p>
                <p><a href="index.php" class="add-to-cart">🏠 Quay lại Trang Chủ</a></p>
            </div>
        <?php else: ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Tên Đơn Hàng (Sản phẩm đầu)</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Chi Tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($don_hangs as $dh): 
                        // Lấy tên sản phẩm đầu tiên để làm tên đơn hàng
                        $ten_sp_dau = $dh['SanPham'][0]['TenSP'] ?? 'Không có sản phẩm';
                        
                        // --- ĐỊNH DẠNG MÃ ĐƠN HÀNG ---
                        // Ví dụ: DH-YYMMDD-ID (Ví dụ: DH-251206-00019)
                        $ngay_dat_hang = date('ymd', strtotime($dh['NgayDat'])); 
                        $ma_dh_so = str_pad($dh['MaDH'], 5, '0', STR_PAD_LEFT); 
                        $ma_dh_hien_thi = "DH-" . $ngay_dat_hang . "-" . $ma_dh_so; 
                    ?>
                        <tr>
                            <td>**<?php echo $ma_dh_hien_thi; ?>**</td>
                            <td><?php echo htmlspecialchars($ten_sp_dau); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($dh['NgayDat'])); ?></td>
                            <td><?php echo number_format($dh['TongTien'], 0, ',', '.') . ' VND'; ?></td>
                            <td><?php echo htmlspecialchars($dh['TrangThai']); ?></td>
                            <td><a href="chi_tiet_don_hang.php?madh=<?php echo $dh['MaDH']; ?>">Xem Chi Tiết</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>