<?php
session_start();
include 'db_connect.php'; 

// Đảm bảo $gio_hang luôn là mảng để tránh lỗi
$gio_hang = $_SESSION['cart'] ?? [];
$tong_tien = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng Của Bạn</title>
    <link rel="stylesheet" href="style.css?v=5">
</head>
<body>
    
    <header class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo">🐻 BONG STORE</a>
            <nav class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="gio_hang.php" style="font-weight: bold;">Giỏ Hàng</a>
                <a href="lich_su_don_hang.php">Lịch Sử Đơn Hàng</a>
                <a href="danh_sach_san_pham.php">Quản Lý Sản Phẩm</a> 
            </nav>
        </div>
    </header>

    <div class="content-container">
        
        <?php
        if (isset($_SESSION['message'])): 
            $message = $_SESSION['message'];
            // Thiết lập class CSS dựa trên loại thông báo (success, error, info)
            $class = ($message['type'] === 'success') ? 'success' : (($message['type'] === 'error') ? 'error' : 'info');
        ?>
            <div class="message-box <?php echo $class; ?>">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php
            // QUAN TRỌNG: Xóa thông báo khỏi Session sau khi hiển thị để nó không hiện lại lần sau
            unset($_SESSION['message']);
        endif;
        ?>
        <h2>GIỎ HÀNG CỦA BẠN</h2>
        
        <?php if (empty($gio_hang)): ?>
            <p style="text-align: center;">Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>
            <form action="xu_ly_cap_nhat_gio_hang.php" method="post">
                <table>
                    <thead>
                        <tr>
                            <th>Ảnh</th> 
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($gio_hang as $ma_sp => $item): 
                            $ten_sp = $item['tensp'] ?? 'Sản phẩm không rõ';
                            $gia_sp = $item['gia'] ?? 0;
                            $so_luong = $item['soluong'] ?? 1;
                            $hinh_anh = $item['hinhanh'] ?? 'default.jpg';
                            
                            $thanh_tien = $gia_sp * $so_luong;
                            $tong_tien += $thanh_tien;
                        ?>
                        <tr>
                            <td>
                                <img src="hinh_anh/<?php echo htmlspecialchars($hinh_anh); ?>" 
                                    alt="<?php echo htmlspecialchars($ten_sp); ?>"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><?php echo htmlspecialchars($ten_sp); ?></td>
                            <td><?php echo number_format($gia_sp, 0, ',', '.'); ?> VNĐ</td>
                            <td><input type="number" name="sl[<?php echo $ma_sp; ?>]" value="<?php echo $so_luong; ?>" min="0" style="width: 60px;"></td>
                            <td><?php echo number_format($thanh_tien, 0, ',', '.'); ?> VNĐ</td>
                            <td><a href="xoa_gio_hang.php?masp=<?php echo $ma_sp; ?>">Xóa</a></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr style="background-color: #3b3b3b; font-weight: bold;">
                            <td colspan="4" style="text-align: right;">Tổng tiền:</td>
                            <td><?php echo number_format($tong_tien, 0, ',', '.'); ?> VNĐ</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="add-to-cart" style="width: 200px; display: inline-block;">Cập Nhật Giỏ Hàng</button>
                    <a href="thanh_toan.php" class="add-to-cart" style="width: 200px; margin-left: 10px; background-color: #2f855a; display: inline-block;">Tiến Hành Thanh Toán</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center;">
        <a href="index.php" class="back-to-home">🏠 Quay lại Trang Chủ</a>
    </div>


    
</body>
</html>