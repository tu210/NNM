<?php
// public/hocphan/index.php

// Nạp các file cần thiết
require_once __DIR__ . '/../../config/database.php'; // Kết nối CSDL
require_once __DIR__ . '/../../src/HocPhan.php';   // Chứa hàm getAllHocPhan
require_once __DIR__ . '/../../includes/header.php'; // Nạp phần đầu trang HTML và menu

// Lấy danh sách tất cả học phần từ CSDL
$hocPhanList = getAllHocPhan($mysqli); // Gọi hàm từ src/HocPhan.php

// Đặt tiêu đề cho trang web (sẽ hiển thị trên tab trình duyệt)
$page_title = 'Danh sách Học Phần';

?>

<h1>DANH SÁCH HỌC PHẦN</h1>

<?php
// Hiển thị thông báo nếu có (ví dụ: sau khi đăng ký thành công/thất bại)
// Các thông báo này sẽ được truyền qua URL từ trang xử lý đăng ký
if (isset($_GET['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Mã Học Phần</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th class="text-center">Đăng ký</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($hocPhanList)): // Kiểm tra xem có học phần nào không ?>
            <?php foreach ($hocPhanList as $hocPhan): // Lặp qua từng học phần trong danh sách ?>
                <tr>
                    <td><?php echo htmlspecialchars($hocPhan['MaHP']); ?></td>
                    <td><?php echo htmlspecialchars($hocPhan['TenHP']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($hocPhan['SoTinChi']); ?></td>
                    <td class="text-center">
                        <form method="POST" action="../dangky_process.php" style="margin: 0; display: inline;">
                            <input type="hidden" name="MaHP" value="<?php echo htmlspecialchars($hocPhan['MaHP']); ?>">

                            <button type="submit" class="btn btn-sm btn-success">Đăng ký</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: // Nếu không có học phần nào ?>
            <tr>
                <td colspan="4" class="text-center">Hiện không có học phần nào trong danh sách.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// Nạp phần chân trang HTML
require_once __DIR__ . '/../../includes/footer.php';

// Đóng kết nối CSDL
if (isset($mysqli)) {
    db_close($mysqli);
}
?>