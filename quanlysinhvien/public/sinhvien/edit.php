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
require_once __DIR__ . '/../../src/SinhVien.php';
require_once __DIR__ . '/../../src/NganhHoc.php';

// --- Lấy MaSV từ URL ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=" . urlencode("Mã sinh viên không hợp lệ."));
    exit;
}
$maSV = $_GET['id'];

// --- Lấy thông tin sinh viên hiện tại ---
$student = getStudentById($mysqli, $maSV);

// Nếu không tìm thấy sinh viên, chuyển hướng về danh sách
if ($student === null) {
    header("Location: index.php?error=" . urlencode("Không tìm thấy sinh viên với mã '$maSV'."));
    exit;
}

// --- Lấy danh sách ngành học ---
$nganhHocList = getAllNganhHoc($mysqli);

$page_title = 'Chỉnh sửa Sinh Viên - ' . htmlspecialchars($student['HoTen']); // Đặt tiêu đề

// --- Include header ---
require_once __DIR__ . '/../../includes/header.php';
?>

<h2>CHỈNH SỬA THÔNG TIN SINH VIÊN</h2>

<?php
// Hiển thị thông báo lỗi nếu có khi quay lại từ update.php
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<div class="form-container">
    <form method="POST" action="update.php" enctype="multipart/form-data">

        <input type="hidden" name="MaSV" value="<?php echo htmlspecialchars($student['MaSV']); ?>">
        <input type="hidden" name="current_hinh" value="<?php echo htmlspecialchars($student['Hinh'] ?? ''); ?>">


        <div class="mb-3">
            <label for="MaSV_display" class="form-label">Mã Sinh Viên:</label>
            <input type="text" class="form-control" id="MaSV_display" value="<?php echo htmlspecialchars($student['MaSV']); ?>" readonly>
            </div>

        <div class="mb-3">
            <label for="HoTen" class="form-label">Họ Tên:</label>
            <input type="text" class="form-control" id="HoTen" name="HoTen" value="<?php echo htmlspecialchars($student['HoTen']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới Tính:</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="GioiTinh" id="gioiTinhNam" value="Nam"
                           <?php echo ($student['GioiTinh'] === 'Nam') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gioiTinhNam">Nam</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="GioiTinh" id="gioiTinhNu" value="Nữ"
                           <?php echo ($student['GioiTinh'] === 'Nữ') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gioiTinhNu">Nữ</label>
                </div>
                 <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="GioiTinh" id="gioiTinhKhac" value="Khác"
                           <?php echo ($student['GioiTinh'] === 'Khác') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gioiTinhKhac">Khác</label>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="NgaySinh" class="form-label">Ngày Sinh:</label>
            <input type="date" class="form-control" id="NgaySinh" name="NgaySinh" value="<?php echo htmlspecialchars($student['NgaySinh'] ?? ''); ?>">
            </div>

        <div class="mb-3">
             <label class="form-label">Hình Ảnh Hiện Tại:</label>
             <div>
                <?php if (!empty($student['Hinh']) && file_exists(__DIR__ . '/../' . $student['Hinh'])): ?>
                     <img src="<?php echo BASE_URL . htmlspecialchars($student['Hinh']); ?>"
                          alt="<?php echo htmlspecialchars($student['HoTen']); ?>" style="max-width: 150px; height: auto; margin-bottom: 10px;">
                 <?php else: ?>
                     <p>Không có ảnh</p>
                 <?php endif; ?>
             </div>
            <label for="Hinh" class="form-label">Chọn ảnh mới (để trống nếu không muốn thay đổi):</label>
            <input type="file" class="form-control" id="Hinh" name="Hinh" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="MaNganh" class="form-label">Ngành Học:</label>
            <select class="form-select" id="MaNganh" name="MaNganh" required>
                <option value="">-- Chọn Ngành Học --</option>
                <?php foreach ($nganhHocList as $nganh): ?>
                    <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>"
                            <?php echo ($student['MaNganh'] === $nganh['MaNganh']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="index.php" class="btn btn-secondary">Quay lại Danh sách</a>
    </form>
</div>

<?php
// Include footer
require_once __DIR__ . '/../../includes/footer.php';

// Đóng kết nối CSDL
db_close($mysqli);
?>