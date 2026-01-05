<?php
include 'db_connect.php';
// Đếm số thông báo chưa xem từ bảng ThongBaoAdmin bạn vừa tạo
$sql_count = "SELECT COUNT(*) as total FROM ThongBaoAdmin WHERE DaXem = 0";
$result_count = $conn->query($sql_count);
$unread = 0;
if ($result_count) {
    $row_count = $result_count->fetch_assoc();
    $unread = $row_count['total'];
}
?>
<div class="admin-sidebar" style="background: #2a2a2a; color: white; padding: 20px;">
    <h3>BONG STORE ADMIN</h3>
    <ul style="list-style: none; padding: 0;">
        <li><a href="admin_thong_ke.php" style="color: white;">Thống kê doanh thu</a></li>
        <li><a href="danh_sach_san_pham.php" style="color: white;">Quản lý sản phẩm</a></li>
        <li>
            <a href="admin_notifications.php" style="color: white; position: relative;">
                Thông báo mới
                <?php if($unread > 0): ?>
                    <span style="background: red; color: white; border-radius: 50%; padding: 2px 8px; font-size: 12px; margin-left: 5px;">
                        <?php echo $unread; ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
        <li><a href="dang_xuat.php" style="color: #ff4d4d;">Đăng xuất</a></li>
    </ul>
</div>