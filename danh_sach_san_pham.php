<?php
session_start();
include 'db_connect.php';

// SQL: Chỉ lấy những sản phẩm CÒN TỒN TẠI (khác -99)
$sql = "SELECT sp.*, dm.TenDM 
        FROM SanPham sp 
        LEFT JOIN DanhMuc dm ON sp.MaDM = dm.MaDM 
        WHERE sp.SoLuongTon != -99
        ORDER BY sp.MaSP DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sản Phẩm - Bong Store</title>
    <style>
        /* GIỮ NGUYÊN CSS CỦA BẠN */
        body { background-color: #0e1111; color: white; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .title { color: #4CAF50; font-size: 22px; font-weight: bold; text-transform: uppercase; }
        .btn-home { background: #333; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; border: 1px solid #444; }
        .btn-add { background: #4CAF50; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        
        table { width: 100%; border-collapse: collapse; background: #1a1d1d; border-radius: 8px; overflow: hidden; }
        th { background: #2d7d32; color: white; padding: 15px; text-align: left; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #2d3333; }
        
        .img-sp { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .price { color: #4CAF50; font-weight: bold; }
        .sold { color: #42a5f5; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 2px; display: inline-block; }
        .bg-gift { background: #e65100; color: white; }
        .bg-ship { background: #00b8d4; color: white; }
        .actions a { text-decoration: none; font-weight: bold; margin-right: 15px; }
        .edit { color: #42a5f5; }
        .delete { color: #ff5252; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <div class="title">📦 QUẢN LÝ SẢN PHẨM NÂNG CẤP</div>
        <div>
            <a href="index.php" class="btn-home">🏠 Trang Chủ</a>
            <a href="them_moi_sp.php" class="btn-add">+ Thêm Sản Phẩm</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>HÌNH</th>
                <th>TÊN SẢN PHẨM</th>
                <th>DỊCH VỤ</th>
                <th>KÍCH THƯỚC</th>
                <th>GIÁ BÁN</th>
                <th>KHO</th>
                <th>ĐÃ BÁN</th>
                <th>THAO TÁC</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><img src="hinh_anh/<?= $row['HinhAnh'] ?>" class="img-sp"></td>
                <td><strong><?= htmlspecialchars($row['TenSP']) ?></strong></td>
                <td>
                    <span class="badge bg-gift">🎁 Gói quà</span><br>
                    <span class="badge bg-ship">🚀 Giao nhanh</span>
                </td>
                <td><?= $row['KichThuoc'] ?></td>
                <td class="price"><?= number_format($row['Gia'], 0, ',', '.') ?>đ</td>
                <td><?= $row['SoLuongTon'] ?></td>
                <td class="sold"><?= $row['DaBan'] ?? 0 ?></td>
                <td class="actions">
                    <a href="sua_san_pham.php?masp=<?= $row['MaSP'] ?>" class="edit">Sửa</a>
                    <a href="xoa_san_pham.php?masp=<?= $row['MaSP'] ?>" class="delete" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>