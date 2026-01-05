<?php
// Bắt buộc phải có để truy cập session hiện tại
session_start(); 

// 1. Nếu session đang được sử dụng (có cookie), hủy cookie đó
// Việc này đảm bảo cookie session ID trên trình duyệt cũng bị xóa
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 2. Xóa tất cả các biến session
session_unset(); 

// 3. Hủy session trên máy chủ
session_destroy(); 

// 4. Chuyển hướng người dùng về trang chủ
header("Location: index.php"); 
exit();
?>