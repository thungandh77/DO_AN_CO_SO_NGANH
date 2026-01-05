<?php
session_start();
include 'db_connect.php';

if(isset($_POST['submit_danh_gia'])) {
    $ma_sp = $_POST['MaSP'];
    $diem = $_POST['Diem'];
    $noi_dung = $_POST['NoiDung'];
    // Giả sử bạn có MaND từ session khi người dùng đăng nhập
    $ma_nd = $_SESSION['MaND'] ?? 1; 

    // Câu lệnh SQL lưu đánh giá
    $sql = "INSERT INTO danhgia (MaSP, MaND, Diem, NoiDung, NgayDanhGia) 
            VALUES ('$ma_sp', '$ma_nd', '$diem', '$noi_dung', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Cảm ơn bạn đã đánh giá!'); window.location.href='index.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>