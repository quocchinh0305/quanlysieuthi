<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ten_nhan_vien = $_SESSION['user_name'] ?? 'bạn';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lịch sử bán hàng</title>
</head>
<body>
<main>
    <a href="/quanlysieuthi/controller/staff/BanHangController.php">&larr; Quay lại màn hình bán hàng</a>
    <h1>Lịch sử bán hàng</h1>
    <p>
        Chào <?= htmlspecialchars($ten_nhan_vien, ENT_QUOTES, 'UTF-8'); ?>,
        đây là kết quả bán hàng của bạn trong ngày <?= date('d/m/Y'); ?>.
    </p>

    <section>
        <div>
            <span>Doanh thu hôm nay</span>
            <strong><?= number_format((float) ($thong_ke['tong_doanh_thu'] ?? 0), 0, ',', '.'); ?>đ</strong>
        </div>
        <div>
            <span>Số đơn đã thanh toán</span>
            <strong><?= number_format((int) ($thong_ke['so_don'] ?? 0), 0, ',', '.'); ?></strong>
        </div>
        <div>
            <span>Khách hàng hôm nay</span>
            <strong><?= number_format((int) ($thong_ke['so_khach_hang'] ?? 0), 0, ',', '.'); ?></strong>
        </div>
    </section>

    <section>
        <h2>Các đơn hàng bạn đã xử lý</h2>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Loại đơn</th>
                        <th>Thời gian</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lich_su_ban_hang->num_rows > 0): ?>
                        <?php while ($don_hang = $lich_su_ban_hang->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($don_hang['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $don_hang['loai_don'] === 'online' ? 'Đặt online' : 'Tại quầy'; ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($don_hang['thoigian_ban'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($don_hang['ten_nguoinhan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($don_hang['sdt_nguoinhan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= number_format((float) $don_hang['tongtien'], 0, ',', '.'); ?>đ</td>
                                <td>
                                    <?php
                                    $phuong_thuc = [
                                        'tienmat' => 'Tiền mặt',
                                        'chuyenkhoan' => 'Chuyển khoản',
                                        'the' => 'Thẻ ngân hàng'
                                    ];
                                    echo htmlspecialchars($phuong_thuc[$don_hang['phuongthuc_thanhtoan']] ?? $don_hang['phuongthuc_thanhtoan'], ENT_QUOTES, 'UTF-8');
                                    ?>
                                </td>
                                <td>
                                    <?php if ($don_hang['trangthai_donhang'] === 'dahuy'): ?>
                                        Đã hủy
                                    <?php elseif ($don_hang['trangthai_thanhtoan'] === 'dathanhtoan'): ?>
                                        Đã thanh toán
                                    <?php else: ?>
                                        Chưa thanh toán
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Bạn chưa có đơn hàng nào được giao hoặc xử lý.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>