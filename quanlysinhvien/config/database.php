<?php
// config/database.php

define('DB_SERVER', 'localhost'); // Hoặc IP/hostname của MySQL server
define('DB_USERNAME', 'root');    // Username kết nối MySQL
define('DB_PASSWORD', '');        // Password kết nối MySQL (để trống nếu không có)
define('DB_NAME', 'Test1');       // Tên database

// Tạo kết nối
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if ($mysqli->connect_error) {
    // Không nên hiển thị lỗi chi tiết trên production
    // Ghi log lỗi hoặc hiển thị thông báo chung chung
    error_log("Connection failed: " . $mysqli->connect_error);
    die("Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.");
}

// Thiết lập charset UTF-8 để làm việc với tiếng Việt
if (!$mysqli->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $mysqli->error);
    // Có thể không cần die ở đây nếu ứng dụng vẫn chạy được phần nào
}

// Hàm tiện ích để đóng kết nối (có thể gọi ở cuối các script hoặc trong footer)
function db_close($mysqli)
{
    if ($mysqli) {
        $mysqli->close();
    }
}

?>