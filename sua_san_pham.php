<?php
session_start();
include 'db_connect.php';

// Lấy mã sản phẩm từ URL (sử dụng 'masp' theo cấu trúc link mới của bạn)
$ma_sp = isset($_GET['masp']) ? (int)$_GET['masp'] : 0;

if ($ma_sp <= 0) {
    header('Location: danh_sach_san_pham.php');
    exit();
}

// 1. Lấy tất cả danh mục từ bảng để điền vào ô chọn
// Lưu ý: Hãy kiểm tra tên bảng danh mục của bạn là 'danhmuc' hay 'danhmuclist'
$sql_dm = "SELECT * FROM DanhMuc ORDER BY TenDM ASC"; 
$result_dm = $conn->query($sql_dm);

// 2. Lấy thông tin hiện tại của sản phẩm
$stmt = $conn->prepare("SELECT * FROM SanPham WHERE MaSP = ?");
$stmt->bind_param("i", $ma_sp);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) { die("Sản phẩm không tồn tại!"); }

// 3. Xử lý khi nhấn nút LƯU THAY ĐỔI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = $_POST['tensp'];
    $gia = $_POST['gia'];
    $kho = $_POST['soluong'];
    $kichthuoc = $_POST['kichthuoc'];
    $mota = $_POST['mota'];
    $madm = $_POST['madm']; // Lấy ID danh mục người dùng chọn
    $is_gift = isset($_POST['is_free_gift']) ? 1 : 0;
    $is_ship = isset($_POST['is_ship_fast']) ? 1 : 0;
    
    // Giữ ảnh cũ nếu không chọn ảnh mới
    $hinh_anh = $product['HinhAnh']; 
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . "_" . $_FILES['hinh_anh']['name'];
        if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], "hinh_anh/" . $file_name)) {
            $hinh_anh = $file_name;
        }
    }

    // Cập nhật Database (Thêm trường MaDM)
    $sql_update = "UPDATE SanPham SET TenSP=?, Gia=?, SoLuongTon=?, KichThuoc=?, MoTa=?, HinhAnh=?, MaDM=?, is_free_gift=?, is_ship_fast=? WHERE MaSP=?";
    $stmt_up = $conn->prepare($sql_update);
    $stmt_up->bind_param("sdisssiiii", $ten, $gia, $kho, $kichthuoc, $mota, $hinh_anh, $madm, $is_gift, $is_ship, $ma_sp);

    if ($stmt_up->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='danh_sach_san_pham.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm - Bong Store</title>
    <style>
        body { background: #121212; color: white; font-family: sans-serif; display: flex; justify-content: center; padding: 40px 0; }
        .form-box { background: #1e1e1e; padding: 30px; border-radius: 10px; width: 500px; border-top: 5px solid #4CAF50; }
        h2 { color: #4CAF50; text-align: center; margin-bottom: 25px; text-transform: uppercase; }
        label { display: block; margin: 15px 0 5px; color: #aaa; font-size: 14px; }
        input, textarea, select { width: 100%; padding: 12px; background: #2a2a2a; border: 1px solid #444; color: white; border-radius: 5px; box-sizing: border-box; }
        select { cursor: pointer; }
        .checkbox-group { display: flex; gap: 20px; margin: 25px 0; padding: 10px; background: #252525; border-radius: 5px; }
        .btn-submit { background: #4CAF50; color: white; border: none; width: 100%; padding: 14px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .btn-submit:hover { background: #45a049; }
        .img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; border: 1px solid #444; }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Sửa Sản Phẩm</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Tên Sản Phẩm:</label>
        <input type="text" name="tensp" value="<?= htmlspecialchars($product['TenSP']) ?>" required>

        <label>Danh Mục:</label>
        <select name="madm" required>
            <option value="">-- Chọn Danh Mục --</option>
            <?php while($dm = $result_dm->fetch_assoc()): ?>
                <option value="<?= $dm['MaDM'] ?>" <?= ($dm['MaDM'] == $product['MaDM']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dm['TenDM']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Giá (VNĐ):</label>
        <input type="number" name="gia" value="<?= $product['Gia'] ?>" required>

        <label>Số Lượng Tồn:</label>
        <input type="number" name="soluong" value="<?= $product['SoLuongTon'] ?>" required>

        <label>Kích Thước:</label>
        <input type="text" name="kichthuoc" value="<?= htmlspecialchars($product['KichThuoc']) ?>">

        <label>Mô Tả:</label>
        <textarea name="mota" rows="4"><?= htmlspecialchars($product['MoTa']) ?></textarea>

        <label>Ảnh Hiện Tại:</label>
        <img src="hinh_anh/<?= $product['HinhAnh'] ?>" class="img-preview" onerror="this.src='hinh_anh/default.png'">
        <input type="file" name="hinh_anh">

        <div class="checkbox-group">
            <label><input type="checkbox" name="is_free_gift" <?= $product['is_free_gift'] ? 'checked' : '' ?>> 🎁 Gói Quà</label>
            <label><input type="checkbox" name="is_ship_fast" <?= $product['is_ship_fast'] ? 'checked' : '' ?>> 🚀 Giao Nhanh</label>
        </div>

        <button type="submit" class="btn-submit">LƯU THAY ĐỔI</button>
        <p style="text-align: center;"><a href="danh_sach_san_pham.php" style="color: #777; text-decoration: none; font-size: 13px; margin-top: 15px; display: inline-block;">Hủy bỏ</a></p>
    </form>
</div>
   <a href="index.php" class="back-to-home">🏠 Quay lại Trang Chủ</a>
</body>
</html>