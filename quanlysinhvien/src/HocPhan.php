<?php
// src/HocPhan.php

/**
 * Lấy tất cả các học phần từ CSDL
 * @param mysqli $mysqli Đối tượng kết nối mysqli
 * @return array Mảng chứa các học phần hoặc mảng rỗng nếu có lỗi/không có dữ liệu
 */
function getAllHocPhan(mysqli $mysqli): array
{
    $hocPhanList = [];
    $sql = "SELECT MaHP, TenHP, SoTinChi FROM HocPhan ORDER BY TenHP"; // Sắp xếp theo tên cho dễ nhìn

    // Thực thi truy vấn
    if ($result = $mysqli->query($sql)) {
        // Lặp qua kết quả và thêm vào mảng
        while ($row = $result->fetch_assoc()) {
            $hocPhanList[] = $row;
        }
        $result->free(); // Giải phóng bộ nhớ kết quả
    } else {
        // Ghi lại lỗi nếu truy vấn thất bại (quan trọng để debug)
        error_log("Lỗi khi thực thi truy vấn lấy học phần: " . $mysqli->error);
        // Bạn có thể thêm xử lý lỗi khác ở đây nếu muốn, ví dụ: trả về false hoặc throw exception
    }

    return $hocPhanList; // Trả về mảng học phần
}

/** * Lấy thông tin một học phần bằng MaHP (Hàm này có thể hữu ích sau này)
 * @param mysqli $mysqli
 * @param string $maHP
 * @return array|null Thông tin học phần hoặc null nếu không tìm thấy/lỗi
 */
/* // Bạn có thể bỏ comment khối này nếu cần dùng hàm này trong tương lai
function getHocPhanById(mysqli $mysqli, string $maHP): ?array {
    $hocPhan = null;
    $sql = "SELECT MaHP, TenHP, SoTinChi FROM HocPhan WHERE MaHP = ?";
    
    // Sử dụng prepared statement để tránh SQL injection
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $maHP); // "s" nghĩa là tham số là string (chuỗi)
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                // Tìm thấy học phần
                $hocPhan = $result->fetch_assoc();
            }
            $result->free();
        } else {
            // Lỗi khi thực thi statement
            error_log("Lỗi khi thực thi statement lấy học phần theo ID: " . $stmt->error);
        }
        $stmt->close(); // Đóng statement
    } else {
        // Lỗi khi chuẩn bị statement
         error_log("Lỗi khi chuẩn bị statement lấy học phần theo ID: " . $mysqli->error);
    }
    
    return $hocPhan;
}
*/

?>