<?php
session_start();
include 'db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['MaND'])) {
    header("Location: dang_nhap.php");
    exit();
}
$ho_ten_nguoi_dung = $_SESSION['HoTen'] ?? $_SESSION['TenDangNhap'];

$tong_tien_thanh_toan = 0;
$gio_hang = $_SESSION['cart'] ?? [];

// Nếu giỏ hàng trống, chuyển hướng về giỏ hàng
if (empty($gio_hang)) {
    header("Location: gio_hang.php");
    exit();
}

// 1. Tính tổng tiền thực tế từ CSDL
// Lấy danh sách MaSP từ giỏ hàng để truy vấn
$ma_sp_list = array_keys($gio_hang);

if (!empty($ma_sp_list)) {
    $placeholders = implode(',', array_fill(0, count($ma_sp_list), '?'));
    
    // Sử dụng prepare statement
    $sql = "SELECT MaSP, Gia FROM SanPham WHERE MaSP IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    // TẠO MẢNG THAM CHIẾU (SỬA LỖI bind_param)
    $types = str_repeat('i', count($ma_sp_list));
    
    // Tạo mảng tham số, bao gồm chuỗi kiểu dữ liệu ở vị trí đầu tiên
    $params = [$types];
    
    // Gắn tham chiếu của từng phần tử MaSP vào mảng $params
    foreach ($ma_sp_list as &$ma_sp) {
        $params[] = &$ma_sp;
    }
    // Gán $params[0] là chuỗi kiểu dữ liệu, các phần tử còn lại là các tham số MaSP
    call_user_func_array([$stmt, 'bind_param'], $params);
    
    $stmt->execute();
    $result = $stmt->get_result();

    $gia_san_pham_db = [];
    while($row = $result->fetch_assoc()) {
        $gia_san_pham_db[$row['MaSP']] = $row['Gia'];
    }
    
    // Tính lại Tổng Tiền DỰA TRÊN GIÁ CSDL VÀ SỐ LƯỢNG SESSION
    foreach ($gio_hang as $ma_sp => $item) {
        if (isset($gia_san_pham_db[$ma_sp])) {
            $gia_sp = $gia_san_pham_db[$ma_sp];
            $so_luong = $item['soluong'];
            $tong_tien_thanh_toan += $gia_sp * $so_luong;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Thanh Toán</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <header class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo">🐻 BONG STORE</a>
            <nav class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="gio_hang.php">Giỏ Hàng</a>
                <a href="lich_su_don_hang.php">Lịch Sử Đơn Hàng</a>
                
                <?php if (isset($_SESSION['MaND'])): ?>
                    <a href="#" style="color: #FFC107;">Xin chào, <?php echo htmlspecialchars($ho_ten_nguoi_dung); ?></a>
                    <a href="xu_ly_dang_xuat.php" class="logout-btn">Đăng Xuất</a>
                <?php else: ?>
                    <a href="dang_nhap.php">Đăng Nhập</a>
                    <a href="dang_ky.php">Đăng Ký</a>
                <?php endif; ?>

            </nav>
        </div>
    </header>

    <div class="content-container" style="max-width: 600px; margin-top: 50px;">
        <h2>XÁC NHẬN THANH TOÁN</h2>
        
        <?php if ($tong_tien_thanh_toan > 0): ?>
            <div style="background-color: #f7fff7; border: 1px solid #d4edda; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <p style="font-size: 1.5em; font-weight: bold; color: #198754;">
                    Tổng tiền cần thanh toán: <span class="price" style="font-size: 1.1em;"><?php echo number_format($tong_tien_thanh_toan, 0, ',', '.'); ?> VNĐ</span>
                </p>
                
                <form action="xu_ly_thanh_toan.php" method="post">
                    <input type="hidden" name="tong_tien_gui" value="<?php echo $tong_tien_thanh_toan; ?>">
                    
                    <div style="margin-bottom: 15px;">
                        <label for="ho_ten" style="display: block; font-weight: bold; margin-bottom: 5px;">Họ và Tên người nhận:</label>
                        <input type="text" id="ho_ten" name="ho_ten" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;" 
                               value="<?php echo htmlspecialchars($ho_ten_nguoi_dung); ?>">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="sdt" style="display: block; font-weight: bold; margin-bottom: 5px;">Số Điện Thoại:</label>
                        <input type="text" id="sdt" name="sdt" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label for="dia_chi" style="display: block; font-weight: bold; margin-bottom: 5px;">Địa chỉ nhận hàng:</label>
                        <textarea id="dia_chi" name="dia_chi" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"></textarea>
                    </div>

                    <button type="submit" class="add-to-cart" style="width: 100%; background-color: #2f855a; font-size: 1.2em;">HOÀN TẤT ĐẶT HÀNG</button>
                </form>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <p style="font-size: 1.2em; color: #cc0000;">Giỏ hàng của bạn đang trống. Vui lòng quay lại <a href="index.php">Trang chủ</a> để chọn sản phẩm.</p>
                <p><a href="gio_hang.php">Quay lại Giỏ hàng</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>