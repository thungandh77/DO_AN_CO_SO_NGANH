<?php
session_start();
// Đảm bảo file db_connect.php không có ký tự lạ nào.
include 'db_connect.php'; 

$message = '';

// --- 1. KIỂM TRA VÀ XÁC THỰC MA_ND ---
if (!isset($_SESSION['MaND']) || empty($_SESSION['MaND'])) {
    $message = '<div class="message-box error">LỖI: Bạn chưa đăng nhập hoặc Mã người dùng (MaND) không được lưu vào session. Vui lòng kiểm tra file xử lý đăng nhập.</div>';
    $MaND = 0; 
} else {
    $MaND = $_SESSION['MaND'];
}

// --- 2. XỬ LÝ CẬP NHẬT thông tin (POST Request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && $MaND != 0) {
    
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $dien_thoai = trim($_POST['dien_thoai'] ?? '');
    $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
    $mat_khau_moi_hash = null;

    if (empty($ho_ten)) {
        $message = '<div class="message-box error">Họ Tên không được để trống.</div>';
    } else {
        
        if (!empty($mat_khau_moi)) {
            $mat_khau_moi_hash = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
        }

        // Truy vấn UPDATE sử dụng tên cột chính xác: HoTen, DienThoai, MatKhau, MaND
        $sql_update = "UPDATE NguoiDung SET HoTen = ?, DienThoai = ?";
        $param_types = "ss"; 
        $param_values = [$ho_ten, $dien_thoai];

        if ($mat_khau_moi_hash) {
            $sql_update .= ", MatKhau = ?";
            $param_types .= "s";
            $param_values[] = $mat_khau_moi_hash;
        }

        $sql_update .= " WHERE MaND = ?";
        $param_types .= "i"; 
        $param_values[] = $MaND;

        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_update === false) {
            $message = '<div class="message-box error">LỖI PREPARE SQL: Lỗi chi tiết: ' . $conn->error . '</div>';
        } else {
            
            // Xử lý bind_param bằng tham chiếu
            $bind_params = array_merge([$param_types], $param_values);
            $refs = [];
            foreach($bind_params as $key => $value) {
                $refs[$key] = &$bind_params[$key];
            }
            
            if (!call_user_func_array([$stmt_update, 'bind_param'], $refs)) {
                $message = '<div class="message-box error">LỖI BIND_PARAM: Kiểm tra lại kiểu dữ liệu.</div>';
            } elseif ($stmt_update->execute()) {
                
                if ($stmt_update->affected_rows > 0) {
                    $message = '<div class="message-box success">Cập nhật thông tin thành công!</div>';
                    $_SESSION['HoTen'] = $ho_ten; 
                } else {
                    $message = '<div class="message-box info">Dữ liệu không có thay đổi.</div>';
                }
                
            } else {
                $message = '<div class="message-box error">LỖI EXECUTE: Không thể thực thi cập nhật. Lỗi chi tiết: ' . $stmt_update->error . '</div>';
            }

            $stmt_update->close();
        }
    }
}

// --- 3. Lấy thông tin tài khoản hiện tại để hiển thị ---
$user = null;
if ($MaND != 0) {
    $sql_user = "SELECT TenDangNhap, Email, HoTen, DienThoai FROM NguoiDung WHERE MaND = ?";
    $stmt_user = $conn->prepare($sql_user);

    if ($stmt_user !== false) {
        $stmt_user->bind_param("i", $MaND);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user = $result_user->fetch_assoc();
        $stmt_user->close();
    }
}

