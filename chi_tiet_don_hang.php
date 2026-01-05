<?php
include 'db_connect.php';

$id = $_GET['id'];

// 1. Lấy thông tin chi tiết sản phẩm
$sql_sp = "SELECT * FROM SanPham WHERE MaSP = $id";
$res_sp = $conn->query($sql_sp);
$sp = $res_sp->fetch_assoc();

// 2. Lấy danh sách đánh giá của khách hàng
$sql_dg = "SELECT dg.*, nd.HoTen 
           FROM DanhGia dg 
           JOIN NguoiDung nd ON dg.MaND = nd.MaND 
           WHERE dg.MaSP = $id ORDER BY ThoiGian DESC";
$res_dg = $conn->query($sql_dg);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $sp['TenSP']; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .service-badge { background: #ecf0f1; padding: 5px; border-radius: 5px; margin-right: 5px; font-size: 12px; }
        .review-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .stars { color: #f1c40f; }
    </style>
</head>
<body>
    <div class="product-detail">
        <div class="product-image">
            <img src="images/<?php echo $sp['HinhAnh']; ?>" width="300">
        </div>
        
        <div class="product-info">
            <h1><?php echo $sp['TenSP']; ?></h1>
            <p class="price">Giá: <?php echo number_format($sp['Gia'], 0, ',', '.'); ?>đ</p>
            <p>Kích thước: <strong><?php echo $sp['KichThuoc']; ?></strong></p>
            <p>Đã bán: <strong><?php echo $sp['DaBan']; ?></strong> sản phẩm</p>
            
            <div class="services">
                <?php if($sp['is_free_gift']) echo '<span class="service-badge">🎁 Gói quà miễn phí</span>'; ?>
                <?php if($sp['is_ship_fast']) echo '<span class="service-badge">🚀 Giao hàng nhanh</span>'; ?>
            </div>

            <h3>Mô tả:</h3>
            <p><?php echo $sp['MoTa']; ?></p>
            
            <button class="btn-add-cart">Thêm vào giỏ hàng</button>
        </div>

        <hr>

        <div class="reviews-section">
            <h3>Đánh giá từ khách hàng</h3>
            <?php if($res_dg->num_rows > 0): ?>
                <?php while($dg = $res_dg->fetch_assoc()): ?>
                    <div class="review-item">
                        <strong><?php echo $dg['HoTen']; ?></strong> 
                        <span class="stars"><?php echo str_repeat('⭐', $dg['Diem']); ?></span>
                        <p><?php echo $dg['NoiDung']; ?></p>
                        <small><?php echo $dg['ThoiGian']; ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>