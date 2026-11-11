<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Quản Lý Đơn Hàng Trực Tuyến</title>
</head>
<body>

    <h2>Quản Lý & Duyệt Đơn Hàng Trực Tuyến</h2>
    <p><a href="/quanlysieuthi/controller/staff/BanHangController.php">Quay lại màn hình bán hàng POS</a></p>
    <hr>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Mã đơn</th>
            <th>Tên người nhận</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ giao</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
            <th>Trạng thái thanh toán</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        <?php while ($row = $danh_sach_don->fetch_assoc()): ?>
            <tr>
                <td><b><?= $row['id']; ?></b></td>
                <td><?= htmlspecialchars($row['ten_nguoinhan']); ?></td>
                <td><?= htmlspecialchars($row['sdt_nguoinhan']); ?></td>
                <td><?= htmlspecialchars($row['diachi']); ?></td>
                <td style="color: red;"><?= number_format($row['thucthu'], 0, ',', '.'); ?>đ</td>
                <td><?= $row['phuongthuc_thanhtoan'] == 'chuyenkhoan' ? 'Chuyển khoản' : 'Tiền mặt (COD)'; ?></td>
                <td>
                    <form action="/quanlysieuthi/controller/staff/QuanLyDonHangController.php?hanh_dong=cap_nhat_thanh_toan" method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <select name="trangthai_thanhtoan" aria-label="Trạng thái thanh toán đơn <?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <option value="chuathanhtoan" <?= $row['trangthai_thanhtoan'] === 'chuathanhtoan' ? 'selected' : ''; ?>>Chưa thanh toán</option>
                            <option value="dathanhtoan" <?= $row['trangthai_thanhtoan'] === 'dathanhtoan' ? 'selected' : ''; ?>>Đã thanh toán</option>
                        </select>
                        <button type="submit">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <?php if($row['trangthai_donhang'] == 'choxacnhan'): ?>
                        <span style="color: orange; font-weight: bold;">Chờ xác nhận</span>
                    <?php elseif($row['trangthai_donhang'] == 'daxacnhan'): ?>
                        <span style="color: blue; font-weight: bold;">Đã xác nhận</span>
                    <?php elseif($row['trangthai_donhang'] == 'dagiao'): ?>
                        <span style="color: green; font-weight: bold;">Đã giao</span>
                    <?php else: ?>
                        <span style="color: gray; font-weight: bold;">Đã hủy</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['trangthai_donhang'] === 'choxacnhan'): ?>
                        <a href="/quanlysieuthi/controller/staff/QuanLyDonHangController.php?hanh_dong=duyet&id=<?= $row['id']; ?>" style="color: green; font-weight: bold;">Duyệt đơn</a> | 
                        <a href="/quanlysieuthi/controller/staff/QuanLyDonHangController.php?hanh_dong=huy&id=<?= $row['id']; ?>" style="color: red;" onclick="return confirm('Bạn có chắc muốn hủy đơn này không?');">Hủy</a>
                    <?php else: ?>
                        <span>Đã xử lý xong</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>