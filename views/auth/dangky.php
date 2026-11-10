<?php
$thoigianSession = 604800;
session_set_cookie_params($thoigianSession);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Quản lý Siêu thị</title>
</head>
<body>

    <h3>ĐĂNG KÝ TÀI KHOẢN KHÁCH HÀNG</h3>

    <?php if(isset($_SESSION['register_error'])): ?>
        <p><?= htmlspecialchars($_SESSION['register_error']); unset($_SESSION['register_error']); ?></p>
    <?php endif; ?>

    <form action="../../controller/XacThucController.php?action=register" method="POST">
        <label>Họ và tên</label><br>
        <input type="text" name="hoten" required placeholder="Ví dụ: Nguyễn Văn A">
        <br><br>

        <label>Email đăng nhập</label><br>
        <input type="email" name="email" required placeholder="email@gmail.com">
        <br><br>

        <label>Mật khẩu</label><br>
        <input type="password" name="matkhau" required placeholder="Mật khẩu tài khoản">
        <br><br>

        <label>Số điện thoại</label><br>
        <input type="text" name="sodienthoai" required placeholder="0912345678">
        <br><br>

        <label>Địa chỉ giao hàng</label><br>
        <textarea name="diachi" placeholder="Số nhà, tên đường..."></textarea>
        <br><br>

        <button type="submit">Hoàn Tất Đăng Ký</button>
    </form>

    <p>
        Đã có tài khoản? 
        <a href="dangnhap.php">Đăng nhập ngay</a>
    </p>

</body>
</html>