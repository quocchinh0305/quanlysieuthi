<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$current_week = isset($_GET['week']) ? intval($_GET['week']) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Điểm Danh Thủ Kho</title>
</head>
<body>

    <h2>Điểm Danh & Lịch Làm Việc Thủ Kho</h2>
    <p>
        <a href="/quanlysieuthi/controller/KhoHangController.php">Quản Lý Kho Hàng</a>
    </p>
    <hr>

    <p>
        <a href="DiemDanhController.php?week=<?= $current_week - 1; ?>">&larr; Tuần trước</a> | 
        <a href="DiemDanhController.php">Tuần này</a> | 
        <a href="DiemDanhController.php?week=<?= $current_week + 1; ?>">Tuần sau &rarr;</a>
    </p>

    <p><b>Khoảng thời gian:</b> <?= $week_start->format('d/m/Y'); ?> - <?= $week_end->format('d/m/Y'); ?></p>

    <?php if (isset($nhan_vien) && $nhan_vien): ?>
        <p><b>Thủ kho:</b> <?= htmlspecialchars($nhan_vien['hoten']); ?> (<?= htmlspecialchars($nhan_vien['chucvu']); ?>)</p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Ngày làm</th>
            <th>Mã ca</th>
            <th>Tên ca</th>
            <th>Thời gian</th>
            <th>Ghi chú công việc</th>
            <th>Trạng thái điểm danh</th>
            <th>Thao tác</th>
        </tr>
        <?php if (isset($danh_sach_lich) && $danh_sach_lich->num_rows > 0): ?>
            <?php while ($lich = $danh_sach_lich->fetch_assoc()): ?>
                <tr>
                    <td><?= $lich['ngaylam']; ?></td>
                    <td><?= $lich['id_ca']; ?></td>
                    <td><?= htmlspecialchars($lich['tenca']); ?></td>
                    <td><?= $lich['giobatdau']; ?> - <?= $lich['gioketthuc']; ?></td>
                    <td><?= htmlspecialchars($lich['ghichu']); ?></td>
                    <td>
                        <?php 
                            $tt = $lich['trangthai_diemdanh'];
                            if ($tt == 'chuadiemdan') echo 'Chưa điểm danh';
                            elseif ($tt == 'dunggio') echo 'Đã điểm danh (Đúng giờ)';
                            elseif ($tt == 'dimuon') echo 'Đã điểm danh (Đi muộn)';
                            elseif ($tt == 'vangmat') echo 'Vắng mặt';
                            else echo $tt;
                        ?>
                    </td>
                    <td>
                        <?php if ($lich['trangthai_diemdanh'] == 'chuadiemdan' && $lich['ngaylam'] == date('Y-m-d')): ?>
                            <form action="DiemDanhController.php?hanh_dong=check_in" method="POST" style="margin:0;">
                                <input type="hidden" name="id_lich" value="<?= $lich['id']; ?>">
                                <button type="submit">Check-in Ngay</button>
                            </form>
                        <?php else: ?>
                            <i><?= ($lich['ngaylam'] == date('Y-m-d')) ? 'Đã hoàn tất' : 'Ngoài ngày hiện tại'; ?></i>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Không có lịch làm việc trong tuần này!</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>