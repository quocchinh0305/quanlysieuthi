<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$tong_tien = 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bán Hàng Tại Quầy</title>
</head>
<body>

    <h2>Phần Mềm Bán Hàng Tại Quầy (POS)</h2>
    <p>
        <a href="/quanlysieuthi/controller/staff/QuanLyDonHangController.php">Quản lý & Duyệt đơn hàng trực tuyến</a> | 
        <a href="/quanlysieuthi/controller/staff/BanHangController.php?hanh_dong=lich_su">Lịch sử bán hàng</a> |
        <a href="/quanlysieuthi/controller/DiemDanhController.php" style="font-weight: bold; color: #007bff;"> Điểm danh</a> |
        <a href="/quanlysieuthi/controller/XacThucController.php?action=logout" style="color: red;">Đăng xuất</a>
    </p>
    <hr>
    
    <form action="/quanlysieuthi/controller/staff/BanHangController.php?hanh_dong=them" method="POST">
        <b>Nhập mã vạch hoặc mã sản phẩm:</b> <br>
        <input type="text" name="ma_vach" size="30" autofocus required>
        <button type="submit">Thêm vào giỏ</button>
    </form>

    <br>
    <h3>Hóa đơn tạm tính</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Tên sản phẩm</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
        <?php if (!empty($gio_hang_pos)): ?>
            <?php foreach ($gio_hang_pos as $item): 
                $thanh_tien = $item['gia_sp'] * $item['so_luong'];
                $tong_tien += $thanh_tien;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['ten_sp']); ?></td>
                    <td><?= number_format($item['gia_sp'], 0, ',', '.'); ?>đ</td>
                    <td><?= $item['so_luong']; ?></td>
                    <td><?= number_format($thanh_tien, 0, ',', '.'); ?>đ</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">Chưa có sản phẩm nào trong hóa đơn.</td></tr>
        <?php endif; ?>
    </table>

    <p><b>Tổng tiền cần thanh toán: <span style="color: red;"><?= number_format($tong_tien, 0, ',', '.'); ?>đ</span></b></p>

    <?php if (!empty($gio_hang_pos)): ?>
        <p><a href="/quanlysieuthi/controller/staff/BanHangController.php?hanh_dong=xoagio" style="color: red;">Xóa trắng hóa đơn này</a></p>
    <?php endif; ?>

    <hr>
    <h3>Thông tin thanh toán của khách</h3>
    <form action="/quanlysieuthi/controller/staff/BanHangController.php?hanh_dong=thanhtoan" method="POST">
        <p>
            Số điện thoại khách: <br>
            <input type="text" name="sdt_khach" value="0933999888" required>
        </p>
        <p>
            Tên khách hàng: <br>
            <input type="text" name="ten_khach" value="Khách vãng lai" required>
        </p>
        <p>
            Hình thức thanh toán: <br>
            <input type="radio" name="phuong_thuc_thanh_toan" value="tienmat" checked> Tiền mặt <br>
            <input type="radio" name="phuong_thuc_thanh_toan" value="chuyenkhoan"> Chuyển khoản <br>
            <input type="radio" name="phuong_thuc_thanh_toan" value="the"> Thẻ ngân hàng
        </p>
        <p>
            <button type="submit">Xác nhận thanh toán & In hóa đơn</button>
        </p>
    </form>

    <hr>
    <h3>Danh sách sản phẩm trong siêu thị</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>Giá bán</th>
            <th>Thao tác</th>
        </tr>
        <?php if (isset($ket_qua) && $ket_qua->num_rows > 0): ?>
            <?php while ($sp = $ket_qua->fetch_assoc()): ?>
                <tr>
                    <td><?= $sp['id']; ?></td>
                    <td><?= htmlspecialchars($sp['tensp']); ?></td>
                    <td><?= number_format($sp['giasp'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <form action="/quanlysieuthi/controller/staff/BanHangController.php?hanh_dong=them" method="POST" style="margin:0;">
                            <input type="hidden" name="ma_vach" value="<?= $sp['id']; ?>">
                            <button type="submit">Chọn mua</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center; color: red;">Chưa có sản phẩm nào trong cơ sở dữ liệu!</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>