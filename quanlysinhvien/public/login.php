<?php
// public/login.php

// Nếu người dùng đã đăng nhập rồi thì chuyển hướng họ đi chỗ khác (vd: trang học phần)
// Cần session_start() trước khi truy cập $_SESSION
session_start();
if (isset($_SESSION['MaSV'])) {
    // Giả sử BASE_URL đã được định nghĩa nếu include header, nếu không thì cần định nghĩa ở đây
    // Hoặc dùng đường dẫn tương đối/tuyệt đối cố định
    header('Location: hocphan/index.php'); // Chuyển đến trang học phần nếu đã đăng nhập
    exit;
}

// Đặt tiêu đề trang
$page_title = 'Đăng Nhập';

// Nạp header (header này cũng cần được cập nhật để có session_start())
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <h2 class="text-center mb-4">ĐĂNG NHẬP</h2>

        <?php
        // Hiển thị thông báo lỗi nếu có (từ trang xử lý hoặc trang khác chuyển về)
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>

        <div class="form-container">
            <form method="POST" action="login_process.php">
                <div class="mb-3">
                    <label for="MaSV" class="form-label">Mã Sinh Viên:</label>
                    <input type="text" class="form-control" id="MaSV" name="MaSV" required autofocus>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Đăng Nhập</button>
                </div>

                <div class="text-center mt-3">
                    <a href="sinhvien/index.php">Back to List</a>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
// Nạp footer
require_once __DIR__ . '/../includes/footer.php';
?>