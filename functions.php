<?php
// functions.php
function taoThongBao($conn, $loai, $ma_nd, $noi_dung) {
    $loai = mysqli_real_escape_string($conn, $loai);
    $noi_dung = mysqli_real_escape_string($conn, $noi_dung);
    $ma_nd = (int)$ma_nd;

    $sql = "INSERT INTO ThongBaoAdmin (LoaiThongBao, MaND, NoiDung, DaXem, ThoiGian) 
            VALUES ('$loai', $ma_nd, '$noi_dung', 0, NOW())";
    return $conn->query($sql);
}
?>