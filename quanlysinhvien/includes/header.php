<?php
// ĐẶT NGAY DÒNG ĐẦU TIÊN CỦA FILE, TRƯỚC MỌI THỨ KHÁC
if (session_status() == PHP_SESSION_NONE) { // Chỉ start nếu session chưa được start
    session_start();
}
define('BASE_URL', 'http://localhost/quanlysinhvien/public/'); // Hoặc '/TenDuAnCuaBan/public/' nếu chạy trong thư mục con


?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Quản Lý Sinh Viên'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">Test1</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>sinhvien/index.php">Sinh Viên</a>
                        <?php // Có thể thêm class 'active' dựa trên trang hiện tại nếu muốn ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>hocphan/index.php">Học Phần</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Đăng Ký</a>
                        <?php // Sẽ cập nhật link này sau ?>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['MaSV'])): // Kiểm tra xem người dùng đã đăng nhập chưa ?>
                        <li class="nav-item">
                            <span class="nav-link"> Chào,
                                <?php echo htmlspecialchars($_SESSION['HoTen'] ?? $_SESSION['MaSV']); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>logout.php">Đăng Xuất</a>
                        </li>
                    <?php else: // Nếu chưa đăng nhập ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>login.php">Đăng Nhập</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
        <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>

        <?php
        // Đóng kết nối CSDL nếu nó đã được mở
// Cần đảm bảo biến $mysqli tồn tại trong scope này
// Cách tốt hơn là quản lý kết nối trong các lớp hoặc hàm cụ thể
// if (isset($mysqli)) {
//     db_close($mysqli);
// }
        ?>
</body>

</html>