<?php
session_start();
include 'db_connect.php';

// 1. Kiểm tra quyền Admin và phương thức POST
if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] !== 'Admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// 2. Lấy dữ liệu từ POST
$ma_sp = isset($_POST['masp']) ? (int)$_POST['masp'] : 0; 
$ten_sp = $_POST['tensp'];
$gia = (float)$_POST['gia'];
$soluongton = (int)$_POST['soluongton'];
$mota = $_POST['mota'];
$madm = (int)$_POST['madm'];
$daban = (int)$_POST['daban'];
$kichthuoc = $_POST['kichthuoc'];
$is_free_gift = isset($_POST['is_free_gift']) ? 1 : 0;
$is_ship_fast = isset($_POST['is_ship_fast']) ? 1 : 0;

$is_update = ($ma_sp > 0);
$hinh_anh = null; 
$target_dir = "hinh_anh/";
$upload_ok = true;

// 3. Xử lý upload ảnh
if (isset($_FILES["hinhmoi"]) && $_FILES["hinhmoi"]["error"] == 0) {
    $file_name = basename($_FILES["hinhmoi"]["name"]);
    $image_file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Tạo tên file duy nhất tránh trùng lặp
    $unique_file_name = time() . '_' . uniqid() . '.' . $image_file_type;
    $hinh_anh = $unique_file_name;
    
    if (!move_uploaded_file($_FILES["hinhmoi"]["tmp_name"], $target_dir . $hinh_anh)) {
        $_SESSION['form_message'] = "Lỗi: Không thể tải ảnh lên server.";
        $_SESSION['form_message_type'] = "error";
        $upload_ok = false;
    }
} else {
    // Nếu là cập nhật và không có ảnh mới, giữ ảnh cũ trong CSDL
    if ($is_update) {
        $stmt_old_img = $conn->prepare("SELECT HinhAnh FROM SanPham WHERE MaSP = ?");
        $stmt_old_img->bind_param("i", $ma_sp);
        $stmt_old_img->execute();
        $result_old_img = $stmt_old_img->get_result();
        $old_img_row = $result_old_img->fetch_assoc();
        $hinh_anh = $old_img_row['HinhAnh'] ?? 'default.jpg';
        $stmt_old_img->close();
    } else {
        // Nếu là thêm mới mà không có ảnh, dùng ảnh mặc định
        $hinh_anh = 'default.jpg';
    }
}

// 4. Lưu vào Cơ sở dữ liệu
if ($upload_ok) {
    if ($is_update) {
        // --- TRƯỜNG HỢP UPDATE (CẬP NHẬT) ---
        $sql = "UPDATE SanPham SET TenSP=?, Gia=?, SoLuongTon=?, MoTa=?, HinhAnh=?, MaDM=?, DaBan=?, KichThuoc=?, is_free_gift=?, is_ship_fast=? WHERE MaSP=?";
        $stmt = $conn->prepare($sql);
        
        // CHUỖI ĐỊNH NGHĨA (11 ký tự): s d i s s i i s i i i
        // 1.TenSP(s), 2.Gia(d), 3.SoLuongTon(i), 4.MoTa(s), 5.HinhAnh(s), 6.MaDM(i), 
        // 7.DaBan(i), 8.KichThuoc(s), 9.is_free_gift(i), 10.is_ship_fast(i), 11.MaSP(i)
        $stmt->bind_param("sdissiisiii", $ten_sp, $gia, $soluongton, $mota, $hinh_anh, $madm, $daban, $kichthuoc, $is_free_gift, $is_ship_fast, $ma_sp);

        if ($stmt->execute()) {
            $_SESSION['form_message'] = "Cập nhật sản phẩm thành công!";
            $_SESSION['form_message_type'] = "success";
        } else {
            $_SESSION['form_message'] = "Lỗi cập nhật CSDL: " . $stmt->error;
            $_SESSION['form_message_type'] = "error";
        }
        $stmt->close();
        header("Location: them_san_pham.php?masp=" . $ma_sp);
        
    } else {
        // --- TRƯỜNG HỢP INSERT (THÊM MỚI) ---
        $sql = "INSERT INTO SanPham (TenSP, Gia, SoLuongTon, MoTa, HinhAnh, MaDM, DaBan, KichThuoc, is_free_gift, is_ship_fast) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // CHUỖI ĐỊNH NGHĨA (10 ký tự): s d i s s i i s i i
        $stmt->bind_param("sdissiisii", $ten_sp, $gia, $soluongton, $mota, $hinh_anh, $madm, $daban, $kichthuoc, $is_free_gift, $is_ship_fast);

        if ($stmt->execute()) {
            $_SESSION['form_message'] = "Thêm sản phẩm mới thành công!";
            $_SESSION['form_message_type'] = "success";
            header('Location: danh_sach_san_pham.php');
            exit();
        } else {
            $_SESSION['form_message'] = "Lỗi thêm mới CSDL: " . $stmt->error;
            $_SESSION['form_message_type'] = "error";
            header('Location: them_san_pham.php');
            exit();
        }
        $stmt->close();
    }
} else {
    // Nếu có lỗi upload ảnh
    $redirect_url = $is_update ? "them_san_pham.php?masp=" . $ma_sp : "them_san_pham.php"; 
    header("Location: " . $redirect_url);
}

$conn->close();
?>