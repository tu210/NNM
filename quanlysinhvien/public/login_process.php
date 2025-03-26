<?php
// public/login_process.php

// Luôn bắt đầu session ở đầu file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nạp các file cần thiết
require_once __DIR__ . '/../config/database.php'; // Kết nối CSDL
require_once __DIR__ . '/../src/SinhVien.php';   // Chứa hàm getStudentById

// Chỉ xử lý khi request là POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy MaSV từ form và làm sạch cơ bản
    $maSV = trim($_POST['MaSV'] ?? '');

    // --- Validation cơ bản ---
    if (empty($maSV)) {
        // Nếu MaSV trống, chuyển hướng về trang login với thông báo lỗi
        header("Location: login.php?error=" . urlencode("Vui lòng nhập Mã Sinh Viên."));
        exit;
    }

    // --- Kiểm tra sự tồn tại của Sinh Viên trong CSDL ---
    // Sử dụng hàm getStudentById đã có trong src/SinhVien.php
    $student = getStudentById($mysqli, $maSV);

    // Đóng kết nối CSDL sau khi kiểm tra xong
    db_close($mysqli);

    // --- Xử lý kết quả ---
    if ($student !== null) {
        // === Đăng nhập thành công ===
        // Sinh viên tồn tại -> Lưu thông tin vào Session

        // Hủy bỏ session cũ (nếu có) và tạo session ID mới để tăng bảo mật
        session_regenerate_id(true);

        $_SESSION['MaSV'] = $student['MaSV'];
        $_SESSION['HoTen'] = $student['HoTen']; // Lưu cả Họ Tên để hiển thị lời chào
        // Bạn có thể lưu thêm thông tin khác nếu cần, ví dụ: MaNganh

        // Chuyển hướng đến trang mong muốn sau khi đăng nhập thành công
        // Ví dụ: chuyển đến trang danh sách học phần
        header("Location: hocphan/index.php");
        exit;

    } else {
        // === Đăng nhập thất bại ===
        // Sinh viên không tồn tại -> Chuyển hướng về trang login với thông báo lỗi
        header("Location: login.php?error=" . urlencode("Mã sinh viên không tồn tại hoặc không đúng."));
        exit;
    }

} else {
    // Nếu không phải POST request, chuyển về trang đăng nhập
    header("Location: login.php");
    exit;
}
?>