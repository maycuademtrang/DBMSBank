<?php
session_start();
if (!isset($_SESSION['MaNV'])) {
    header("Location: login.php");
    exit;
}
?>
<?php
// Tệp tin: index.php (Trang chủ quản lý hệ thống ngân hàng)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Ngân hàng Nội bộ</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .hero { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; padding: 3rem 0; border-radius: 10px; }
        .card-menu { transition: transform 0.2s; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-menu:hover { transform: translateY(-5px); cursor: pointer; }
        .icon-large { font-size: 3rem; color: #0d6efd; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-bank"></i> BankApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Trang chủ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="customers.php">Khách hàng</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="transactions.php">Giao dịch</a>
        </li>
        <?php if (isset($_SESSION['Role']) && $_SESSION['Role'] === 'QuanLy'): ?>
        <li class="nav-item">
          <a class="nav-link" href="employees.php">Nhân viên</a>
        </li>
        <?php endif; ?>
      </ul>
      
      <div class="d-flex text-white align-items-center">
        <span class="me-3">
          <i class="bi bi-person-circle"></i> Xin chào, 
          <strong><?= htmlspecialchars($_SESSION['TenNV'] ?? 'Khách') ?></strong> 
          (<?= htmlspecialchars($_SESSION['Role'] ?? '') ?>)
        </span>
        <a href="logout.php" class="btn btn-sm btn-danger">
          <i class="bi bi-box-arrow-right"></i> Đăng xuất
        </a>
      </div>
    </div>
  </div>
</nav>

<div class="container">
    <div class="hero text-center mb-5">
        <h1 class="display-4 fw-bold">Hệ Thống Quản Lý Ngân Hàng</h1>
        <p class="lead">Hệ thống quản lý khách hàng, tài khoản và giao dịch an toàn.</p>
    </div>

    <div class="row text-center">
        <!-- Quản lý Khách hàng -->
        <div class="col-md-4 mb-4">
            <div class="card card-menu h-100" onclick="window.location.href='customers.php'">
                <div class="card-body py-5">
                    <i class="bi bi-people-fill icon-large mb-3 d-block"></i>
                    <h3 class="card-title h5">Quản lý Khách Hàng</h3>
                    <p class="card-text text-muted">Thêm, sửa, xóa, tìm kiếm và thống kê dữ liệu khách hàng.</p>
                </div>
            </div>
        </div>

        <!-- Quản lý Giao Dịch -->
        <div class="col-md-4 mb-4">
            <div class="card card-menu h-100" onclick="window.location.href='transactions.php'">
                <div class="card-body py-5">
                    <i class="bi bi-currency-exchange icon-large mb-3 d-block"></i>
                    <h3 class="card-title h5">Quản lý Giao Dịch</h3>
                    <p class="card-text text-muted">Thực hiện nạp tiền, rút tiền, chuyển khoản an toàn qua hệ thống Transaction.</p>
                </div>
            </div>
        </div>

        <!-- Các module khác (Demo UI) -->
        <div class="col-md-4 mb-4">
            <div class="card card-menu h-100" style="opacity: 0.6;">
                <div class="card-body py-5">
                    <i class="bi bi-credit-card-2-front-fill icon-large mb-3 d-block text-secondary"></i>
                    <h3 class="card-title h5">Quản lý Tài Khoản & Thẻ</h3>
                    <p class="card-text text-muted">Module đang được phát triển...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
