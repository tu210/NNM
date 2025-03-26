<?php
// public/sinhvien/show.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/SinhVien.php';

// --- Lấy MaSV từ URL ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=" . urlencode("Mã sinh viên không hợp lệ."));
    exit;
}
$maSV = $_GET['id'];

// --- Lấy thông tin sinh viên ---
$student = getStudentById($mysqli, $maSV);

// Nếu không tìm thấy sinh viên
if ($student === null) {
    header("Location: index.php?error=" . urlencode("Không tìm thấy sinh viên với mã '$maSV'."));
    exit;
}

$page_title = 'Chi tiết Sinh Viên - ' . htmlspecialchars($student['HoTen']);

// --- Include header ---
require_once __DIR__ . '/../../includes/header.php';
?>

<h2>CHI TIẾT SINH VIÊN</h2>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                <?php if (!empty($student['Hinh']) && file_exists(__DIR__ . '/../' . $student['Hinh'])): ?>
                    <img src="<?php echo BASE_URL . htmlspecialchars($student['Hinh']); ?>"
                        alt="<?php echo htmlspecialchars($student['HoTen']); ?>" class="img-fluid rounded detail-img mb-3">
                <?php else: ?>
                    <p>(Không có ảnh)</p>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-3">Mã Sinh Viên:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($student['MaSV']); ?></dd>

                    <dt class="col-sm-3">Họ Tên:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($student['HoTen']); ?></dd>

                    <dt class="col-sm-3">Giới Tính:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($student['GioiTinh']); ?></dd>

                    <dt class="col-sm-3">Ngày Sinh:</dt>
                    <dd class="col-sm-9">
                        <?php
                        if (!empty($student['NgaySinh'])) {
                            try {
                                $ngaySinhObj = new DateTime($student['NgaySinh']);
                                echo $ngaySinhObj->format('d/m/Y');
                            } catch (Exception $e) {
                                echo htmlspecialchars($student['NgaySinh']); // Hiển thị gốc nếu lỗi format
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </dd>

                    <dt class="col-sm-3">Ngành Học:</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($student['TenNganh'] ?? 'N/A'); ?></dd>
                </dl>
                <div class="mt-3">
                    <a href="edit.php?id=<?php echo $student['MaSV']; ?>" class="btn btn-primary">Chỉnh sửa</a>
                    <a href="index.php" class="btn btn-secondary">Quay lại Danh sách</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../../includes/footer.php';

// Đóng kết nối CSDL
db_close($mysqli);
?>