<?php
// src/NganhHoc.php

/**
 * Lấy tất cả các ngành học từ CSDL
 * @param mysqli $mysqli Đối tượng kết nối mysqli
 * @return array Mảng chứa các ngành học hoặc mảng rỗng nếu có lỗi/không có dữ liệu
 */
function getAllNganhHoc(mysqli $mysqli): array
{
    $nganhHocList = [];
    $sql = "SELECT MaNganh, TenNganh FROM NganhHoc ORDER BY TenNganh";
    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $nganhHocList[] = $row;
        }
        $result->free(); // Giải phóng bộ nhớ
    } else {
        error_log("Error executing query: " . $mysqli->error);
        // Xử lý lỗi ở đây nếu cần
    }
    return $nganhHocList;
}
?>