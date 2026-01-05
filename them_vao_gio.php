<?php
session_start(); // PHẢI LÀ DÒNG CODE ĐẦU TIÊN

include 'db_connect.php'; // Kết nối cơ sở dữ liệu

// 1. Lấy MaSP từ URL
$ma_sp = $_GET['masp'] ?? null; 
$so_luong = $_GET['soluong'] ?? 1; // Mặc định thêm 1 sản phẩm

// Biến cờ kiểm tra việc thêm giỏ hàng có thành công không
$gio_hang_thanh_cong = false;
$ten_sp_moi = "";

if ($ma_sp && is_numeric($ma_sp) && $so_luong > 0) {
    // 2. Truy vấn CSDL để lấy thông tin sản phẩm
    $sql = "SELECT TenSP, Gia, HinhAnh FROM SanPham WHERE MaSP = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $ma_sp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $ten_sp_moi = htmlspecialchars($product['TenSP']); // Lưu tên sản phẩm để dùng cho thông báo
            
            // 3. Khởi tạo giỏ hàng nếu chưa tồn tại
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // 4. Thêm/Cập nhật sản phẩm vào giỏ hàng
            if (isset($_SESSION['cart'][$ma_sp])) {
                $_SESSION['cart'][$ma_sp]['soluong'] += $so_luong;
            } else {
                $_SESSION['cart'][$ma_sp] = [
                    'tensp' => $ten_sp_moi,
                    'gia' => $product['Gia'],
                    'hinhanh' => $product['HinhAnh'],
                    'soluong' => $so_luong
                ];
            }
            $gio_hang_thanh_cong = true; // Đánh dấu thành công
        }
        $stmt->close();
    }
}

// ==========================================================
// 5. GHI LẠI THÔNG BÁO VÀO CSDL (CHO ADMIN)
// Logic này chỉ chạy khi việc thêm giỏ hàng ở trên thành công
// ==========================================================
if ($gio_hang_thanh_cong) {
    
    $maND_hien_tai = $_SESSION['MaND'] ?? NULL; // Lấy MaND (NULL nếu chưa đăng nhập)
    
    // Chuẩn bị nội dung thông báo
    if ($maND_hien_tai === NULL) {
        $noiDungThongBao = "Khách hàng (Chưa đăng nhập) vừa thêm **{$ten_sp_moi}** vào giỏ hàng.";
    } else {
        // Lưu MaND vào CSDL để Admin có thể truy vấn tên khách hàng
        $noiDungThongBao = "Khách hàng (MaND: {$maND_hien_tai}) vừa thêm **{$ten_sp_moi}** vào giỏ hàng.";
    }

    // Chuẩn bị truy vấn CSDL
    $sql_insert = "INSERT INTO ThongBaoAdmin (LoaiThongBao, MaND, NoiDung) VALUES ('cart', ?, ?)";

    $stmt_notif = $conn->prepare($sql_insert);
    
    if ($stmt_notif) {
        // SỬ DỤNG bind_param: 'i' cho integer (MaND), 's' cho string (NoiDung)
        // MaND có thể là NULL, trong trường hợp đó nó sẽ được bind dưới dạng integer NULL
        $stmt_notif->bind_param("is", $maND_hien_tai, $noiDungThongBao);
        $stmt_notif->execute();
        $stmt_notif->close();
    }
}
// ==========================================================

// Chuyển hướng về trang Giỏ hàng
header("Location: gio_hang.php");
exit();
?>