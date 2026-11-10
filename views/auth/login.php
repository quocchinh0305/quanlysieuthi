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
    <title>Đăng nhập - Quản lý Siêu thị</title>
</head>
<body>

    <h3>ĐĂNG NHẬP HỆ THỐNG</h3>

    <?php if(isset($_SESSION['error'])): ?>
        <p><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['success'])): ?>
        <p><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form action="../../controller/XacThucController.php?action=login" method="POST">
        <label>Địa chỉ Email</label><br>
        <input type="email" name="email" required placeholder="Nhập email...">
        <br><br>
        <label>Mật khẩu</label><br>
        <input type="password" name="matkhau" required placeholder="Nhập mật khẩu...">
        <br><br>
        <button type="submit">Đăng Nhập</button>
    </form>

    <p>
        Chưa có tài khoản? 
        <a href="dangky.php">Đăng ký ngay</a>
    </p>

</body>
</html>