<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Tồn Kho & Kiểm Kê</title>
</head>
<body>

    <h2>Quản Lý Tồn Kho & Cảnh Báo Hạn Sử Dụng (FIFO)</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'nhap_thanh_cong'): ?>
        <p><b>Thêm phiếu nhập kho thành công!</b></p>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'xuat_thanh_cong'): ?>
        <p><b>Lập phiếu xuất kho thành công!</b></p>
    <?php endif; ?>

    <p>
        <a href="/quanlysieuthi/controller/KhoHangController.php?hanh_dong=form_nhap">+ Lập Phiếu Nhập Kho</a> | 
        <a href="/quanlysieuthi/controller/KhoHangController.php?hanh_dong=form_xuat">+ Lập Phiếu Xuất Kho</a> | 
        <a href="/quanlysieuthi/controller/DiemDanhController.php">Điểm Danh</a> |
        <a href="/quanlysieuthi/controller/XacThucController.php?action=logout" style="color: red;">Đăng xuất</a>
    </p>
    <hr>

    <h3>Danh Sách Lô Hàng Trong Kho</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Mã Lô</th>
            <th>Tên sản phẩm</th>
            <th>Nhà cung cấp</th>
            <th>Giá nhập</th>
            <th>Hạn sử dụng (HSD)</th>
            <th>Tồn kho</th>
            <th>Tồn quầy</th>
            <th>Trạng thái</th>
        </tr>
        <?php if (isset($danh_sach_kho) && $danh_sach_kho->num_rows > 0): ?>
            <?php while ($row = $danh_sach_kho->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ma_lo']; ?></td>
                    <td><?= htmlspecialchars($row['tensp']); ?> (<?= $row['donvi']; ?>)</td>
                    <td><?= htmlspecialchars($row['tennhacungcap']); ?></td>
                    <td><?= number_format($row['gianhap'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <?php 
                            $hsd = $row['hsd'];
                            $ngay_hien_tai = date('Y-m-d');
                            $can_date = date('Y-m-d', strtotime('+7 days'));
                            if ($hsd < $ngay_hien_tai) {
                                echo '<b>Đã hết hạn (' . $hsd . ')</b>';
                            } elseif ($hsd <= $can_date) {
                                echo '<b>Sắp hết hạn (' . $hsd . ')</b>';
                            } else {
                                echo $hsd;
                            }
                        ?>
                    </td>
                    <td><?= $row['sl_kho']; ?></td>
                    <td><?= $row['sl_quay']; ?></td>
                    <td><?= $row['trangthai']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" align="center">Không có lô hàng nào trong kho!</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>