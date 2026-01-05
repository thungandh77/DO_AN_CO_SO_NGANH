<?php
session_start();
include 'db_connect.php';

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] !== 'Admin') {
    // Nếu không phải Admin, chuyển hướng về trang chủ
    header("Location: index.php");
    exit();
}

// 2. Chuẩn bị truy vấn UPDATE
// Đánh dấu tất cả các thông báo chưa xem (DaXem = 0) thành đã xem (DaXem = 1)
$sql = "UPDATE ThongBaoAdmin SET DaXem = TRUE WHERE DaXem = FALSE"; 

$thanh_cong = false;

if ($conn->query($sql) === TRUE) {
    $thanh_cong = true;
} else {
    // Tùy chọn: ghi log lỗi hoặc hiển thị lỗi cho Admin
    // echo "Lỗi khi cập nhật thông báo: " . $conn->error;
}

// 3. Chuyển hướng về trang chủ
if ($thanh_cong) {
    header("Location: index.php?msg=cleared");
} else {
    // Nếu có lỗi CSDL
    header("Location: index.php?error=clearfail");
}
exit();
?>