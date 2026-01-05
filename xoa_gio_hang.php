<?php
session_start();

// 1. Kiểm tra xem Mã Sản Phẩm có được truyền qua tham số GET không
if (isset($_GET['masp'])) {
    
    // 2. Lấy Mã Sản Phẩm và đảm bảo nó là số nguyên
    $ma_sp_can_xoa = (int)$_GET['masp'];

    // 3. Kiểm tra xem giỏ hàng (session) có tồn tại không
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        
        // 4. Kiểm tra xem Sản phẩm cần xóa có trong giỏ hàng không
        if (isset($_SESSION['cart'][$ma_sp_can_xoa])) {
            
            // THỰC HIỆN XÓA SẢN PHẨM KHỎI SESSION GIỎ HÀNG
            unset($_SESSION['cart'][$ma_sp_can_xoa]);
            
            // Đặt thông báo thành công
            $_SESSION['message'] = [
                'type' => 'success', 
                'text' => 'Sản phẩm đã được xóa khỏi giỏ hàng.'
            ];

        } else {
            // Đặt thông báo lỗi nếu Sản phẩm không tồn tại trong giỏ
            $_SESSION['message'] = [
                'type' => 'error', 
                'text' => 'Lỗi: Sản phẩm này không có trong giỏ hàng.'
            ];
        }

    } else {
        // Đặt thông báo lỗi nếu Giỏ hàng trống
        $_SESSION['message'] = [
            'type' => 'info', 
            'text' => 'Giỏ hàng của bạn đã trống.'
        ];
    }
} else {
    // Đặt thông báo lỗi nếu thiếu Mã Sản Phẩm
    $_SESSION['message'] = [
        'type' => 'error', 
        'text' => 'Lỗi: Thiếu Mã Sản Phẩm để xóa.'
    ];
}

// 5. Chuyển hướng người dùng trở lại trang Giỏ hàng
header("Location: gio_hang.php");
exit();
?>