<?php
// Thông tin kết nối CSDL (XAMPP mặc định)
$host = '127.0.0.1';
$db   = 'quanly_nganhang';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    // Bật ngoại lệ (Exception) khi có lỗi xảy ra để dễ dàng debug và catch lỗi
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    // Trả về dữ liệu dạng mảng kết hợp (Associative Array)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    // Tắt Emulate Prepares để bắt buộc dùng Prepared Statements thực sự của MySQL, chống SQL Injection
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Nếu lỗi kết nối, in ra thông báo lỗi
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>
