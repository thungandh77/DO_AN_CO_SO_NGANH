<?php
include 'db_connect.php';
include 'functions.php';
session_start();

if (isset($_POST['gui_dg'])) {
    $ma_sp = $_POST['MaSP'];
    $ma_nd = $_SESSION['MaND'];
    $diem = $_POST['Diem'];
    $noi_dung_dg = $_POST['NoiDung'];

    // 1. Lưu vào bảng DanhGia
    $sql = "INSERT INTO DanhGia (MaSP, MaND, Diem, NoiDung) 
            VALUES ($ma_sp, $ma_nd, $diem, '$noi_dung_dg')";

    if ($conn->query($sql) === TRUE) {
        // 2. THÔNG BÁO CHO ADMIN CÓ ĐÁNH GIÁ MỚI
        $noi_dung_tb = "vừa đánh giá $diem sao cho sản phẩm ID: $ma_sp";
        taoThongBao($conn, 'review', $ma_nd, $noi_dung_tb);

        header("Location: chi_tiet_san_pham.php?id=$ma_sp&msg=success");
    }
}
?>