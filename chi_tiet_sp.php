<?php
session_start();
include 'db_connect.php';

// Lấy mã sản phẩm từ trên link (URL)
$ma_sp = isset($_GET['masp']) ? (int)$_GET['masp'] : 0;

// 1. Truy vấn lấy chi tiết con gấu bông này
$sql = "SELECT sp.*, (SELECT AVG(Diem) FROM danhgia WHERE MaSP = sp.MaSP) as DiemTB 
        FROM SanPham sp WHERE MaSP = $ma_sp";
$result = $conn->query($sql);
$sp = $result->fetch_assoc();

if (!$sp) {
    die("Sản phẩm không tồn tại!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $sp['TenSP']; ?> - Bong Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container-detail { display: flex; gap: 50px; padding: 40px; color: white; background: #1a1a1a; border-radius: 15px; }
        .product-img-big { width: 400px; height: 400px; object-fit: cover; border-radius: 10px; }
        .info-detail h1 { color: #4CAF50; font-size: 32px; }
        .price-big { font-size: 28px; color: #ff3333; font-weight: bold; margin: 20px 0; }
        .detail-row { font-size: 18px; margin-bottom: 10px; }
        .rating-box { margin-top: 30px; padding: 20px; background: #222; border-radius: 10px; }
        .star-input { font-size: 20px; padding: 5px; border-radius: 5px; }
        textarea { width: 100%; padding: 10px; border-radius: 5px; margin-top: 10px; background: #333; color: white; border: 1px solid #444; }
    </style>
</head>
<body>

<div class="container-detail">
    <img src="hinh_anh/<?php echo $sp['HinhAnh']; ?>" class="product-img-big">

    <div class="info-detail">
        <h1><?php echo $sp['TenSP']; ?></h1>
        <div class="detail-row">⭐ Đánh giá: <strong><?php echo $sp['DiemTB'] ? round($sp['DiemTB'], 1) : 'Chưa có'; ?> / 5</strong></div>
        <div class="price-big"><?php echo number_format($sp['Gia'], 0, ',', '.'); ?> VNĐ</div>
        <div class="detail-row">Kích thước: <span style="color: #4CAF50;"><?php echo $sp['KichThuoc']; ?></span></div>
        <div class="detail-row">Đã bán: <strong><?php echo $sp['DaBan']; ?></strong></div>
        <div>Số lượng còn lại: <strong><?php echo $sp['SoLuongTon'] ?? $sp['SoLuong'] ?? 0; ?></strong></div>

        <div class="rating-box">
            <h3>GỬI ĐÁNH GIÁ CỦA BẠN</h3>
            <?php if(isset($_SESSION['MaND'])): ?>
                <form action="xu_ly_danh_gia.php" method="POST">
                    <input type="hidden" name="MaSP" value="<?php echo $ma_sp; ?>">
                    <label>Số sao: </label>
                    <select name="Diem" class="star-input">
                        <option value="5">5 ⭐⭐⭐⭐⭐ (Tuyệt vời)</option>
                        <option value="4">4 ⭐⭐⭐⭐ (Tốt)</option>
                        <option value="3">3 ⭐⭐⭐ (Bình thường)</option>
                        <option value="2">2 ⭐⭐ (Tệ)</option>
                        <option value="1">1 ⭐ (Rất tệ)</option>
                    </select>
                    <textarea name="NoiDung" rows="3" placeholder="Gấu bông này thế nào hả bạn?"></textarea>
                    <button type="submit" name="submit_danh_gia" style="background:#4CAF50; color:white; padding:12px 25px; border:none; margin-top:10px; border-radius:5px; cursor:pointer;">GỬI ĐÁNH GIÁ</button>
                </form>
            <?php else: ?>
                <p style="color: #ff9800;">Bạn cần <a href="dang_nhap.php" style="color:white;">Đăng nhập</a> để đánh giá sản phẩm này.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>