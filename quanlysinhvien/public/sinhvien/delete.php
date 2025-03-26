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

// --- Lấy MaSV từ URL ---
// Quan trọng: Nên kiểm tra thêm quyền hạn của người dùng trước khi thực hiện xóa
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=" . urlencode("Mã sinh viên không hợp lệ."));
    exit;
}
$maSV = $_GET['id'];

// --- (Tùy chọn nhưng nên có) Lấy thông tin sinh viên ĐỂ LẤY ĐƯỜNG DẪN ẢNH trước khi xóa ---
$student = getStudentById($mysqli, $maSV);
$hinhPathToDelete = null;
if ($student !== null && !empty($student['Hinh'])) {
    $hinhPathToDelete = $student['Hinh']; // Lưu lại đường dẫn ảnh để xóa file
} elseif ($student === null) {
    // Nếu không tìm thấy sinh viên thì cũng không cần xóa nữa, báo lỗi luôn
    header("Location: index.php?error=" . urlencode("Không tìm thấy sinh viên để xóa."));
    db_close($mysqli); // Nhớ đóng kết nối
    exit;
}

// --- Thực hiện xóa sinh viên khỏi CSDL ---
if (deleteStudent($mysqli, $maSV)) {
    // Xóa thành công khỏi CSDL
    $message = "Xóa sinh viên thành công!";

    // --- Xóa file ảnh trên server nếu có ---
    if ($hinhPathToDelete !== null) {
        $fullPath = __DIR__ . '/../' . $hinhPathToDelete;
        if (file_exists($fullPath)) {
            // Cố gắng xóa file, @ để ẩn lỗi nếu không xóa được (ví dụ do permission)
            if (!@unlink($fullPath)) {
                // Có thể ghi log lỗi nếu cần theo dõi việc xóa file thất bại
                error_log("Could not delete image file: " . $fullPath);
                // Có thể thêm ghi chú vào message cho người dùng biết
                // $message .= " (Lưu ý: Không thể xóa file ảnh trên server.)";
            }
        }
    }
    // Chuyển hướng về trang danh sách với thông báo thành công
    header("Location: index.php?message=" . urlencode($message));
    exit;

} else {
    // Xóa thất bại (có thể do lỗi DB, hoặc ràng buộc khóa ngoại - Foreign Key Constraint)
    // Ví dụ: Nếu sinh viên đã đăng ký học phần (có bản ghi trong DangKy), CSDL có thể chặn xóa.
    // Cần xem xét lại logic hoặc thông báo lỗi phù hợp hơn.
    header("Location: index.php?error=" . urlencode("Lỗi khi xóa sinh viên. Có thể do sinh viên đã có dữ liệu liên quan (đăng ký học phần) hoặc lỗi cơ sở dữ liệu."));
    exit;
}

// Đóng kết nối CSDL (Thường không chạy đến đây vì có exit ở trên)
db_close($mysqli);
?>