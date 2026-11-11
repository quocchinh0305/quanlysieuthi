<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title> Điểm Danh</title>
</head>
<body>

    <h2> Điểm Danh </h2>
    <p>
        <a href="/quanlysieuthi/controller/staff/BanHangController.php">Trang Bán Hàng (POS)</a> | 
        <a href="/quanlysieuthi/controller/staff/QuanLyDonHangController.php">Quản Lý Đơn Hàng Online</a>
    </p>
    <hr>

    <?php if (isset($nhan_vien) && $nhan_vien): ?>
        <p><b>Nhân viên:</b> <?= htmlspecialchars($nhan_vien['hoten'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <p>
        <a href="DiemDanhController.php?week=<?= urlencode($tuan_truoc); ?>">&larr; Tuần trước</a>
        |
        <strong>Tuần <?= htmlspecialchars($week_start->format('d/m/Y')); ?> - <?= htmlspecialchars($week_end->format('d/m/Y')); ?></strong>
        |
        <a href="DiemDanhController.php?week=<?= urlencode($tuan_sau); ?>">Tuần sau &rarr;</a>
        <?php if ($ngay_bat_dau !== $tuan_hien_tai): ?>
            | <a href="DiemDanhController.php?week=<?= urlencode($tuan_hien_tai); ?>">Tuần hiện tại</a>
        <?php endif; ?>
    </p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>STT</th>
            <th>Ngày làm</th>
            <th>Nhân viên</th>
            <th>Tên ca</th>
            <th>Khung giờ quy định</th>
            <th>Trạng thái & Thời gian check-in</th>
            <th>Ghi chú</th>
            <th>Thao tác</th>
        </tr>
        <?php if (isset($danh_sach_lich) && $danh_sach_lich->num_rows > 0): ?>
            <?php $stt = 1; while ($row = $danh_sach_lich->fetch_assoc()): ?>
                <tr>
                    <td><?= $stt++; ?></td>
                    <td><?= $row['ngaylam']; ?></td>
                    <td><?= htmlspecialchars($row['ten_nhanvien']); ?></td>
                    <td><?= htmlspecialchars($row['tenca']); ?></td>
                    <td><?= $row['giobatdau'] . ' - ' . $row['gioketthuc']; ?></td>
                    <td>
                        <?php 
                            $tt = $row['trangthai_diemdanh'];
                            $time_dd = isset($row['thoigian_diemdanh']) ? $row['thoigian_diemdanh'] : '';
                            $da_diem_danh = in_array($tt, ['dunggio', 'dimuon', 'vangmat'], true);

                            if ($tt == 'dunggio') {
                                echo '<b style="color: green;">Điểm danh thành công (Đúng giờ)</b><br><small>(' . $time_dd . ')</small>';
                            } elseif ($tt == 'dimuon') {
                                echo '<b style="color: orange;">Điểm danh thành công (Đi muộn)</b><br><small>(' . $time_dd . ')</small>';
                            } elseif ($tt == 'vangmat') {
                                echo '<b style="color: red;">Vắng mặt</b>';
                            } else {
                                echo '<i>Chưa điểm danh</i>';
                            }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['ghichu']); ?></td>
                    <td>
                        <?php if ($da_diem_danh): ?>
                            <span>Đã điểm danh</span>
                        <?php elseif (isset($form_nhap_ly_do_id) && $form_nhap_ly_do_id == $row['id']): ?>
                            <form action="/quanlysieuthi/controller/DiemDanhController.php?hanh_dong=luu_diemdanh&amp;id=<?= $row['id']; ?>&amp;week=<?= urlencode($ngay_bat_dau); ?>" method="POST" style="margin:0;">
                                <span style="color: red; font-size: 12px;">Bạn đã đến muộn! Vui lòng nhập lý do:</span><br>
                                <input type="text" name="ghichu" placeholder="Nhập lý do đi muộn..." size="20" required>
                                <button type="submit">Gửi lý do</button>
                            </form>
                        <?php else: ?>
                            <!-- Nút check-in ban đầu -->
                            <form action="/quanlysieuthi/controller/DiemDanhController.php?hanh_dong=kiemtra&amp;id=<?= $row['id']; ?>&amp;week=<?= urlencode($ngay_bat_dau); ?>" method="POST" style="margin:0;">
                                <button type="submit">Check-in ngay</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="color: red;">Không có lịch làm việc nào!</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>