if ($MaND == 0 && empty($message)) {
     $message = '<div class="message-box error">LỖI: Mã người dùng (MaND) không hợp lệ trong session. Vui lòng đăng nhập lại.</div>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông Tin Tài Khoản - Bong Store</title>
    <link rel="stylesheet" href="style.css?v=17"> 
    <style>
        /* CSS TỐI THIỂU VÀ AN TOÀN - ĐÃ GỠ BỎ MỌI KHẢ NĂNG CHẶN NHẬP LIỆU */
        .account-form { max-width: 600px; margin: 0 auto; padding: 30px; background-color: #1e1e1e; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #ccc; }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #4CAF50; /* Viền nổi bật cho ô có thể chỉnh sửa */
            border-radius: 4px;
            background-color: #333;
            color: white;
            box-sizing: border-box;
            font-size: 16px;
        }
        .form-group input:focus {
            border-color: #FFC107; /* Hiệu ứng khi focus */
            background-color: #444;
        }
        
        .form-group input[readonly] {
            background-color: #222; /* Màu nền cho ô bị khóa */
            cursor: not-allowed;
            color: #aaa;
            border-color: #444;
        }

        .btn-submit { background-color: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; transition: background-color 0.3s; width: 100%; }
        .btn-submit:hover { background-color: #38a169; }

        .message-box { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; text-align: center; }
        .message-box.success { background-color: #4CAF50; color: white; }
        .message-box.error { background-color: #f44336; color: white; }
        .message-box.info { background-color: #03A9F4; color: white; }
    </style>
</head>
<body>
    
    <header class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo">🐻 BONG STORE</a>
            
            <nav class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="gio_hang.php">Giỏ Hàng</a>
                
                <?php
                if (isset($_SESSION['MaND'])) {
                    echo '<a href="lich_su_don_hang.php">Lịch Sử ĐH</a>';
                    if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] == 'Admin') {
                        echo '<a href="thong_ke_admin.php">Thống Kê</a>'; 
                        echo '<a href="danh_sach_san_pham.php">Quản Lý SP</a>';
                    }
                }
                ?>
            </nav>

            <div class="user-controls">
                <?php if ($MaND != 0): 
                    $ho_ten_session = htmlspecialchars($_SESSION['HoTen'] ?? $user['TenDangNhap'] ?? 'Khách');
                    ?>
                    <a href="thong_tin_tai_khoan.php" class="user-name-link" style="color: #FFC107 !important;">
                        Xin chào, <?php echo $ho_ten_session; ?>
                    </a>
                    <a href="xu_ly_dang_xuat.php" class="logout-btn">Đăng Xuất</a>
                <?php else: ?>
                    <a href="dang_nhap.php" class="nav-links-single">Đăng Nhập</a>
                    <a href="dang_ky.php" class="nav-links-single">Đăng Ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="content-container">
        <h2 style="color: white; border-bottom: 2px solid #4CAF50;">THÔNG TIN TÀI KHOẢN</h2>
        
        <?php echo $message; ?>

        <?php if ($user && $MaND != 0): ?>
            <form method="POST" action="thong_tin_tai_khoan.php" class="account-form">
                
                <div class="form-group">
                    <label for="ten_dang_nhap">Tên Đăng Nhập:</label>
                    <input type="text" id="ten_dang_nhap" value="<?php echo htmlspecialchars($user['TenDangNhap']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['Email'] ?? 'Chưa cung cấp'); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="ho_ten">Họ Tên:</label>
                    <input type="text" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($user['HoTen']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="dien_thoai">Điện Thoại:</label>
                    <input type="text" id="dien_thoai" name="dien_thoai" value="<?php echo htmlspecialchars($user['DienThoai']); ?>">
                </div>

                <hr style="border-color: #444; margin: 30px 0;">

                <div class="form-group">
                    <label for="mat_khau_moi">Mật Khẩu Mới (Bỏ trống nếu không muốn đổi):</label>
                    <input type="password" id="mat_khau_moi" name="mat_khau_moi" placeholder="********">
                </div>

                <button type="submit" class="btn-submit">CẬP NHẬT THÔNG TIN</button>
            </form>
        <?php else: ?>
            <div class="message-box info">Vui lòng đăng nhập để xem thông tin tài khoản.</div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="back-to-home">Quay lại Trang Chủ</a>
        </div>
    </div>
    
</body>
</html>