<?php
// Tệp tin: transactions.php (Quản lý Giao Dịch & Xử lý Transaction)
require_once 'db_connect.php';

$action = $_GET['action'] ?? 'list';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_post = $_POST['action'] ?? '';

    if ($action_post === 'transfer') {
        $maGD = uniqid('GD_'); // Tạo mã giao dịch ngẫu nhiên
        $soTkGui = $_POST['SoTK_Gui'];
        $soTkNhan = $_POST['SoTK_Nhan'];
        $soTien = floatval($_POST['SoTien']);
        $noiDung = $_POST['NoiDung'];
        $maNV = $_POST['MaNV']; // Nhân viên thực hiện giao dịch (giả định có sẵn)

        if ($soTien <= 0) {
            $message = "<div class='alert alert-warning'>Số tiền phải lớn hơn 0!</div>";
        } elseif ($soTkGui === $soTkNhan) {
            $message = "<div class='alert alert-warning'>Tài khoản gửi và nhận không được trùng nhau!</div>";
        } else {
            try {
                // BẮT ĐẦU TRANSACTION
                $pdo->beginTransaction();

                // 1. Kiểm tra tài khoản gửi có đủ số dư và lấy số dư hiện tại (Dùng FOR UPDATE để lock row tránh race condition)
                $stmtCheckGui = $pdo->prepare("SELECT SoDu FROM TAI_KHOAN WHERE SoTK = ? AND TrangThaiXoa = 0 FOR UPDATE");
                $stmtCheckGui->execute([$soTkGui]);
                $tkGui = $stmtCheckGui->fetch();

                if (!$tkGui) {
                    throw new Exception("Tài khoản gửi không tồn tại hoặc đã bị khóa!");
                }
                if ($tkGui['SoDu'] < $soTien) {
                    throw new Exception("Số dư tài khoản không đủ để thực hiện giao dịch!");
                }

                // 2. Kiểm tra tài khoản nhận có tồn tại không
                $stmtCheckNhan = $pdo->prepare("SELECT SoTK FROM TAI_KHOAN WHERE SoTK = ? AND TrangThaiXoa = 0 FOR UPDATE");
                $stmtCheckNhan->execute([$soTkNhan]);
                if (!$stmtCheckNhan->fetch()) {
                    throw new Exception("Tài khoản nhận không tồn tại!");
                }

                // 3. Trừ tiền tài khoản gửi
                $stmtTruTien = $pdo->prepare("UPDATE TAI_KHOAN SET SoDu = SoDu - ? WHERE SoTK = ?");
                $stmtTruTien->execute([$soTien, $soTkGui]);

                // 4. Cộng tiền tài khoản nhận
                $stmtCongTien = $pdo->prepare("UPDATE TAI_KHOAN SET SoDu = SoDu + ? WHERE SoTK = ?");
                $stmtCongTien->execute([$soTien, $soTkNhan]);

                // 5. Lưu lịch sử giao dịch vào bảng GIAO_DICH
                $stmtGD = $pdo->prepare("INSERT INTO GIAO_DICH (MaGD, SoTK, MaNV, SoTK_Nhan, LoaiGD, SoTien, NoiDung) VALUES (?, ?, ?, ?, 'Chuyển khoản', ?, ?)");
                $stmtGD->execute([$maGD, $soTkGui, $maNV, $soTkNhan, $soTien, $noiDung]);

                // COMMIT TRANSACTION NẾU TẤT CẢ THÀNH CÔNG
                $pdo->commit();
                $message = "<div class='alert alert-success'>Chuyển khoản thành công! Mã GD: $maGD</div>";

            } catch (Exception $e) {
                // ROLLBACK NẾU CÓ BẤT KỲ LỖI NÀO XẢY RA
                $pdo->rollBack();
                $message = "<div class='alert alert-danger'>Giao dịch thất bại: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giao Dịch - Ngân hàng Nội bộ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style> body { background-color: #f8f9fa; } .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,.05); } </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-bank"></i> BankApp</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="customers.php">Khách hàng</a></li>
        <li class="nav-item"><a class="nav-link active" href="transactions.php">Giao dịch</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="text-primary"><i class="bi bi-currency-exchange"></i> Quản lý Giao Dịch</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="?action=transfer" class="btn btn-success"><i class="bi bi-arrow-left-right"></i> Chuyển khoản</a>
            <a href="?action=stats" class="btn btn-info text-white"><i class="bi bi-bar-chart-fill"></i> Thống kê GD</a>
            <a href="?action=list" class="btn btn-secondary"><i class="bi bi-list"></i> Lịch sử</a>
        </div>
    </div>

    <?= $message; ?>

    <?php if ($action === 'list'): ?>
        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>Mã GD</th>
                            <th>TK Gửi</th>
                            <th>TK Nhận</th>
                            <th>Loại GD</th>
                            <th>Số Tiền</th>
                            <th>Thời Gian</th>
                            <th>Nội Dung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM GIAO_DICH WHERE TrangThaiXoa = 0 ORDER BY ThoiGian DESC LIMIT 50");
                        $transactions = $stmt->fetchAll();
                        if (count($transactions) > 0):
                            foreach ($transactions as $gd): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($gd['MaGD']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($gd['SoTK']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($gd['SoTK_Nhan'] ?? '-') ?></td>
                                    <td class="text-center"><span class="badge bg-primary"><?= htmlspecialchars($gd['LoaiGD']) ?></span></td>
                                    <td class="text-end fw-bold text-success"><?= number_format($gd['SoTien']) ?> VNĐ</td>
                                    <td class="text-center"><?= htmlspecialchars($gd['ThoiGian']) ?></td>
                                    <td><?= htmlspecialchars($gd['NoiDung']) ?></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="7" class="text-center">Chưa có giao dịch nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($action === 'transfer'): ?>
        <div class="card w-75 mx-auto">
            <div class="card-header bg-success text-white"><h5>Thực Hiện Chuyển Khoản</h5></div>
            <div class="card-body">
                <form method="POST" action="transactions.php?action=list">
                    <input type="hidden" name="action" value="transfer">
                    <!-- Giả định lấy nhân viên thực hiện từ Session (ở đây fix cứng) -->
                    <input type="hidden" name="MaNV" value="NV001"> 
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Số Tài Khoản Gửi</label>
                            <input type="text" name="SoTK_Gui" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Số Tài Khoản Nhận</label>
                            <input type="text" name="SoTK_Nhan" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Số Tiền Giao Dịch (VNĐ)</label>
                        <input type="number" name="SoTien" class="form-control" min="1000" step="1000" required>
                    </div>
                    <div class="mb-3">
                        <label>Nội Dung Chuyển Khoản</label>
                        <textarea name="NoiDung" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" onclick="return confirm('Xác nhận chuyển khoản?');">Chuyển Tiền</button>
                    <a href="?action=list" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>

    <?php elseif ($action === 'stats'): ?>
        <!-- THỐNG KÊ TỔNG TIỀN THEO LOẠI GIAO DỊCH -->
        <div class="card w-75 mx-auto">
            <div class="card-header bg-info text-white"><h5>Thống Kê Tổng Tiền Theo Loại Giao Dịch</h5></div>
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead class="table-info">
                        <tr>
                            <th>Loại Giao Dịch</th>
                            <th>Tổng Số Lượng</th>
                            <th>Tổng Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Thống kê tổng tiền giao dịch theo loại
                        $stmt = $pdo->query("
                            SELECT LoaiGD, COUNT(MaGD) AS SoLuong, SUM(SoTien) AS TongTien
                            FROM GIAO_DICH 
                            WHERE TrangThaiXoa = 0 
                            GROUP BY LoaiGD
                        ");
                        $stats = $stmt->fetchAll();
                        if (count($stats) > 0):
                            foreach ($stats as $stat): ?>
                                <tr>
                                    <td><span class="badge bg-primary"><?= htmlspecialchars($stat['LoaiGD']) ?></span></td>
                                    <td><?= $stat['SoLuong'] ?> giao dịch</td>
                                    <td class="fw-bold text-success"><?= number_format($stat['TongTien']) ?> VNĐ</td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr><td colspan="3">Chưa có dữ liệu thống kê.</td></tr>
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
