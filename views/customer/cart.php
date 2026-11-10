
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ten_khachhang = $_SESSION['user_name'] ?? 'Anh Chính';
$cart = $cart ?? [];
$tong_tien = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - VQC</title>
</head>
<body>

    <header>
        <h2>
            <a href="/quanlysieuthi/views/customer/dashboard.php">VQC</a>
        </h2>

        <nav>
            Xin chào, <?= htmlspecialchars($ten_khachhang); ?>
            |
            <a href="/quanlysieuthi/views/customer/dashboard.php">
                Tiếp tục mua sắm
            </a>
        </nav>
    </header>

    <hr>

    <main>
        <h3>Giỏ hàng của bạn</h3>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;">
                <?= htmlspecialchars($_SESSION['success']); ?>
            </p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($cart)): ?>

            <form action="/quanlysieuthi/controller/CartController.php?action=update" method="POST">

                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($cart as $id => $item):

                            $soluong = (int)$item['soluong'];
                            $giasp = (float)$item['giasp'];
                            $thanh_tien = $giasp * $soluong;

                            $tong_tien += $thanh_tien;

                        ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($item['tensp']); ?>
                                    </strong>
                                    <br>
                                    <small>
                                        Đơn vị: <?= htmlspecialchars($item['donvi']); ?>
                                    </small>
                                </td>

                                <td>
                                    <?= number_format($giasp, 0, ',', '.'); ?>đ
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="soluong[<?= htmlspecialchars($id); ?>]"
                                        value="<?= $soluong; ?>"
                                        min="1"
                                        required
                                    >
                                </td>

                                <td>
                                    <?= number_format($thanh_tien, 0, ',', '.'); ?>đ
                                </td>

                                <td>
                                    <a
                                        href="/quanlysieuthi/controller/CartController.php?action=delete&id=<?= urlencode($id); ?>"
                                        onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                                    >
                                        Xóa
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>

                <p>
                    <a href="/quanlysieuthi/views/customer/dashboard.php">
                        Mua thêm sản phẩm
                    </a>
                    |
                    <button type="submit">
                        Cập nhật giỏ hàng
                    </button>
                </p>

            </form>

            <hr>

            <section>
                <h4>Cộng giỏ hàng</h4>

                <p>
                    Tạm tính:
                    <strong>
                        <?= number_format($tong_tien, 0, ',', '.'); ?>đ
                    </strong>
                </p>

                <p>
                    Phí vận chuyển:
                    <strong>Miễn phí</strong>
                </p>

                <p>
                    Tổng cộng:
                    <strong>
                        <?= number_format($tong_tien, 0, ',', '.'); ?>đ
                    </strong>
                </p>

                <a href="/quanlysieuthi/controller/ThanhToanController.php?action=form_thanh_toan">
                    <button type="button">
                        TIẾN HÀNH ĐẶT HÀNG
                    </button>
                </a>
            </section>

        <?php else: ?>

            <p>Giỏ hàng của bạn đang trống.</p>

            <a href="/quanlysieuthi/views/customer/dashboard.php">
                Quay lại mua sắm
            </a>

        <?php endif; ?>

    </main>

</body>
</html>
