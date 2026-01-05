<?php
session_start();
include 'db_connect.php'; 

$search_query = $_GET['search'] ?? '';
$filter_category = $_GET['dm'] ?? ''; 

// --- TRUY VẤN DANH MỤC ---
$sql_danhmuc = "SELECT MaDM, TenDM FROM DanhMuc ORDER BY MaDM ASC";
$result_danhmuc = $conn->query($sql_danhmuc);
$danh_muc_list = [];
while($dm_row = $result_danhmuc->fetch_assoc()) { $danh_muc_list[] = $dm_row; }

// --- TRUY VẤN THÔNG BÁO (NẾU LÀ ADMIN) ---
$unread_count = 0;
if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] == 'Admin') {
    $notify_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM thongbaoadmin WHERE DaXem = 0");
    $notify_data = mysqli_fetch_assoc($notify_query);
    $unread_count = $notify_data['total'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bong Store - Trang Chủ</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>"> 
    <style>
        /* CSS SLIDER & BANNER */
        .slider-container { width: 100%; position: relative; overflow: hidden; height: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .slider-track { display: flex; height: 100%; transition: transform 0.5s ease-in-out; }
        .slide { min-width: 100%; height: 100%; }
        .slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        
        .user-controls { display: flex; align-items: center; gap: 15px; }
        .user-name-link { color: #fff; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .user-name-link:hover { color: #4CAF50; text-decoration: underline; }
        .logout-btn { color: #ff4d4d; font-weight: bold; text-decoration: none; padding: 4px 10px; border: 1px solid #ff4d4d; border-radius: 3px; }
        
        /* Badge thông báo */
        .badge-notify { background: red; color: white; padding: 2px 6px; border-radius: 50%; font-size: 10px; position: relative; top: -10px; left: -5px; }

        .product-card { border: 1px solid #333; padding: 15px; border-radius: 10px; text-align: center; background: #1a1a1a; }
        .info-container { background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; margin: 10px 0; }
        .highlight-text { color: #4CAF50; font-weight: bold; }
        .xem-them-btn { display: block; width: 150px; margin: 25px auto; padding: 12px; background: #CC0000; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

<header class="header-bar">
    <div class="nav-container">
        <a href="index.php" class="logo-image-link">
            <img src="hinh_anh/LOGO.png" alt="Logo" style="height:40px;">
            <span class="store-name-header">BONG STORE</span> 
        </a>
        <nav class="nav-links">
            <a href="index.php">Trang Chủ</a>
            <a href="gio_hang.php">Giỏ Hàng</a>
            <?php if (isset($_SESSION['MaND'])): ?>
                <a href="lich_su_don_hang.php">Lịch Sử ĐH</a>
                <?php if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] == 'Admin'): ?>
                    <a href="admin_thong_ke.php">Thống Kê 
                        <?php if($unread_count > 0): ?><span class="badge-notify"><?= $unread_count ?></span><?php endif; ?>
                    </a>
                    <a href="danh_sach_san_pham.php">Quản Lý SP</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="user-controls">
            <?php if (isset($_SESSION['MaND'])): 
                $display_name = (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] == 'Admin') ? "Thu ngân (Admin)" : htmlspecialchars($_SESSION['HoTen'] ?? $_SESSION['TenDangNhap']);
            ?>
                <a href="thong_tin_tai_khoan.php" class="user-name-link">
                    Xin chào, <strong><?= $display_name ?></strong>
                </a>
                <a href="xu_ly_dang_xuat.php" class="logout-btn">Đăng Xuất</a>
            <?php else: ?>
                <a href="dang_nhap.php" class="user-name-link">Đăng Nhập</a>
                <a href="dang_ky.php" class="user-name-link" style="margin-left:10px;">Đăng Ký</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="content-container" style="max-width: 100%; padding: 0;">

    <?php if (empty($search_query) && empty($filter_category)): ?>
        <div class="slider-container">
            <div class="slider-track">
                <div class="slide"><img src="hinh_anh/Stich2.jpg" alt="Slide 1"></div>
                <div class="slide"><img src="hinh_anh/AI.png" alt="Slide 2"></div>
                <div class="slide"><img src="hinh_anh/AI3.jpg" alt="Slide 3"></div>
                <div class="slide"><img src="hinh_anh/AI4.jpg" alt="Slide 4"></div>
            </div>
            <button class="prev-btn" style="left:10px; position:absolute; top:50%; background:rgba(0,0,0,0.5); color:white; border:none; padding:15px; cursor:pointer; border-radius:50%;">❮</button>
            <button class="next-btn" style="right:10px; position:absolute; top:50%; background:rgba(0,0,0,0.5); color:white; border:none; padding:15px; cursor:pointer; border-radius:50%;">❯</button>
        </div>

        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div class="special-banner-section" style="text-align:center; margin:40px 0;">
                <h3 style="color: #4CAF50; font-size: 2em;">GẤU TEDDY SIÊU KHỔNG LỒ</h3>
                <img src="hinh_anh/safe2.png" alt="Banner Safe" style="width:100%; border-radius:10px;">
            </div>

            <?php foreach ($danh_muc_list as $dm): 
                $ma_dm = $dm['MaDM']; $ten_dm = $dm['TenDM'];
                // Logic hiển thị banner theo danh mục
                $banner_image = 'GAUBONGTEDY.png';
                if (stripos($ten_dm, 'Hoạt Hình') !== false) $banner_image = 'GAUBONGHOATHINH.png';
                elseif (stripos($ten_dm, 'Hình Thú') !== false) $banner_image = 'GAUBONGHINHTHU.jpg';
            ?>
                <div class="category-section">
                    <h3 style="text-align:center; color:#4CAF50; margin:40px 0; text-transform:uppercase;"><?= htmlspecialchars($ten_dm) ?></h3>
                    <img src="hinh_anh/<?= $banner_image ?>" alt="Banner DM" style="width:100%; border-radius:10px;">
                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top:25px;">
                        <?php
                        $sql_sp = "SELECT sp.*, (SELECT AVG(Diem) FROM danhgia WHERE MaSP = sp.MaSP) as DiemTB FROM SanPham sp WHERE MaDM = $ma_dm LIMIT 4";
                        $res_sp = $conn->query($sql_sp);
                        while($row = $res_sp->fetch_assoc()) { displayCard($row); }
                        ?>
                    </div>
                    <a href="index.php?dm=<?= $ma_dm ?>" class="xem-them-btn">XEM THÊM ></a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
            <h2 style="color:#4CAF50; text-align:center;">KẾT QUẢ CHO: <?= htmlspecialchars($search_query) ?></h2>
            <div class="product-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <?php
                $sql_filter = "SELECT sp.*, (SELECT AVG(Diem) FROM danhgia WHERE MaSP = sp.MaSP) as DiemTB FROM SanPham sp WHERE 1";
                if($filter_category) $sql_filter .= " AND MaDM = " . (int)$filter_category;
                if($search_query) $sql_filter .= " AND TenSP LIKE '%$search_query%'";
                $res_filter = $conn->query($sql_filter);
                while($row = $res_filter->fetch_assoc()) { displayCard($row); }
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="footer-container" style="display: flex; justify-content: space-around; background: #111; padding: 40px; color: #ccc;">
        <div class="footer-col">
            <h4>BONG STORE</h4>
            <p>126 Nguyễn Thiện Thành, Trà Vinh</p>
            <p>SĐT: 0343047913</p>
        </div>
        <div class="footer-col">
            <h4>Liên kết</h4>
            <ul><li><a href="index.php" style="color:#ccc;">Trang Chủ</a></li></ul>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <ul><li><a href="#" style="color:#ccc;">Chính sách bảo mật</a></li></ul>
        </div>
        <div class="footer-col">
            <h4>Email</h4>
            <p>ThuNgandh77@gmail.com</p>
        </div>
    </div>
    <div class="copyright" style="text-align:center; padding:15px; background: #000;">&copy; 2025 Bong Store.</div>
</footer>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const track = document.querySelector('.slider-track');
    function showSlide(index) {
        if (index >= slides.length) currentSlide = 0;
        else if (index < 0) currentSlide = slides.length - 1;
        else currentSlide = index;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
    document.querySelector('.next-btn').addEventListener('click', () => showSlide(currentSlide + 1));
    document.querySelector('.prev-btn').addEventListener('click', () => showSlide(currentSlide - 1));
    setInterval(() => showSlide(currentSlide + 1), 5000);
</script>

<?php
function displayCard($row) {
    $gia = number_format($row['Gia'], 0, ',', '.');
    $sao = $row['DiemTB'] ? round($row['DiemTB'], 1) . ' ⭐' : 'Chưa có sao';
    $hinh = !empty($row['HinhAnh']) ? $row['HinhAnh'] : 'default.jpg';
    ?>
    <div class="product-card">
        <a href="chi_tiet_sp.php?masp=<?= $row['MaSP'] ?>" style="text-decoration:none;">
            <img src="hinh_anh/<?= htmlspecialchars($hinh) ?>" alt="SP" style="width:100%; height:180px; object-fit:contain;">
            <h3 style="color:white; font-size:16px; margin:10px 0;"><?= htmlspecialchars($row['TenSP']) ?></h3>
        </a>
        <div class="info-container">
            <div style="color:#ccc; font-size:12px;">Size: <span class="highlight-text"><?= $row['KichThuoc'] ?></span></div>
            <div style="color:#ccc; font-size:12px;">Kho: <span class="highlight-text"><?= $row['SoLuongTon'] ?></span></div>
        </div>
        <div style="color:#f1c40f; margin-bottom:10px;"><?= $sao ?></div>
        <p style="color:#4CAF50; font-weight:bold;"><?= $gia ?> VNĐ</p>
        <a href="them_vao_gio.php?masp=<?= $row['MaSP'] ?>" style="background:#4CAF50; color:white; padding:8px 15px; text-decoration:none; border-radius:5px; display:inline-block; margin-top:10px;">Thêm vào giỏ</a>
    </div>
    <?php
}
?>
</body>
</html>