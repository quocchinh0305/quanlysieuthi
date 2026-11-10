<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../config/payment.php";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Thành Công</title>
</head>
<body>

    <h2>Đặt Hàng Thành Công!</h2>
    <hr>

    <?php if ($order): ?>
        <h3>Thông tin đơn hàng</h3>
        <p><b>Mã đơn hàng:</b> <?= htmlspecialchars($order['id']); ?></p>
        <p><b>Họ tên người nhận:</b> <?= htmlspecialchars($order['ten_nguoinhan']); ?></p>
        <p><b>Số điện thoại:</b> <?= htmlspecialchars($order['sdt_nguoinhan']); ?></p>
        <p><b>Địa chỉ nhận:</b> <?= htmlspecialchars($order['diachi']); ?></p>
        <p><b>Hình thức thanh toán:</b> <?= ($order['phuongthuc_thanhtoan'] == 'tienmat') ? 'Tiền mặt (COD)' : 'Chuyển khoản'; ?></p>
        <p><b>Trạng thái thanh toán:</b> <?= $order['trangthai_thanhtoan'] === 'dathanhtoan' ? 'Đã thanh toán' : 'Chưa thanh toán'; ?></p>

        <?php if ($order['phuongthuc_thanhtoan'] === 'chuyenkhoan' && $order['trangthai_thanhtoan'] === 'chuathanhtoan'): ?>
            <?php
            $noi_dung_chuyen_khoan = 'Thanh toan ' . $order['id'];
            $qr_url = 'https://img.vietqr.io/image/'
                . rawurlencode($thong_tin_nhan_chuyen_khoan['ma_ngan_hang'])
                . '-' . rawurlencode($thong_tin_nhan_chuyen_khoan['so_tai_khoan'])
                . '-compact2.png?amount=' . rawurlencode((string) (int) $order['tongtien'])
                . '&addInfo=' . rawurlencode($noi_dung_chuyen_khoan)
                . '&accountName=' . rawurlencode($thong_tin_nhan_chuyen_khoan['chu_tai_khoan']);
            ?>
            <h3>Quét mã QR để chuyển khoản</h3>
            <p>Ngân hàng: <?= htmlspecialchars($thong_tin_nhan_chuyen_khoan['ten_ngan_hang']); ?></p>
            <p>Số tài khoản: <?= htmlspecialchars($thong_tin_nhan_chuyen_khoan['so_tai_khoan']); ?></p>
            <p>Chủ tài khoản: <?= htmlspecialchars($thong_tin_nhan_chuyen_khoan['chu_tai_khoan']); ?></p>
            <p>Số tiền: <b><?= number_format($order['tongtien'], 0, ',', '.'); ?>đ</b></p>
            <p>Nội dung chuyển khoản: <b><?= htmlspecialchars($noi_dung_chuyen_khoan); ?></b></p>
            <img src="<?= htmlspecialchars($qr_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Mã QR chuyển khoản" width="260" height="260">
            <p>Đơn hàng sẽ được cập nhật đã thanh toán sau khi nhân viên xác nhận đã nhận tiền.</p>
        <?php elseif ($order['phuongthuc_thanhtoan'] === 'tienmat'): ?>
            <p>Vui lòng thanh toán khi nhận hàng. Nhân viên sẽ cập nhật trạng thái sau khi nhận tiền.</p>
        <?php endif; ?>

        <h3>Chi tiết sản phẩm</h3>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Tên sản phẩm</th>
                <th>Đơn vị</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
            <?php while ($item = $order_items->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['tensp']); ?></td>
                    <td><?= htmlspecialchars($item['donvi']); ?></td>
                    <td><?= $item['soluong']; ?></td>
                    <td><?= number_format($item['gia'], 0, ',', '.'); ?>đ</td>
                    <td><?= number_format($item['gia'] * $item['soluong'], 0, ',', '.'); ?>đ</td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p><b>Tổng cộng: <span><?= number_format($order['tongtien'], 0, ',', '.'); ?>đ</span></b></p>
    <?php endif; ?>

    <br>
 <a href="/quanlysieuthi/views/customer/dashboard.php">Quay lại mua sắm</a>

</body>
</html>