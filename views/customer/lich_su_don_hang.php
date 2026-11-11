<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Mua Hàng</title>
</head>
<body>

    <h2>Lịch Sử Đơn Hàng Của Bạn</h2>
    <hr>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Mã đơn hàng</th>
            <th>Ngày đặt</th>
            <th>Người nhận</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Tổng tiền</th>
            <th>Hình thức TT</th>
            <th>Thanh toán</th>
            <th>Trạng thái đơn</th>
            <th>Thao tác</th>
        </tr>
        <?php if (isset($result_orders) && $result_orders->num_rows > 0): ?>
            <?php while ($row = $result_orders->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['created_at'] ?? ''); ?></td>
                    <td><?= htmlspecialchars($row['ten_nguoinhan']); ?></td>
                    <td><?= htmlspecialchars($row['sdt_nguoinhan']); ?></td>
                    <td><?= htmlspecialchars($row['diachi']); ?></td>
                    <td><?= number_format($row['tongtien'], 0, ',', '.'); ?>đ</td>
                    <td><?= ($row['phuongthuc_thanhtoan'] == 'tienmat') ? 'Tiền mặt (COD)' : 'Chuyển khoản'; ?></td>
                    <td><?= $row['trangthai_thanhtoan'] === 'dathanhtoan' ? 'Đã thanh toán' : ($row['trangthai_thanhtoan'] === 'dahuy' ? 'Đã hủy' : 'Chưa thanh toán'); ?></td>
                    <td><?= htmlspecialchars($row['trangthai_donhang']); ?></td>
                    <td>
                        <?php if ($row['trangthai_donhang'] == 'choxacnhan'): ?>
                            <a href="/quanlysieuthi/controller/customer/ThanhToanController.php?action=huy_don&id=<?= $row['id']; ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">Hủy đơn</a>
                        <?php else: ?>
                            <a href="/quanlysieuthi/controller/customer/ThanhToanController.php?action=mua_lai&id=<?= $row['id']; ?>">Mua lại</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="10">Bạn chưa có đơn hàng nào cả!</td>
            </tr>
        <?php endif; ?>
    </table>

    <br>
    <p><a href="/quanlysieuthi/views/customer/dashboard.php">Quay lại mua sắm</a></p>

</body>
</html>