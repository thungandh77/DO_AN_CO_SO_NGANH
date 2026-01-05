<?php
// Mật khẩu bạn muốn dùng cho Admin
$mat_khau_goc = '123456';

// Mã hóa mật khẩu
$mat_khau_da_hash = password_hash($mat_khau_goc, PASSWORD_DEFAULT);

// In ra chuỗi đã mã hóa
echo "Mật khẩu mã hóa cho '$mat_khau_goc' là: " . $mat_khau_da_hash;
?>