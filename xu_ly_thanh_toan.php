<?php
// BẬT HIỂN THỊ LỖI
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php'; // Đảm bảo file này tồn tại và kết nối đúng

// Kiểm tra Session đăng nhập
if (!isset($_SESSION['MaND'])) {
    header("Location: dang_nhap.php");
    exit();
}
$ma_nd_session = $_SESSION['MaND']; 

// Lấy dữ liệu từ POST và Giỏ hàng
$gio_hang = $_SESSION['cart'] ?? []; 
$tong_tien_post = $_POST['tong_tien_gui'] ?? 0;
$ho_ten = $_POST['ho_ten'] ?? '';
$sdt = $_POST['sdt'] ?? ''; 
$dia_chi = $_POST['dia_chi'] ?? '';

// --- 1. KIỂM TRA ĐIỀU KIỆN BAN ĐẦU ---
if (empty($gio_hang)) {
    header("Location: gio_hang.php?error=no_items");
    exit();
}
if (empty($ho_ten) || empty($sdt) || empty($dia_chi)) {
    header("Location: gio_hang.php?error=missing_info");
    exit();
}

// --- 2. TÍNH TỔNG TIỀN THỰC TẾ TỪ CSDL ---
$tong_tien_thuc_te = 0;
$san_pham_chi_tiet = [];
$ma_sp_list = array_keys($gio_hang);

if (!empty($ma_sp_list)) {
    $placeholders = implode(',', array_fill(0, count($ma_sp_list), '?'));
    $sql = "SELECT MaSP, Gia FROM SanPham WHERE MaSP IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // BIND PARAMETERS
    $types = str_repeat('i', count($ma_sp_list));
    $params = [$types]; 
    
    foreach ($ma_sp_list as &$ma_sp_ref) {
        $params[] = &$ma_sp_ref;
    }
    call_user_func_array([$stmt, 'bind_param'], $params);
    unset($ma_sp_ref); 
    
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()) {
        $ma_sp = $row['MaSP'];
        $gia_sp = $row['Gia'];
        $so_luong = $gio_hang[$ma_sp]['soluong'];
        
        $tong_tien_thuc_te += $gia_sp * $so_luong;
        
        $san_pham_chi_tiet[] = [
            'MaSP' => $ma_sp,
            'DonGia' => $gia_sp,
            'SoLuong' => $so_luong
        ];
    }
    $stmt->close();
}

// --- 3. KIỂM TRA TỔNG TIỀN ---
if (abs($tong_tien_post - $tong_tien_thuc_te) > 10) { 
    header("Location: gio_hang.php?error=price_mismatch");
    exit();
}

// Khởi tạo Transaction
$conn->begin_transaction();
$thanh_cong = false;
$ma_dh_moi = 0; 
$error_message = '';

try {
    // --- 4. CHÈN VÀO BẢNG DONHANG ---
    $trang_thai_ban_dau = 'Chờ xử lý';
    $sql_dh = "INSERT INTO DonHang (MaND, NgayDat, TongTien, TrangThai, DiaChiGiaoHang) 
                VALUES (?, NOW(), ?, ?, ?)";
    $stmt_dh = $conn->prepare($sql_dh);
    
    $stmt_dh->bind_param("idss", $ma_nd_session, $tong_tien_thuc_te, $trang_thai_ban_dau, $dia_chi);
    $stmt_dh->execute();
    
    $ma_dh_moi = $conn->insert_id;
    $stmt_dh->close();
    
    // --- 5. CHÈN VÀO BẢNG CHITIETDONHANG & CẬP NHẬT SỐ LƯỢNG ĐÃ BÁN ---
    $sql_ctdh = "INSERT INTO ChiTietDonHang (MaDH, MaSP, SoLuong, DonGia) VALUES (?, ?, ?, ?)";
    $stmt_ctdh = $conn->prepare($sql_ctdh);

    // SQL cập nhật: Tăng DaBan và Giảm SoLuongTon
    $sql_update_kho = "UPDATE SanPham SET DaBan = DaBan + ?, SoLuongTon = SoLuongTon - ? WHERE MaSP = ?";
    $stmt_update_kho = $conn->prepare($sql_update_kho);
    
    foreach ($san_pham_chi_tiet as $item) {
        // Lưu chi tiết đơn hàng
        $stmt_ctdh->bind_param("iidi", 
            $ma_dh_moi, 
            $item['MaSP'], 
            $item['SoLuong'], 
            $item['DonGia']);
        $stmt_ctdh->execute();

        // Cập nhật số liệu Đã Bán và Tồn Kho (i i i tương ứng SoLuong, SoLuong, MaSP)
        $stmt_update_kho->bind_param("iii", $item['SoLuong'], $item['SoLuong'], $item['MaSP']);
        $stmt_update_kho->execute();
    }
    $stmt_ctdh->close();
    $stmt_update_kho->close();
    
    // --- 6. GHI THÔNG BÁO ADMIN ---
    $maND_hien_tai = $ma_nd_session ?? NULL; 
    $noiDungThongBao = ($maND_hien_tai === NULL) 
        ? "Khách hàng (Vãng lai) vừa đặt đơn hàng mới: MaDH $ma_dh_moi." 
        : "Khách hàng (MaND: $maND_hien_tai) vừa đặt đơn hàng mới: MaDH $ma_dh_moi.";

    $loaiThongBao = 'order';
    $sql_insert_notif = "INSERT INTO ThongBaoAdmin (LoaiThongBao, MaND, NoiDung) VALUES (?, ?, ?)";
    $stmt_notif = $conn->prepare($sql_insert_notif);

    if ($stmt_notif) {
        $stmt_notif->bind_param("sis", $loaiThongBao, $maND_hien_tai, $noiDungThongBao);
        $stmt_notif->execute();
        $stmt_notif->close();
    }
    
    // HOÀN TẤT TRANSACTION
    $conn->commit();
    unset($_SESSION['cart']); 
    $thanh_cong = true;

} catch (Exception $e) {
    $conn->rollback();
    $error_message = $e->getMessage() . " | MySQL Error: " . $conn->error; 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết Quả Đặt Hàng</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        .result-box {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .result-box h1 { margin-bottom: 20px; }
        .result-box p { color: #ddd; margin-bottom: 15px; }
        .add-to-cart { padding: 10px 20px; text-decoration: none; border-radius: 5px; color: white; }
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
            </nav>
        </div>
    </header>

    <div class="result-box">
        <?php 
        $ngay_dat_hang = date('ymd');
        $ma_dh_so = str_pad($ma_dh_moi, 5, '0', STR_PAD_LEFT);
        $ma_don_hang_hien_thi = "DH-" . $ngay_dat_hang . "-" . $ma_dh_so; 
        ?>

        <?php if ($thanh_cong): ?>
            <h1 style="color: #4CAF50;">✅ Đặt hàng thành công!</h1>
            <p style="font-size: 1.2em;">Mã Đơn hàng: <strong><?php echo $ma_don_hang_hien_thi; ?></strong></p>
            <p>Số lượng sản phẩm đã bán và kho hàng đã được cập nhật tự động.</p>
            <script>setTimeout(function(){ window.location.href = 'lich_su_don_hang.php'; }, 3000);</script>
        <?php else: ?>
            <h1 style="color: #cc0000;">❌ Đặt hàng thất bại!</h1>
            <p>Lỗi: <strong><?php echo htmlspecialchars($error_message); ?></strong></p>
            <p><a href="gio_hang.php" class="add-to-cart" style="background-color: #f44336;">Quay lại Giỏ hàng</a></p>
        <?php endif; ?>
    </div>
</body>
</html>