<?php
// public/sinhvien/create.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['MaSV'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang login
    // Sử dụng đường dẫn tuyệt đối từ gốc web để đảm bảo đúng từ mọi nơi
    header('Location: /quanlysinhvien/public/login.php?error=' . urlencode('Bạn cần đăng nhập để thực hiện hành động này.'));
    // Lưu ý: '/quanlysinhvien/' là đường dẫn gốc của dự án bạn, chỉnh lại nếu cần.
    exit(); // Dừng thực thi script hiện tại ngay lập tức
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/NganhHoc.php'; // Cần để lấy danh sách ngành

// Lấy danh sách ngành học
$nganhHocList = getAllNganhHoc($mysqli);

$page_title = 'Thêm Sinh Viên'; // Đặt tiêu đề

// Include header
require_once __DIR__ . '/../../includes/header.php';
?>

<h2>THÊM SINH VIÊN MỚI</h2>

<?php
// Hiển thị thông báo lỗi nếu có khi quay lại từ store.php
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<div class="form-container">
    <form method="POST" action="store.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="MaSV" class="form-label">Mã Sinh Viên:</label>
            <input type="text" class="form-control" id="MaSV" name="MaSV" required maxlength="10">
        </div>

        <div class="mb-3">
            <label for="HoTen" class="form-label">Họ Tên:</label>
            <input type="text" class="form-control" id="HoTen" name="HoTen" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới Tính:</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="GioiTinh" id="gioiTinhNam" value="Nam" checked>
                    <label class="form-check-label" for="gioiTinhNam">Nam</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="GioiTinh" id="gioiTinhNu" value="Nữ">
                    <label class="form-check-label" for="gioiTinhNu">Nữ</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="GioiTinh" id="gioiTinhKhac" value="Khác">
                    <label class="form-check-label" for="gioiTinhKhac">Khác</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="NgaySinh" class="form-label">Ngày Sinh:</label>
            <input type="date" class="form-control" id="NgaySinh" name="NgaySinh">
        </div>

        <div class="mb-3">
            <label for="Hinh" class="form-label">Hình Ảnh:</label>
            <input type="file" class="form-control" id="Hinh" name="Hinh" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="MaNganh" class="form-label">Ngành Học:</label>
            <select class="form-select" id="MaNganh" name="MaNganh" required>
                <option value="">-- Chọn Ngành Học --</option>
                <?php foreach ($nganhHocList as $nganh): ?>
                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>">
                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
        <a href="index.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<?php
// Include footer
require_once __DIR__ . '/../../includes/footer.php';

// Đóng kết nối CSDL
db_close($mysqli);
?>