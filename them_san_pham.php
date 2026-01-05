<?php
session_start();
include 'db_connect.php';

// Chỉ cho phép Admin truy cập trang này
if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

$is_edit = isset($_GET['masp']);
$title = $is_edit ? "CẬP NHẬT SẢN PHẨM" : "THÊM SẢN PHẨM MỚI";
$sanpham = [
    'MaSP' => null,
    'TenSP' => '',
    'Gia' => '',
    'SoLuongTon' => 0,
    'MoTa' => '',
    'HinhAnh' => 'default.jpg',
    'MaDM' => '',
    // Các trường mới
    'DaBan' => 0, 
    'KichThuoc' => '',
    'is_free_gift' => 0, 
    'is_ship_fast' => 0 
];

if ($is_edit) {
    $ma_sp = (int)$_GET['masp'];
    $sql = "SELECT * FROM SanPham WHERE MaSP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ma_sp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sanpham = $result->fetch_assoc();
    } else {
        echo "<script>alert('Sản phẩm không tồn tại.'); window.location.href='danh_sach_san_pham.php';</script>";
        exit();
    }
}

// Lấy danh mục cho dropdown
$sql_dm = "SELECT MaDM, TenDM FROM DanhMuc ORDER BY TenDM";
$result_dm = $conn->query($sql_dm);
$danh_muc_list = [];
if ($result_dm) {
    while ($row = $result_dm->fetch_assoc()) {
        $danh_muc_list[] = $row;
    }
}

// Kiểm tra thông báo lỗi hoặc thành công từ quá trình xử lý (nếu có)
$message = $_SESSION['form_message'] ?? '';
$message_type = $_SESSION['form_message_type'] ?? '';
unset($_SESSION['form_message']);
unset($_SESSION['form_message_type']);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="icon" type="image/png" href="hinh_anh/LOGO.png">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>"> 
</head>
<body>
    
    <header class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo-image-link">
                <img src="hinh_anh/LOGO.png" alt="Logo Bong Store" class="header-logo-img">
                <span class="store-name-header">BONG STORE</span> 
            </a>
            <nav class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="danh_sach_san_pham.php" style="font-weight: bold;">Quản Lý SP</a>
                <a href="thong_ke_admin.php">Thống Kê</a>
            </nav>
            <div class="user-controls">
                <?php if (isset($_SESSION['MaND'])): ?>
                    <span class="user-name-link">Xin chào, Admin</span>
                    <a href="xu_ly_dang_xuat.php" class="logout-btn">Đăng Xuất</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="admin-form-container">
        <h2><?= $title ?></h2>
        
        <?php if ($message): ?>
            <div class="alert-message alert-<?= $message_type ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="xu_ly_quan_ly_san_pham.php" method="POST" enctype="multipart/form-data"> 
            
            <input type="hidden" name="masp" value="<?= htmlspecialchars($sanpham['MaSP']) ?>">
            
            <div class="form-group">
                <label for="tensp">Tên Sản Phẩm:</label>
                <input type="text" id="tensp" name="tensp" value="<?= htmlspecialchars($sanpham['TenSP']) ?>" required>
            </div>

            <div class="form-group">
                <label for="gia">Giá (VNĐ):</label>
                <input type="number" id="gia" name="gia" value="<?= htmlspecialchars($sanpham['Gia']) ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="soluongton">Số Lượng Tồn:</label>
                <input type="number" id="soluongton" name="soluongton" value="<?= htmlspecialchars($sanpham['SoLuongTon']) ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="daban">Số Lượng Đã Bán:</label>
                <input type="number" id="daban" name="daban" value="<?= htmlspecialchars($sanpham['DaBan']) ?>" min="0" required>
            </div>

            <div class="form-group">
                <label for="kichthuoc">Kích Thước:</label>
                <input type="text" id="kichthuoc" name="kichthuoc" value="<?= htmlspecialchars($sanpham['KichThuoc']) ?>" placeholder="Ví dụ: 1.2m hoặc 30cm" required>
            </div>

            <div class="form-group">
                <label for="madm">Danh Mục:</label>
                <select id="madm" name="madm" required>
                    <option value="">-- Chọn Danh Mục --</option>
                    <?php foreach ($danh_muc_list as $dm): ?>
                        <option value="<?= $dm['MaDM'] ?>" <?= ($dm['MaDM'] == $sanpham['MaDM']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dm['TenDM']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="align-items: flex-start;">
                <label for="mota">Mô Tả:</label>
                <textarea id="mota" name="mota" required><?= htmlspecialchars($sanpham['MoTa']) ?></textarea>
            </div>

            <div class="form-group checkbox-group">
                <label for="dichvu">Dịch vụ:</label>
                <div class="checkbox-container"> 
                    <div class="checkbox-item">
                        <input type="checkbox" id="free_gift" name="is_free_gift" value="1" <?= ($sanpham['is_free_gift'] == 1) ? 'checked' : '' ?>>
                        <label for="free_gift">Gói Quà Miễn Phí</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="ship_fast" name="is_ship_fast" value="1" <?= ($sanpham['is_ship_fast'] == 1) ? 'checked' : '' ?>>
                        <label for="ship_fast">Giao Hàng Nhanh</label>
                    </div>
                </div>
            </div>
            <?php if ($is_edit): ?>
                <div class="form-group image-preview-group">
                    <label>Ảnh Hiện Tại:</label>
                    <div class="image-preview" style="flex-grow: 1;">
                        <img src="hinh_anh/<?= htmlspecialchars($sanpham['HinhAnh']) ?>" alt="Ảnh Sản Phẩm">
                        <p style="font-size: 0.9em; margin-top: 5px;"><?= htmlspecialchars($sanpham['HinhAnh']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

            <div class="form-group">
                <label for="hinhmoi">Chọn Ảnh Mới:</label>
                <input type="file" id="hinhmoi" name="hinhmoi" accept="image/*" <?= $is_edit ? '' : 'required' ?>>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $is_edit ? 'Cập Nhật Sản Phẩm' : 'Thêm Sản Phẩm' ?></button>
                <a href="danh_sach_san_pham.php" class="btn-cancel">Hủy Bỏ</a>
            </div>
        </form>
    </div>
    
    </body>
</html>