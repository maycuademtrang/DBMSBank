<?php
require_once 'db_connect.php';

// Tạo mã hash chuẩn trực tiếp từ hệ thống PHP của bạn
$password_chuẩn = password_hash('123456', PASSWORD_DEFAULT);

// Cập nhật thẳng vào Database
$sql = "UPDATE nhan_vien SET Username = 'admin', Password = ?, Role = 'QuanLy' WHERE MaNV = 'NV001'";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$password_chuẩn])) {
    echo "<h1>Đã reset mật khẩu thành công!</h1>";
    echo "<p>Tài khoản: <b>admin</b></p>";
    echo "<p>Mật khẩu: <b>123456</b></p>";
    echo "<a href='login.php'>Bấm vào đây để quay lại trang Đăng nhập</a>";
} else {
    echo "Có lỗi xảy ra, hãy kiểm tra lại kết nối DB!";
}
?>