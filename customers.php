<?php
session_start();
if (!isset($_SESSION['MaNV'])) {
    header("Location: login.php");
    exit;
}
?>
<?php
// Tệp tin: customers.php (Quản lý Khách hàng)
require_once 'db_connect.php';

$action = $_GET['action'] ?? 'list';
$message = '';

// Xử lý Thêm/Sửa/Xóa (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_post = $_POST['action'] ?? '';

    if ($action_post === 'add') {
        // Lấy dữ liệu từ form thêm mới
        $maKH = $_POST['MaKH'];
        $tenKH = $_POST['TenKH'];
        $cccd = $_POST['CCCD'];
        $sdt = $_POST['SDT'];
        $email = $_POST['Email'];
        $diaChi = $_POST['DiaChi'];

        // Kiểm tra mã KH đã tồn tại chưa
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM KHACH_HANG WHERE MaKH = ?");
        $stmt_check->execute([$maKH]);
        if ($stmt_check->fetchColumn() > 0) {
            $message = "<div class='alert alert-danger'>Mã khách hàng đã tồn tại!</div>";
        } else {
            // Prepared Statement để chèn dữ liệu
            $sql = "INSERT INTO KHACH_HANG (MaKH, TenKH, CCCD, SDT, Email, DiaChi) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$maKH, $tenKH, $cccd, $sdt, $email, $diaChi])) {
                $message = "<div class='alert alert-success'>Thêm khách hàng thành công!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Lỗi khi thêm khách hàng!</div>";
            }
        }
    } elseif ($action_post === 'edit') {
        // Lấy dữ liệu từ form chỉnh sửa
        $maKH = $_POST['MaKH']; // Khóa chính (không cho phép sửa mã trong form)
        $tenKH = $_POST['TenKH'];
        $cccd = $_POST['CCCD'];
        $sdt = $_POST['SDT'];
        $email = $_POST['Email'];
        $diaChi = $_POST['DiaChi'];

        // Prepared Statement để cập nhật
        $sql = "UPDATE KHACH_HANG SET TenKH = ?, CCCD = ?, SDT = ?, Email = ?, DiaChi = ? WHERE MaKH = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$tenKH, $cccd, $sdt, $email, $diaChi, $maKH])) {
            $message = "<div class='alert alert-success'>Cập nhật thông tin thành công!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Lỗi khi cập nhật!</div>";
        }
    } elseif ($action_post === 'delete') {
        // Chức năng XÓA MỀM (Soft Delete) - Cập nhật TrangThaiXoa = 1
        $maKH = $_POST['MaKH'];
        $sql = "UPDATE KHACH_HANG SET TrangThaiXoa = 1 WHERE MaKH = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$maKH])) {
            $message = "<div class='alert alert-success'>Đã xóa khách hàng thành công (Soft Delete)!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Lỗi khi xóa khách hàng!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khách hàng - Ngân hàng Nội bộ</title>
    <!-- Sử dụng Bootstrap 5 từ CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,.05); border: none; }
        .table-hover tbody tr:hover { background-color: #f1f5f9; }
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
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="text-primary"><i class="bi bi-people-fill"></i> Quản lý Khách Hàng</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="?action=add" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm mới</a>
            <a href="?action=stats" class="btn btn-info text-white"><i class="bi bi-bar-chart-fill"></i> Thống kê</a>
            <a href="?action=list" class="btn btn-secondary"><i class="bi bi-list"></i> Danh sách</a>
        </div>
    </div>

    <?= $message; ?>

    <?php if ($action === 'list'): ?>
        <!-- DANH SÁCH KHÁCH HÀNG -->
        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>Mã KH</th>
                            <th>Tên Khách Hàng</th>
                            <th>CCCD</th>
                            <th>SĐT</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Chỉ lấy khách hàng chưa bị xóa (TrangThaiXoa = 0)
                        $stmt = $pdo->query("SELECT * FROM KHACH_HANG WHERE TrangThaiXoa = 0 ORDER BY NgayDangKy DESC");
                        $customers = $stmt->fetchAll();
                        if (count($customers) > 0):
                            foreach ($customers as $kh): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($kh['MaKH']) ?></td>
                                    <td><?= htmlspecialchars($kh['TenKH']) ?></td>
                                    <td><?= htmlspecialchars($kh['CCCD']) ?></td>
                                    <td><?= htmlspecialchars($kh['SDT']) ?></td>
                                    <td><?= htmlspecialchars($kh['Email']) ?></td>
                                    <td><?= htmlspecialchars($kh['DiaChi']) ?></td>
                                    <td class="text-center">
                                        <a href="?action=edit&id=<?= urlencode($kh['MaKH']) ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Sửa</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khách hàng này không?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="MaKH" value="<?= htmlspecialchars($kh['MaKH']) ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Chưa có dữ liệu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($action === 'add'): ?>
        <!-- FORM THÊM MỚI -->
        <div class="card w-75 mx-auto">
            <div class="card-header bg-success text-white"><h5>Thêm Khách Hàng Mới</h5></div>
            <div class="card-body">
                <form method="POST" action="customers.php?action=list">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label>Mã Khách Hàng</label>
                        <input type="text" name="MaKH" class="form-control" required placeholder="VD: KH001">
                    </div>
                    <div class="mb-3">
                        <label>Tên Khách Hàng</label>
                        <input type="text" name="TenKH" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Số CCCD</label>
                            <input type="text" name="CCCD" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Số Điện Thoại</label>
                            <input type="text" name="SDT" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="Email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Địa Chỉ</label>
                        <textarea name="DiaChi" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Lưu Khách Hàng</button>
                    <a href="?action=list" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>

    <?php elseif ($action === 'edit' && isset($_GET['id'])): 
        // Lấy dữ liệu cũ để đưa vào form
        $stmt = $pdo->prepare("SELECT * FROM KHACH_HANG WHERE MaKH = ? AND TrangThaiXoa = 0");
        $stmt->execute([$_GET['id']]);
        $kh = $stmt->fetch();
        if (!$kh) {
            echo "<div class='alert alert-danger'>Khách hàng không tồn tại hoặc đã bị xóa.</div>";
            exit;
        }
    ?>
        <!-- FORM CHỈNH SỬA -->
        <div class="card w-75 mx-auto">
            <div class="card-header bg-warning text-dark"><h5>Chỉnh Sửa Thông Tin Khách Hàng</h5></div>
            <div class="card-body">
                <form method="POST" action="customers.php?action=list">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="MaKH" value="<?= htmlspecialchars($kh['MaKH']) ?>">
                    
                    <div class="mb-3">
                        <label>Mã Khách Hàng (Không thể thay đổi)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($kh['MaKH']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label>Tên Khách Hàng</label>
                        <input type="text" name="TenKH" class="form-control" value="<?= htmlspecialchars($kh['TenKH']) ?>" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Số CCCD</label>
                            <input type="text" name="CCCD" class="form-control" value="<?= htmlspecialchars($kh['CCCD']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Số Điện Thoại</label>
                            <input type="text" name="SDT" class="form-control" value="<?= htmlspecialchars($kh['SDT']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($kh['Email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Địa Chỉ</label>
                        <textarea name="DiaChi" class="form-control" rows="2"><?= htmlspecialchars($kh['DiaChi']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning">Cập Nhật</button>
                    <a href="?action=list" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>

    <?php elseif ($action === 'stats'): ?>
        <!-- THỐNG KÊ -->
        <div class="card w-75 mx-auto">
            <div class="card-header bg-info text-white"><h5>Thống Kê Khách Hàng Theo Địa Chỉ</h5></div>
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead class="table-info">
                        <tr>
                            <th>Địa Chỉ</th>
                            <th>Số Lượng Khách Hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT DiaChi, COUNT(MaKH) AS SoLuong 
                            FROM KHACH_HANG 
                            WHERE TrangThaiXoa = 0 AND DiaChi != '' AND DiaChi IS NOT NULL
                            GROUP BY DiaChi 
                            ORDER BY SoLuong DESC
                        ");
                        $stats = $stmt->fetchAll();
                        if (count($stats) > 0):
                            foreach ($stats as $stat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($stat['DiaChi']) ?></td>
                                    <td class="fw-bold"><?= $stat['SoLuong'] ?></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="2">Chưa có dữ liệu thống kê.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
