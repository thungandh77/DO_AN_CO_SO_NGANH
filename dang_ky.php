<?php
// Bắt đầu session nếu cần, nhưng không cần thiết cho trang đăng ký
session_start();
// Bao gồm file kết nối CSDL
include 'db_connect.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Lấy dữ liệu từ POST
    $ten_dang_nhap = trim($_POST['TenDangNhap']);
    $mat_khau = md5(trim($_POST['MatKhau'])); // Mã hóa MD5 (Cần nâng cấp lên PASSWORD_HASH trong thực tế)
    $ho_ten = trim($_POST['HoTen']);
    $loai_nd = 'KhachHang'; 

    // Kiểm tra các trường bắt buộc
    if (empty($ten_dang_nhap) || empty($_POST['MatKhau']) || empty($ho_ten)) {
        $message = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // 2. KIỂM TRA TÊN ĐĂNG NHẬP ĐÃ TỒN TẠI CHƯA
        $check_sql = "SELECT TenDangNhap FROM NguoiDung WHERE TenDangNhap = '$ten_dang_nhap'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            // Lỗi trùng lặp
            $message = "Tên đăng nhập đã được sử dụng. Vui lòng chọn tên khác.";
        } else {
            // 3. THỰC HIỆN ĐĂNG KÝ
            $insert_sql = "INSERT INTO NguoiDung (TenDangNhap, MatKhau, HoTen, LoaiND) 
                           VALUES ('$ten_dang_nhap', '$mat_khau', '$ho_ten', '$loai_nd')";
            
            if (mysqli_query($conn, $insert_sql)) {
                $message = "Đăng ký thành công! Bạn có thể <a href='dang_nhap.php'>Đăng nhập</a> ngay bây giờ.";
                // Xóa biến để tránh lỗi POST lại
                $_POST = array(); 
            } else {
                $message = "Lỗi đăng ký: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký - Bong Store</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* CSS riêng cho Form Đăng ký */
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
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #ccc;
            text-decoration: none;
        }
        .login-link a:hover {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <header class="header-bar">
        </header>

    <div class="auth-container">
        <h2>Đăng Ký Tài Khoản</h2>
        
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="dang_ky.php">
            <div class="form-group">
                <label for="TenDangNhap">Tên Đăng Nhập:</label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" required>
            </div>
            
            <div class="form-group">
                <label for="MatKhau">Mật Khẩu:</label>
                <input type="password" id="MatKhau" name="MatKhau" required>
            </div>
            
            <div class="form-group">
                <label for="HoTen">Họ Tên:</label>
                <input type="text" id="HoTen" name="HoTen" required>
            </div>

            <input type="submit" value="Đăng Ký">
        </form>

        <p class="login-link">
            Bạn đã có tài khoản? <a href="dang_nhap.php">Đăng nhập</a>
        </p>
    </div>
</body>
</html>