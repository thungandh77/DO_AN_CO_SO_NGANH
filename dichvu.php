<?php
// BẮT ĐẦU PHIÊN VÀ KIỂM TRA ĐĂNG NHẬP NẾU CẦN
session_start();
// include 'header.php'; // Nếu bạn có file header riêng

// Lấy tham số dịch vụ từ URL
$service_type = isset($_GET['type']) ? $_GET['type'] : '';

// Khai báo dữ liệu chi tiết cho từng dịch vụ
$services = [
    'giaohang' => [
        'title' => 'DỊCH VỤ GIAO HÀNG TẬN NHÀ',
        'image' => 'hinh_anh/dichvu_giaohang_banner.jpg', // Thay bằng ảnh banner thực tế
        'content' => '
<h3>Chính sách Giao Hàng của Bống Store</h3>
<p>Chúng tôi cung cấp dịch vụ giao hàng nhanh chóng và tin cậy đến tận tay khách hàng trên toàn quốc. Gấu bông được đóng gói cẩn thận, đảm bảo an toàn tuyệt đối trong quá trình vận chuyển.</p>
<ul>
    <li><strong>Giao hàng tiêu chuẩn:</strong> 3-5 ngày làm việc.</li>
    <li><strong>Giao hàng nhanh (Nội thành):</strong> 2-4 giờ sau khi xác nhận đơn hàng.</li>
    <li><strong>Miễn phí giao hàng:</strong> Áp dụng cho đơn hàng trên 500.000 VNĐ.</li>
</ul>
<p>Vui lòng kiểm tra sản phẩm trước khi thanh toán và liên hệ ngay với chúng tôi nếu có bất kỳ vấn đề gì về sản phẩm.</p>
        '
    ],
    'goiqua' => [
        'title' => 'DỊCH VỤ GÓI QUÀ MIỄN PHÍ',
        'image' => 'hinh_anh/dichvu_goiqua_banner.jpg', // Thay bằng ảnh banner thực tế
        'content' => '
<h3>Biến món quà của bạn trở nên đặc biệt</h3>
<p>Chúng tôi cung cấp dịch vụ gói quà miễn phí theo yêu cầu, giúp bạn tạo ấn tượng và sự bất ngờ cho người nhận. Chúng tôi sử dụng giấy gói cao cấp, nơ ruy-băng tinh tế và thẻ lời chúc miễn phí.</p>
<ul>
    <li><strong>Miễn phí:</strong> Gói quà cơ bản cho tất cả đơn hàng.</li>
    <li><strong>Gói quà cao cấp:</strong> Thêm phụ phí nhỏ cho hộp quà đặc biệt và hoa khô trang trí.</li>
    <li><strong>Thẻ lời chúc:</strong> Bạn có thể ghi nội dung lời chúc trong phần ghi chú khi đặt hàng.</li>
</ul>
<p>Hãy để Bống Store giúp bạn gửi gắm yêu thương qua từng món quà được chăm chút.</p>
        '
    ],
    'vesinh' => [
        'title' => 'DỊCH VỤ VỆ SINH & LÀM MỚI GẤU BÔNG',
        'image' => 'hinh_anh/dichvu_vesinh_banner.jpg', // Thay bằng ảnh banner thực tế
        'content' => '
<h3>Gấu Bông Sạch Sẽ, An Toàn Tuyệt Đối</h3>
<p>Dịch vụ vệ sinh chuyên sâu giúp loại bỏ bụi bẩn, vi khuẩn, nấm mốc và các tác nhân gây dị ứng, trả lại sự mềm mại và hương thơm cho gấu bông của bạn. Chúng tôi sử dụng các hóa chất chuyên dụng, an toàn cho da và sức khỏe.</p>
<h4>Quy Trình Vệ Sinh 4 Bước</h4>
<ol>
    <li><strong>Kiểm tra:</strong> Đánh giá chất liệu, tình trạng hỏng hóc (nếu có).</li>
    <li><strong>Xử lý vết bẩn:</strong> Dùng dung dịch đặc biệt để loại bỏ các vết bẩn cứng đầu.</li>
    <li><strong>Giặt hấp/Sấy khô:</strong> Sử dụng công nghệ giặt khô hoặc hấp nhiệt tùy theo chất liệu.</li>
    <li><strong>Hoàn thiện:</strong> Sấy khô, chải lông và khử khuẩn lần cuối.</li>
</ol>
<p><strong>Bảng Giá:</strong> Từ 50.000 VNĐ - 200.000 VNĐ tùy kích thước và chất liệu.</p>
        '
    ],
    'nennho' => [
        'title' => 'DỊCH VỤ NÉN NHỎ GẤU BÔNG (Đóng gói du lịch)',
        'image' => 'hinh_anh/dichvu_nennho_banner.jpg', // Thay bằng ảnh banner thực tế
        'content' => '
<h3>Tối ưu không gian cho chuyến đi của bạn</h3>
<p>Bạn muốn mang theo những chú gấu bông yêu thích khi đi du lịch hoặc gửi đi nước ngoài? Dịch vụ nén nhỏ gấu bông của chúng tôi giúp giảm tối đa kích thước, tiết kiệm không gian hành lý và chi phí vận chuyển.</p>
<h4>Lợi ích của việc Nén Nhỏ</h4>
<ul>
    <li><strong>Tiết kiệm:</strong> Giảm 50-70% thể tích, giảm cước phí vận chuyển.</li>
    <li><strong>Bảo vệ:</strong> Gấu bông được hút chân không, tránh ẩm mốc và bụi bẩn.</li>
    <li><strong>Đơn giản:</strong> Chỉ cần mở túi, gấu bông sẽ trở lại hình dáng ban đầu.</li>
</ul>
<p><strong>Giá dịch vụ:</strong> 20.000 VNĐ/con (áp dụng cho gấu bông từ 30cm trở lên).</p>
        '
    ],
];

// Kiểm tra xem loại dịch vụ có tồn tại không
if (array_key_exists($service_type, $services)) {
    $service = $services[$service_type];
} else {
    // Xử lý khi không tìm thấy dịch vụ (hoặc không có tham số)
    $service = null;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $service ? $service['title'] : 'Dịch Vụ của Bống Store'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php // include 'header.php'; ?>
    <div class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo">BỐNG STORE</a>
        </div>
    </div>
    <div class="content-container service-detail">
        <?php if ($service): ?>
        <a href="index.php" class="back-to-home">← Quay lại trang chủ</a>

        <h1 class="service-title"><?php echo $service['title']; ?></h1>

        <div class="service-image-banner">
            <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['title']; ?>">
        </div>

        <div class="service-content">
            <?php echo $service['content']; ?>
        </div>

        <a href="index.php" class="back-to-home" style="margin-top: 20px;">← Quay lại trang chủ</a>

        <?php else: ?>
        <div class="message-box error">
            <p>Không tìm thấy thông tin dịch vụ này.</p>
            <a href="index.php" class="back-to-home">Quay lại Trang Chủ</a>
        </div>
        <?php endif; ?>
    </div>

    <?php // include 'footer.php'; ?>
</body>
</html>