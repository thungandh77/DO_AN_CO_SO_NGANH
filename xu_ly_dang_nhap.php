<?php
// Bắt đầu session để lưu trữ thông tin người dùng
session_start();
// Bao gồm file kết nối CSDL
include 'db_connect.php';

// Kiểm tra xem dữ liệu POST đã được gửi đi chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Lấy và làm sạch dữ liệu từ form
    $tenDangNhap = $_POST['TenDangNhap'] ?? '';
    $matKhau = $_POST['MatKhau'] ?? '';
    
    // Kiểm tra xem trường nào bị bỏ trống
    if (empty($tenDangNhap) || empty($matKhau)) {
        $_SESSION['error_message'] = "Vui lòng điền đầy đủ Tên đăng nhập và Mật khẩu.";
        header("Location: dang_nhap.php");
        exit;
    }

    // 2. Chuẩn bị truy vấn bằng Prepared Statement
    // Chỉ lấy những thông tin cần thiết: Mã người dùng, Mật khẩu đã hash, Loại người dùng, và Họ Tên
    $sql = "SELECT MaND, MatKhau, LoaiND, HoTen FROM NguoiDung WHERE TenDangNhap = ?";
    
    // Kiểm tra kết nối
    if (!$conn) {
        $_SESSION['error_message'] = "Lỗi kết nối CSDL.";
        header("Location: dang_nhap.php");
        exit;
    }
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Gắn tham số (s: string)
        $stmt->bind_param("s", $tenDangNhap);
        
        // Thực thi truy vấn
        $stmt->execute();
        
        // Lấy kết quả
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // 3. XÁC THỰC MẬT KHẨU BẰNG password_verify (Quan trọng nhất)
            // So sánh mật khẩu người dùng nhập ($matKhau) với chuỗi hash trong CSDL ($user['MatKhau'])
            if (password_verify($matKhau, $user['MatKhau'])) {
                
                // Đăng nhập thành công, thiết lập các biến session
                $_SESSION['MaND'] = $user['MaND'];
                $_SESSION['TenDangNhap'] = $tenDangNhap;
                $_SESSION['HoTen'] = $user['HoTen']; // Lưu Tên để hiển thị
                $_SESSION['LoaiND'] = $user['LoaiND']; // Lưu LoạiND (Admin/KhachHang)

                // 4. Chuyển hướng người dùng
                
                if ($user['LoaiND'] == 'Admin') {
                    // Chuyển hướng Admin về trang thống kê hoặc trang chủ
                    header("Location: thong_ke_admin.php");
                } else {
                    // Chuyển hướng Khách hàng về trang chủ
                    header("Location: index.php");
                }
                exit;

            } else {
                // Mật khẩu không khớp
                $_SESSION['error_message'] = "Tên đăng nhập hoặc Mật khẩu không đúng.";
                header("Location: dang_nhap.php");
                exit;
            }
        } else {
            // Không tìm thấy người dùng
            $_SESSION['error_message'] = "Tên đăng nhập hoặc Mật khẩu không đúng.";
            header("Location: dang_nhap.php");
            exit;
        }

        $stmt->close();
    } else {
        // Lỗi chuẩn bị truy vấn
        $_SESSION['error_message'] = "Đã xảy ra lỗi hệ thống khi đăng nhập. Vui lòng thử lại sau.";
        header("Location: dang_nhap.php");
        exit;
    }
    
    $conn->close();

} else {
    // Nếu truy cập trực tiếp file này mà không qua POST form
    header("Location: dang_nhap.php");
    exit;
}
?>