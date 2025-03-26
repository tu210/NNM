<?php
// public/logout.php

// Luôn bắt đầu session để có thể truy cập và hủy nó
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Bước 1: Hủy tất cả các biến session.
$_SESSION = array();

// Bước 2: Nếu sử dụng session cookie (thường là mặc định), hủy cả cookie đó.
// Điều này quan trọng để đảm bảo đăng xuất hoàn toàn.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // Đặt thời gian hết hạn về quá khứ
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Bước 3: Hủy bỏ session.
session_destroy();

// Bước 4: Chuyển hướng người dùng về trang đăng nhập.
// Đảm bảo rằng đường dẫn này đúng. Nếu logout.php và login.php cùng cấp trong public/
// thì chỉ cần tên file là đủ.
header("Location: login.php");
exit; // Dừng script ngay sau khi chuyển hướng

?>