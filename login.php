<?php
session_start();
require_once 'db_connect.php';

// Nếu đã đăng nhập thì đẩy thẳng vào trang chủ
if (isset($_SESSION['MaNV'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['Username']);
    $password = $_POST['Password'];

    $stmt = $pdo->prepare("SELECT * FROM NHAN_VIEN WHERE Username = ? AND TrangThaiXoa = 0");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Kiểm tra tài khoản và mật khẩu
    if ($user && password_verify($password, $user['Password'])) {
        // Đăng nhập thành công -> Lưu Session
        $_SESSION['MaNV'] = $user['MaNV'];
        $_SESSION['TenNV'] = $user['TenNV'];
        $_SESSION['Role'] = $user['Role'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Hệ thống Ngân hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center text-primary mb-4">BankApp Login</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Tên đăng nhập</label>
                <input type="text" name="Username" class="form-control" required placeholder="Nhập username...">
            </div>
            <div class="mb-4">
                <label>Mật khẩu</label>
                <input type="password" name="Password" class="form-control" required placeholder="Nhập mật khẩu...">
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
        </form>
    </div>
</body>
</html>