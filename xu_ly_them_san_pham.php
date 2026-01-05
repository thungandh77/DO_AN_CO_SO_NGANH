<?php
include 'db_connect.php'; // Kết nối CSDL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Lấy dữ liệu từ Form
    $ten_sp = $_POST['tensp'];
    $gia = $_POST['gia'];
    $soluong = $_POST['soluongton'];
    $ma_dm = 1; // Giả sử MaDM = 1, bạn cần phát triển logic này sau

    // 2. Viết câu lệnh SQL INSERT
    $sql = "INSERT INTO SanPham (TenSP, Gia, SoLuongTon, MaDM) 
            VALUES ('$ten_sp', $gia, $soluong, $ma_dm)";

    // 3. Thực thi và kiểm tra
    if ($conn->query($sql) === TRUE) {
        echo "Thêm sản phẩm thành công!";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }

    // 4. Đóng kết nối
    $conn->close();
}
?>