<?php
session_start();
include 'db_connect.php'; 

// 1. Kiểm tra giỏ hàng và dữ liệu POST
if (!isset($_SESSION['cart']) || empty($_POST['sl'])) {
    // Nếu giỏ hàng trống hoặc không có số lượng nào được gửi, chuyển hướng về giỏ hàng
    // Đặt thông báo lỗi nếu cần
    $_SESSION['message'] = [
        'type' => 'error', 
        'text' => 'Không có dữ liệu giỏ hàng để cập nhật.'
    ];
    header("Location: gio_hang.php");
    exit();
}

$so_luong_cap_nhat = $_POST['sl'];
$gio_hang = &$_SESSION['cart']; 
$ma_sp_can_giu_lai = [];
$da_cap_nhat = false; // Biến cờ kiểm tra xem có thay đổi nào xảy ra không

// 2. Duyệt và Cập nhật số lượng
foreach ($so_luong_cap_nhat as $ma_sp => $so_luong_moi) {
    $ma_sp = (int)$ma_sp;
    $so_luong_moi = (int)$so_luong_moi;

    if (isset($gio_hang[$ma_sp])) {
        
        if ($so_luong_moi > 0) {
            // Kiểm tra nếu số lượng thực sự thay đổi
            if ($gio_hang[$ma_sp]['soluong'] !== $so_luong_moi) {
                $gio_hang[$ma_sp]['soluong'] = $so_luong_moi;
                $da_cap_nhat = true;
            }
            $ma_sp_can_giu_lai[] = $ma_sp;
            
        } else {
            // Nếu số lượng mới là 0 hoặc âm, đánh dấu là đã xóa (sẽ xử lý ở bước 3)
            $da_cap_nhat = true;
        }
    }
}

// 3. Xóa các sản phẩm đã bị xóa hoặc có số lượng = 0
$gio_hang_moi = [];
$tong_so_luong_moi = 0;

foreach ($gio_hang as $ma_sp => $item) {
    if (in_array($ma_sp, $ma_sp_can_giu_lai)) {
        $gio_hang_moi[$ma_sp] = $item;
        $tong_so_luong_moi += $item['soluong'];
    } else {
        // Nếu sản phẩm không còn trong giỏ hàng mới (đã bị xóa do sl=0), đánh dấu là đã cập nhật
        $da_cap_nhat = true;
    }
}

// Ghi đè giỏ hàng cũ bằng giỏ hàng mới đã được cập nhật/xóa
$_SESSION['cart'] = $gio_hang_moi;

// --- BƯỚC QUAN TRỌNG: ĐẶT THÔNG BÁO THÀNH CÔNG ---
if ($da_cap_nhat || count($gio_hang) !== count($gio_hang_moi)) {
    // Nếu có thay đổi số lượng HOẶC số lượng sản phẩm trong giỏ bị thay đổi (bị xóa)
    $_SESSION['message'] = [
        'type' => 'success', 
        'text' => 'Giỏ hàng đã được cập nhật thành công!'
    ];
} else {
    // Nếu không có bất kỳ thay đổi nào
    $_SESSION['message'] = [
        'type' => 'info', 
        'text' => 'Không có thay đổi nào được thực hiện trên giỏ hàng.'
    ];
}


// Chuyển hướng người dùng trở lại trang Giỏ hàng
header("Location: gio_hang.php");
exit();
?>