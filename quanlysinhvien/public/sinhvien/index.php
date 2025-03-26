<?php
// public/sinhvien/index.php

require_once __DIR__ . '/../../config/database.php'; // Đường dẫn tương đối từ file hiện tại đến database.php
require_once __DIR__ . '/../../src/SinhVien.php';   // Đường dẫn tương đối đến SinhVien.php

// Lấy danh sách sinh viên
$students = getAllStudentsWithNganh($mysqli);

$page_title = 'Danh sách Sinh Viên'; // Đặt tiêu đề cho trang

// Include header
require_once __DIR__ . '/../../includes/header.php'; // Đường dẫn tương đối
?>

<h1>TRANG SINH VIÊN</h1>

<div class="mb-3">
    <a href="create.php" class="btn btn-success">Add Student</a>
</div>

<?php
// Hiển thị thông báo (nếu có, ví dụ sau khi thêm/sửa/xóa thành công)
// Bạn có thể dùng session flash messages cho việc này
if (isset($_GET['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>MaSV</th>
            <th>Họ Tên</th>
            <th>Giới Tính</th>
            <th>Ngày Sinh</th>
            <th>Hình</th>
            <th>Ngành Học</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['MaSV']); ?></td>
                    <td><?php echo htmlspecialchars($student['HoTen']); ?></td>
                    <td><?php echo htmlspecialchars($student['GioiTinh']); ?></td>
                    <td>
                        <?php
                        // Định dạng lại ngày sinh từ YYYY-MM-DD sang DD/MM/YYYY
                        if (!empty($student['NgaySinh'])) {
                            $ngaySinhObj = date_create($student['NgaySinh']);
                            if ($ngaySinhObj) {
                                echo date_format($ngaySinhObj, 'd/m/Y');
                            } else {
                                echo htmlspecialchars($student['NgaySinh']); // Hiển thị như cũ nếu không parse được
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (!empty($student['Hinh']) && file_exists(__DIR__ . '/../' . $student['Hinh'])): ?>
                            <img src="<?php echo BASE_URL . htmlspecialchars($student['Hinh']); ?>"
                                alt="<?php echo htmlspecialchars($student['HoTen']); ?>" class="student-img-thumbnail">
                        <?php else: ?>
                            <small>No image</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($student['TenNganh'] ?? 'N/A'); // Hiển thị tên ngành ?></td>
                    <td class="action-links">
                        <a href="edit.php?id=<?php echo $student['MaSV']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="show.php?id=<?php echo $student['MaSV']; ?>" class="btn btn-sm btn-info">Details</a>
                        <a href="delete.php?id=<?php echo $student['MaSV']; ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">Không có sinh viên nào.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// Include footer
require_once __DIR__ . '/../../includes/footer.php'; // Đường dẫn tương đối

// Đóng kết nối CSDL
db_close($mysqli);
?>