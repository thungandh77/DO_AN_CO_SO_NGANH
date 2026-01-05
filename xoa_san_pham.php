<?php
session_start();
include 'db_connect.php'; 

// Lấy mã sản phẩm
$ma_sp = $_GET['masp'] ?? $_GET['id'] ?? null;

if ($ma_sp && is_numeric($ma_sp)) {
    // Thay vì DELETE (bị lỗi khóa ngoại), ta dùng UPDATE để ẩn sản phẩm
    // Đặt SoLuongTon = -99 để đánh dấu là sản phẩm đã bị xóa
    $sql_hide = "UPDATE SanPham SET SoLuongTon = -99 WHERE MaSP = ?";
    $stmt_hide = $conn->prepare($sql_hide);
    $stmt_hide->bind_param("i", $ma_sp);
    
    if ($stmt_hide->execute()) {
        // Thành công thì quay về trang danh sách
        header("Location: danh_sach_san_pham.php");
        exit();
    } else {
        echo "Lỗi hệ thống không thể ẩn sản phẩm: " . $conn->error;
    }
} else {
    header("Location: danh_sach_san_pham.php");
}
?>