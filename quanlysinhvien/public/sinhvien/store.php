<?php
// public/sinhvien/store.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/SinhVien.php';

// Chỉ xử lý khi request là POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ form và làm sạch cơ bản
    $maSV = trim($_POST['MaSV'] ?? '');
    $hoTen = trim($_POST['HoTen'] ?? '');
    $gioiTinh = $_POST['GioiTinh'] ?? 'Nam'; // Mặc định là Nam nếu không chọn
    $ngaySinh = $_POST['NgaySinh'] ?? null; // Ngày sinh có thể để trống
    $maNganh = $_POST['MaNganh'] ?? '';

    // --- Validation cơ bản ---
    $errors = [];
    if (empty($maSV)) {
        $errors[] = "Mã sinh viên là bắt buộc.";
    } elseif (strlen($maSV) > 10) {
        $errors[] = "Mã sinh viên không được quá 10 ký tự.";
    }
    // TODO: Nên kiểm tra xem MaSV đã tồn tại chưa (cần thêm hàm check trong SinhVien.php)
    /*
    if (isStudentExists($mysqli, $maSV)) {
         $errors[] = "Mã sinh viên '$maSV' đã tồn tại.";
    }
    */

    if (empty($hoTen)) {
        $errors[] = "Họ tên là bắt buộc.";
    }
    if (empty($maNganh)) {
        $errors[] = "Vui lòng chọn ngành học.";
    }
    if (!in_array($gioiTinh, ['Nam', 'Nữ', 'Khác'])) { // Đảm bảo giá trị hợp lệ
        $errors[] = "Giới tính không hợp lệ.";
    }

    // Định dạng lại ngày sinh thành YYYY-MM-DD nếu nó không rỗng
    // Mặc dù type="date" thường gửi đúng định dạng, kiểm tra lại cho chắc
    if (!empty($ngaySinh)) {
        try {
            $dateObj = new DateTime($ngaySinh);
            $ngaySinhFormatted = $dateObj->format('Y-m-d');
        } catch (Exception $e) {
            $errors[] = "Định dạng ngày sinh không hợp lệ.";
            $ngaySinhFormatted = null; // Đặt lại nếu lỗi
        }
    } else {
        $ngaySinhFormatted = null; // Cho phép NULL nếu không nhập
    }

    // --- Xử lý upload file hình ảnh ---
    $hinhPath = null; // Đường dẫn lưu vào DB, mặc định là null
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Hinh']['tmp_name'];
        $fileName = $_FILES['Hinh']['name'];
        $fileSize = $_FILES['Hinh']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Kiểm tra loại file
        if (!in_array($fileExtension, $allowedTypes)) {
            $errors[] = "Loại file không hợp lệ. Chỉ chấp nhận JPG, JPEG, PNG, GIF.";
        }

        // Kiểm tra kích thước file
        if ($fileSize > $maxFileSize) {
            $errors[] = "Kích thước file quá lớn. Tối đa là 5MB.";
        }

        // Nếu không có lỗi validation file
        if (empty($errors)) {
            $targetDir = __DIR__ . '/../uploads/sinhvien/'; // Thư mục lưu ảnh
            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    $errors[] = "Không thể tạo thư mục lưu trữ file.";
                    // Không nên để 0777 trong production, chỉ dùng khi test local nếu cần
                }
            }

            if (empty($errors)) { // Kiểm tra lại lỗi sau khi tạo thư mục
                $uniqueName = uniqid('sv_', true) . '.' . $fileExtension; // Tạo tên file duy nhất
                $targetFilePath = $targetDir . $uniqueName;

                // Di chuyển file upload vào thư mục đích
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    // Lưu đường dẫn tương đối vào DB (tính từ thư mục public/)
                    $hinhPath = 'uploads/sinhvien/' . $uniqueName;
                } else {
                    $errors[] = "Có lỗi xảy ra khi tải file lên.";
                }
            }
        }
    } elseif (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Nếu có lỗi upload khác ngoài việc không chọn file
        $errors[] = "Có lỗi xảy ra với file tải lên: " . $_FILES['Hinh']['error'];
    }


    // --- Nếu có lỗi validation thì quay lại trang create ---
    if (!empty($errors)) {
        // Chuyển hướng về trang create với thông báo lỗi
        $errorString = implode('<br>', $errors);
        header("Location: create.php?error=" . urlencode($errorString));
        exit;
    }

    // --- Nếu không có lỗi, chuẩn bị dữ liệu và thêm vào CSDL ---
    $studentData = [
        'MaSV' => $maSV,
        'HoTen' => $hoTen,
        'GioiTinh' => $gioiTinh,
        'NgaySinh' => $ngaySinhFormatted, // Đã định dạng YYYY-MM-DD hoặc là null
        'Hinh' => $hinhPath,             // Đường dẫn ảnh hoặc null
        'MaNganh' => $maNganh
    ];

    // Gọi hàm thêm sinh viên
    if (addStudent($mysqli, $studentData)) {
        // Thêm thành công, chuyển hướng về trang danh sách với thông báo
        header("Location: index.php?message=" . urlencode("Thêm sinh viên thành công!"));
        exit;
    } else {
        // Có lỗi khi thêm vào CSDL
        // Xóa file ảnh đã upload nếu có lỗi DB
        if ($hinhPath && file_exists(__DIR__ . '/../' . $hinhPath)) {
            unlink(__DIR__ . '/../' . $hinhPath);
        }
        header("Location: create.php?error=" . urlencode("Lỗi khi thêm sinh viên vào cơ sở dữ liệu."));
        exit;
    }

} else {
    // Nếu không phải POST request, chuyển về trang danh sách
    header("Location: index.php");
    exit;
}

// Đóng kết nối CSDL (Mặc dù các lệnh exit ở trên sẽ dừng script trước khi đến đây)
db_close($mysqli);
?>