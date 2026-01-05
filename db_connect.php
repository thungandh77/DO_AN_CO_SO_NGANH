<?php
$servername = "localhost"; // Máy chủ của XAMPP
$username = "root";      // Tên người dùng mặc định của XAMPP
$password = "";          // Mật khẩu mặc định là rỗng
$dbname = "quanlybanhang"; // Tên Schema bạn vừa tạo

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// BƯỚC BỔ SUNG: Thiết lập bộ ký tự thành UTF-8 cho kết nối
// Đảm bảo dữ liệu tiếng Việt được xử lý và hiển thị đúng
$conn->set_charset("utf8");

// KHÔNG có session_start() ở đây, lệnh này nên ở đầu các file cần dùng session (index.php, xu_ly_dang_nhap.php,...)
?>