<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$tong_tien = 0;
foreach ($cart as $item) {
    $tong_tien += $item['giasp'] * $item['soluong'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh Toán Đơn Hàng</title>
</head>
<body>

    <h2>Trang Thanh Toán Siêu thị VQC</h2>
    <hr>

    <form action="/quanlysieuthi/controller/ThanhToanController.php?action=xu_ly" method="POST">
        <h3>1. Thông tin người nhận</h3>
        <p>
            Họ tên người nhận: <br>
            <input type="text" name="ten_nguoinhan" size="30" required>
        </p>
        <p>
            Số điện thoại nhận hàng: <br>
            <input type="text" name="sdt_nguoinhan" size="30" required>
        </p>
        <p>
            Địa chỉ giao hàng: <br>
            <textarea name="diachi" rows="3" cols="35" required></textarea>
        </p>

        <h3>2. Hình thức thanh toán</h3>
        <p>
            <input type="radio" name="phuongthuc_thanhtoan" value="tienmat" checked> Thanh toán khi nhận hàng (COD) <br>
            <input type="radio" name="phuongthuc_thanhtoan" value="chuyenkhoan"> Chuyển khoản ngân hàng
        </p>

        <h3>3. Danh sách sản phẩm trong giỏ</h3>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['tensp']); ?></td>
                    <td><?= $item['soluong']; ?></td>
                    <td><?= number_format($item['giasp'], 0, ',', '.'); ?>đ</td>
                    <td><?= number_format($item['giasp'] * $item['soluong'], 0, ',', '.'); ?>đ</td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <p><b>Tổng số tiền cần thanh toán: <span style="color: red;"><?= number_format($tong_tien, 0, ',', '.'); ?>đ</span></b></p>

        <p>
            <a href="/quanlysieuthi/controller/CartController.php?action=index">Quay lại giỏ hàng</a> | 
            <button type="submit">Xác nhận đặt hàng</button>
        </p>
    </form>

</body>
</html>