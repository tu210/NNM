<?php
// includes/footer.php
// Đảm bảo không có khoảng trắng hoặc ký tự lạ trước <?php (nếu có thẻ mở php)
?>
</div>
<footer class="mt-4 text-center text-muted">
    <p>&copy; <?php echo date('Y'); ?> Quản Lý Sinh Viên</p>
    <?php
    // Bạn có thể thêm thông tin khác vào đây nếu muốn
    // Ví dụ: echo "<p>Liên hệ: abc@example.com</p>";
    ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

<?php /* Bỏ comment nếu bạn có file này
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
*/ ?>

<?php
// Đoạn code đóng kết nối CSDL có thể đặt ở đây hoặc cuối các file xử lý chính
// Tuy nhiên, PHP thường tự đóng kết nối khi script kết thúc, nên có thể không bắt buộc.
/*
global $mysqli; // Cần khai báo global nếu $mysqli không trong scope này
if (isset($mysqli) && $mysqli instanceof mysqli) {
    $mysqli->close();
}
*/
?>
</body>

</html>