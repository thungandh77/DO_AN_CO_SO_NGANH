<?php
session_start();
// Bao gồm file kết nối CSDL
include 'db_connect.php'; 

$message = "";

// ----------------------------------------------------------------------
// BƯỚC FIX 1: HỦY PHIÊN CŨ KHI CÓ ĐĂNG NHẬP MỚI
// Logic quan trọng để fix lỗi xung đột khi chuyển từ User sang Admin
// ----------------------------------------------------------------------
if (isset($_SESSION['MaND']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Nếu đã có MaND (tức là đã đăng nhập) và có yêu cầu POST (đang cố gắng đăng nhập lần nữa)
    
    session_unset();    // Hủy tất cả biến session cũ
    session_destroy();  // Hủy phiên làm việc cũ
    
    // BẮT BUỘC: Khởi động lại phiên để lưu thông tin đăng nhập MỚI
    session_start(); 
}
// ----------------------------------------------------------------------


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Lấy và làm sạch dữ liệu từ POST
    $ten_dang_nhap = $conn->real_escape_string(trim($_POST['TenDangNhap'])); 
    $mat_khau_nhap = md5(trim($_POST['MatKhau'])); // Mã hóa mật khẩu đầu vào (md5)

    if (empty($ten_dang_nhap) || empty($_POST['MatKhau'])) {
        $message = "Vui lòng nhập Tên đăng nhập và Mật khẩu.";
    } else {
        // 2. TRUY VẤN XÁC THỰC
        $sql = "SELECT MaND, TenDangNhap, HoTen, LoaiND, MatKhau FROM NguoiDung WHERE TenDangNhap = '$ten_dang_nhap'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) { 
            $user = $result->fetch_assoc();
            
            // 3. SO SÁNH MẬT KHẨU
            if ($user['MatKhau'] == $mat_khau_nhap) {
                // Đăng nhập thành công!
                
                // 4. TẠO SESSION MỚI
                $_SESSION['MaND'] = $user['MaND'];
                $_SESSION['TenDangNhap'] = $user['TenDangNhap'];
                $_SESSION['HoTen'] = $user['HoTen'];
                $_SESSION['LoaiND'] = $user['LoaiND'];

                // Chuyển hướng người dùng sau khi đăng nhập thành công
                if ($user['LoaiND'] == 'Admin') {
                    // Đổi tên trang nếu cần thiết (ví dụ: admin_dashboard.php -> thong_ke_admin.php)
                    header("Location: thong_ke_admin.php"); 
                } else {
                    header("Location: index.php"); 
                }
                exit();
            } else {
                $message = "Sai Mật khẩu. Vui lòng thử lại.";
            }
        } else {
            $message = "Tên đăng nhập không tồn tại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - Bong Store</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Sử dụng CSS tương tự file dang_ky.php */
        .auth-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border: 1px solid #4CAF50;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        .auth-container h2 {
            text-align: center;
            color: white;
            margin-bottom: 25px;
        }
        .message {
            color: #FFC107;
            text-align: center;
            margin-bottom: 15px;
        }
        .auth-container input[type="submit"] {
            background-color: #4CAF50;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .auth-container input[type="submit"]:hover {
            background-color: #5cb85c;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #ccc;
            text-decoration: none;
        }
        .register-link a:hover {
            color: #4CAF50;
        }
        /* CSS cho label và input */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #ddd;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            background-color: #333;
            color: white;
            border-radius: 4px;
            box-sizing: border-box; /* Đảm bảo padding không làm tăng chiều rộng */
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Đăng Nhập Hệ Thống</h2>
        
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="dang_nhap.php">
            <div class="form-group">
                <label for="TenDangNhap">Tên Đăng Nhập:</label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" required>
            </div>
            
            <div class="form-group">
                <label for="MatKhau">Mật Khẩu:</label>
                <input type="password" id="MatKhau" name="MatKhau" required>
            </div>
            
            <input type="submit" value="Đăng Nhập">
        </form>

        <p class="register-link">
            Chưa có tài khoản? <a href="dang_ky.php">Đăng ký ngay</a>
        </p>
    </div>
</body>
</html>