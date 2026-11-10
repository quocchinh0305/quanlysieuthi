<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ của tôi - VQC</title>
</head>
<body>

    <div>
        <a href="/quanlysieuthi/views/customer/dashboard.php">&larr; Quay lại trang chủ</a> | 
        <a href="/quanlysieuthi/controller/ThanhToanController.php?action=lich_su">Lịch sử đơn hàng</a> | 
        <a href="/quanlysieuthi/controller/ThanhToanController.php?action=index">Giỏ hàng</a> | 
        <a href="/quanlysieuthi/controller/KhachHangController.php?action=dang_xuat" style="color: red;">Đăng xuất</a>
    </div>

    <hr>

    <main>
        <h3>Hồ Sơ Của Tôi</h3>
        <p>Quản lý thông tin profile và bảo mật tài khoản cá nhân</p>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <p style="color: green; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['success_msg']); unset($_SESSION['success_msg']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_msg'])): ?>
            <p style="color: red; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['error_msg']); unset($_SESSION['error_msg']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($khachhang) && $khachhang): ?>
            <form action="/quanlysieuthi/controller/KhachHangController.php?action=update_tai_khoan" method="POST">
                
                <p>
                    <label><strong>Họ và tên:</strong></label><br>
                    <input type="text" name="hoten" value="<?= htmlspecialchars($khachhang['hoten']); ?>" required>
                </p>

                <p>
                    <label><strong>Email:</strong></label><br>
                    <span><?= htmlspecialchars($khachhang['email'] ?? 'Chưa cập nhật'); ?></span>
                </p>

                <p>
                    <label><strong>Số điện thoại:</strong></label><br>
                    <input type="text" name="sodienthoai" value="<?= htmlspecialchars($khachhang['sodienthoai'] ?? ''); ?>">
                </p>

                <p>
                    <label><strong>Địa chỉ:</strong></label><br>
                    <input type="text" name="diachi" value="<?= htmlspecialchars($khachhang['diachi'] ?? ''); ?>" style="width: 300px;">
                </p>

                <p>
                    <label><strong>Hạng thành viên:</strong></label><br>
                    <span><?= htmlspecialchars($khachhang['hangthanhvien'] ?? 'Thường'); ?></span>
                </p>

                <p>
                    <button type="submit">Lưu</button>
                </p>
            </form>

            <hr>

            <p>
                <a href="/quanlysieuthi/controller/KhachHangController.php?action=deleteTaiKhoan" 
                   onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản này không? Mọi dữ liệu sẽ mất hoàn toàn!');"
                   style="color: red;">
                   <u>Xóa tài khoản vĩnh viễn</u>
                </a>
            </p>

        <?php else: ?>
            <p>Không tìm thấy thông tin tài khoản.</p>
        <?php endif; ?>
    </main>

</body>
</html>