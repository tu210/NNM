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

// Chỉ xử lý khi request là POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Lấy dữ liệu từ form
    $maSV = $_POST['MaSV'] ?? ''; // Lấy từ trường hidden
    $hoTen = trim($_POST['HoTen'] ?? '');
    $gioiTinh = $_POST['GioiTinh'] ?? 'Nam';
    $ngaySinh = $_POST['NgaySinh'] ?? null;
    $maNganh = $_POST['MaNganh'] ?? '';
    $currentHinh = $_POST['current_hinh'] ?? null; // Lấy đường dẫn ảnh cũ

    // --- Validation cơ bản ---
    $errors = [];
    if (empty($maSV)) { // Kiểm tra MaSV ẩn có được gửi không
        $errors[] = "Mã sinh viên không hợp lệ.";
        // Chuyển hướng sớm nếu không có MaSV
        header("Location: index.php?error=" . urlencode(implode('<br>', $errors)));
        exit;
    }
    if (empty($hoTen)) {
        $errors[] = "Họ tên là bắt buộc.";
    }
    if (empty($maNganh)) {
        $errors[] = "Vui lòng chọn ngành học.";
    }
    if (!in_array($gioiTinh, ['Nam', 'Nữ', 'Khác'])) {
        $errors[] = "Giới tính không hợp lệ.";
    }

    // Định dạng lại ngày sinh
    $ngaySinhFormatted = null;
    if (!empty($ngaySinh)) {
        try {
            $dateObj = new DateTime($ngaySinh);
            $ngaySinhFormatted = $dateObj->format('Y-m-d');
        } catch (Exception $e) {
            $errors[] = "Định dạng ngày sinh không hợp lệ.";
        }
    }

    // --- Xử lý upload file hình ảnh MỚI (nếu có) ---
    $hinhPath = $currentHinh; // Bắt đầu bằng ảnh cũ
    $newHinhUploaded = false; // Cờ để biết có ảnh mới được upload thành công không
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    // Kiểm tra xem có file MỚI được tải lên không
    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Hinh']['tmp_name'];
        $fileName = $_FILES['Hinh']['name'];
        $fileSize = $_FILES['Hinh']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Kiểm tra loại file và kích thước cho file MỚI
        if (!in_array($fileExtension, $allowedTypes)) {
            $errors[] = "Loại file mới không hợp lệ. Chỉ chấp nhận JPG, JPEG, PNG, GIF.";
        }
        if ($fileSize > $maxFileSize) {
            $errors[] = "Kích thước file mới quá lớn. Tối đa là 5MB.";
        }

        // Nếu không có lỗi validation file MỚI
        if (empty($errors)) {
            $targetDir = __DIR__ . '/../uploads/sinhvien/';
            // Không cần tạo lại thư mục vì nó đã tồn tại từ lúc thêm
            $uniqueName = uniqid('sv_', true) . '.' . $fileExtension;
            $targetFilePath = $targetDir . $uniqueName;

            // Di chuyển file MỚI upload
            if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                // Xóa file ảnh CŨ nếu upload file MỚI thành công và ảnh cũ tồn tại
                if (!empty($currentHinh) && file_exists(__DIR__ . '/../' . $currentHinh)) {
                    @unlink(__DIR__ . '/../' . $currentHinh); // @ để ẩn lỗi nếu không xóa được
                }
                // Cập nhật đường dẫn ảnh thành ảnh MỚI
                $hinhPath = 'uploads/sinhvien/' . $uniqueName;
                $newHinhUploaded = true; // Đánh dấu đã upload ảnh mới thành công
            } else {
                $errors[] = "Có lỗi xảy ra khi tải file mới lên.";
            }
        }
    } elseif (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Nếu có lỗi upload khác (không phải là không chọn file)
        $errors[] = "Có lỗi xảy ra với file tải lên: " . $_FILES['Hinh']['error'];
    }

    // --- Nếu có lỗi validation thì quay lại trang edit ---
    if (!empty($errors)) {
        // Chuyển hướng về trang edit với thông báo lỗi
        $errorString = implode('<br>', $errors);
        header("Location: edit.php?id=" . $maSV . "&error=" . urlencode($errorString));
        exit;
    }

    // --- Chuẩn bị dữ liệu để cập nhật ---
    $studentData = [
        'HoTen' => $hoTen,
        'GioiTinh' => $gioiTinh,
        'NgaySinh' => $ngaySinhFormatted,
        'MaNganh' => $maNganh
        // Không cần thêm 'MaSV' vào đây vì nó được dùng trong WHERE
    ];

    // Chỉ thêm 'Hinh' vào dữ liệu nếu có ảnh mới được upload thành công HOẶC nếu bạn muốn cập nhật cả khi không đổi ảnh
    // Cách hàm updateStudent đang viết sẽ chỉ cập nhật Hinh nếu $data['Hinh'] được truyền vào và không rỗng
    // Do đó, chỉ cần truyền $hinhPath (là đường dẫn mới hoặc cũ)
    $studentData['Hinh'] = $hinhPath;


    // --- Gọi hàm cập nhật sinh viên ---
    if (updateStudent($mysqli, $maSV, $studentData)) {
        // Cập nhật thành công
        header("Location: index.php?message=" . urlencode("Cập nhật thông tin sinh viên thành công!"));
        exit;
    } else {
        // Lỗi khi cập nhật CSDL
        // Nếu có upload ảnh mới thành công nhưng lỗi DB, cân nhắc xóa ảnh mới upload
        if ($newHinhUploaded && file_exists(__DIR__ . '/../' . $hinhPath)) {
            @unlink(__DIR__ . '/../' . $hinhPath);
        }
        header("Location: edit.php?id=" . $maSV . "&error=" . urlencode("Lỗi khi cập nhật thông tin sinh viên vào cơ sở dữ liệu."));
        exit;
    }

} else {
    // Nếu không phải POST request
    header("Location: index.php");
    exit;
}

// Đóng kết nối CSDL
db_close($mysqli);
?>