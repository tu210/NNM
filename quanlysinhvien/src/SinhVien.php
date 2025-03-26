<?php
// src/SinhVien.php

/**
 * Lấy tất cả sinh viên kèm tên ngành học
 * @param mysqli $mysqli
 * @return array
 */
function getAllStudentsWithNganh(mysqli $mysqli): array
{
    $students = [];
    $sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, sv.MaNganh, nh.TenNganh
            FROM SinhVien sv
            LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
            ORDER BY sv.HoTen";

    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        $result->free();
    } else {
        error_log("Error executing query: " . $mysqli->error);
    }
    return $students;
}

/**
 * Lấy thông tin chi tiết của một sinh viên bằng MaSV
 * @param mysqli $mysqli
 * @param string $maSV
 * @return array|null Trả về mảng thông tin sinh viên hoặc null nếu không tìm thấy/lỗi
 */
function getStudentById(mysqli $mysqli, string $maSV): ?array
{
    $student = null;
    $sql = "SELECT sv.MaSV, sv.HoTen, sv.GioiTinh, sv.NgaySinh, sv.Hinh, sv.MaNganh, nh.TenNganh
            FROM SinhVien sv
            LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
            WHERE sv.MaSV = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $maSV);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $student = $result->fetch_assoc();
            }
            $result->free();
        } else {
            error_log("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $mysqli->error);
    }
    return $student;
}

/**
 * Thêm sinh viên mới vào CSDL
 * @param mysqli $mysqli
 * @param array $data Mảng chứa dữ liệu sinh viên ['MaSV', 'HoTen', 'GioiTinh', 'NgaySinh', 'Hinh', 'MaNganh']
 * @return bool True nếu thành công, False nếu thất bại
 */
function addStudent(mysqli $mysqli, array $data): bool
{
    $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        // Sửa 'ssssss' nếu kiểu dữ liệu không đúng (vd: NgaySinh là date)
        $stmt->bind_param(
            "ssssss",
            $data['MaSV'],
            $data['HoTen'],
            $data['GioiTinh'],
            $data['NgaySinh'], // Đảm bảo định dạng 'YYYY-MM-DD'
            $data['Hinh'],
            $data['MaNganh']
        );

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Error executing insert statement: " . $stmt->error);
            $stmt->close();
            return false;
        }
    } else {
        error_log("Error preparing insert statement: " . $mysqli->error);
        return false;
    }
}

/**
 * Cập nhật thông tin sinh viên
 * @param mysqli $mysqli
 * @param string $maSV MaSV của sinh viên cần cập nhật
 * @param array $data Dữ liệu mới ['HoTen', 'GioiTinh', 'NgaySinh', 'Hinh', 'MaNganh'] (Hinh có thể null nếu không đổi)
 * @return bool
 */
function updateStudent(mysqli $mysqli, string $maSV, array $data): bool
{
    // Nếu không có hình mới, không cập nhật cột Hinh
    if (!empty($data['Hinh'])) {
        $sql = "UPDATE SinhVien SET HoTen=?, GioiTinh=?, NgaySinh=?, Hinh=?, MaNganh=? WHERE MaSV=?";
    } else {
        $sql = "UPDATE SinhVien SET HoTen=?, GioiTinh=?, NgaySinh=?, MaNganh=? WHERE MaSV=?";
    }


    if ($stmt = $mysqli->prepare($sql)) {
        if (!empty($data['Hinh'])) {
            $stmt->bind_param(
                "ssssss", // ssssss nếu có Hinh
                $data['HoTen'],
                $data['GioiTinh'],
                $data['NgaySinh'], // Định dạng 'YYYY-MM-DD'
                $data['Hinh'],
                $data['MaNganh'],
                $maSV
            );
        } else {
            $stmt->bind_param(
                "sssss", // sssss nếu không có Hinh
                $data['HoTen'],
                $data['GioiTinh'],
                $data['NgaySinh'], // Định dạng 'YYYY-MM-DD'
                $data['MaNganh'],
                $maSV
            );
        }


        if ($stmt->execute()) {
            // Kiểm tra xem có dòng nào thực sự được cập nhật không
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            // Trả về true nếu có ít nhất 1 dòng bị ảnh hưởng hoặc không có lỗi
            // (Lưu ý: submit form mà không thay đổi gì cũng coi là thành công về mặt kỹ thuật)
            return $affected_rows >= 0;
        } else {
            error_log("Error executing update statement: " . $stmt->error);
            $stmt->close();
            return false;
        }
    } else {
        error_log("Error preparing update statement: " . $mysqli->error);
        return false;
    }
}


/**
 * Xóa sinh viên khỏi CSDL
 * @param mysqli $mysqli
 * @param string $maSV
 * @return bool True nếu xóa thành công, False nếu thất bại
 */
function deleteStudent(mysqli $mysqli, string $maSV): bool
{
    // Cẩn thận: Nên kiểm tra xem sinh viên có dữ liệu liên quan ở bảng khác không trước khi xóa
    // Ví dụ: kiểm tra bảng DangKy
    // Tạm thời xóa trực tiếp

    $sql = "DELETE FROM SinhVien WHERE MaSV = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $maSV);
        if ($stmt->execute()) {
            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            // Trả về true nếu có đúng 1 dòng bị xóa
            return $affected_rows === 1;
        } else {
            error_log("Error executing delete statement: " . $stmt->error);
            $stmt->close();
            return false;
        }
    } else {
        error_log("Error preparing delete statement: " . $mysqli->error);
        return false;
    }
}
?>