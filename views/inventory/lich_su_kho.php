<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function hienThiGiaTriKho($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lịch sử nhập xuất kho</title>
    <style>
        body { margin: 0; padding: 24px; background: #f5f7fb; color: #253047; font: 15px Arial, sans-serif; }
        main { max-width: 1200px; margin: 0 auto; }
        h1 { margin-bottom: 8px; }
        h2 { margin: 0 0 16px; }
        section { margin-top: 24px; padding: 20px; background: #fff; border: 1px solid #e5e9f0; border-radius: 10px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 11px; border-bottom: 1px solid #eaecf0; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #475467; white-space: nowrap; }
        .back { display: inline-block; margin-bottom: 18px; color: #175cd3; text-decoration: none; }
        .empty { color: #667085; text-align: center; padding: 24px; }
        @media (max-width: 600px) { body { padding: 16px; } section { padding: 14px; } }
    </style>
</head>
<body>
<main>
    <a class="back" href="/quanlysieuthi/controller/KhoHangController.php">&larr; Quay lại quản lý kho</a>
    <h1>Lịch sử nhập xuất kho</h1>
    <p>Theo dõi các phiếu nhập từ nhà cung cấp và phiếu xuất kho theo thời gian.</p>

    <section>
        <h2>Lịch sử nhập kho</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Thời gian</th>
                        <th>Nhà cung cấp</th>
                        <th>Nhân viên</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá nhập</th>
                        <th>Hạn sử dụng</th>
                        <th>Tổng tiền phiếu</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lich_su_nhap->num_rows > 0): ?>
                        <?php while ($phieu = $lich_su_nhap->fetch_assoc()): ?>
                            <tr>
                                <td><?= hienThiGiaTriKho($phieu['id_phieu']); ?></td>
                                <td><?= $phieu['ngaynhap'] ? hienThiGiaTriKho(date('d/m/Y H:i', strtotime($phieu['ngaynhap']))) : '—'; ?></td>
                                <td><?= hienThiGiaTriKho($phieu['tennhacungcap'] ?? ''); ?></td>
                                <td><?= hienThiGiaTriKho($phieu['ten_nhanvien'] ?? ''); ?></td>
                                <td><?= hienThiGiaTriKho($phieu['tensp'] ?? '—'); ?><?= !empty($phieu['donvi']) ? ' (' . hienThiGiaTriKho($phieu['donvi']) . ')' : ''; ?></td>
                                <td><?= isset($phieu['soluongnhap']) ? number_format((int) $phieu['soluongnhap'], 0, ',', '.') : '—'; ?></td>
                                <td><?= isset($phieu['gianhap']) ? number_format((float) $phieu['gianhap'], 0, ',', '.') . 'đ' : '—'; ?></td>
                                <td><?= hienThiGiaTriKho($phieu['hsd'] ?? '—'); ?></td>
                                <td><?= number_format((float) $phieu['tongtien'], 0, ',', '.'); ?>đ</td>
                                <td><?= hienThiGiaTriKho($phieu['ghichu'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td class="empty" colspan="10">Chưa có phiếu nhập kho.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <h2>Lịch sử xuất kho</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Thời gian</th>
                        <th>Loại xuất</th>
                        <th>Nhân viên</th>
                        <th>Mã lô</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng xuất</th>
                        <th>Lý do</th>
                        <th>Ghi chú phiếu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lich_su_xuat->num_rows > 0): ?>
                        <?php while ($phieu = $lich_su_xuat->fetch_assoc()): ?>
                            <?php
                            $loai_xuat = [
                                'len_quay_ke' => 'Chuyển lên quầy kệ',
                                'xuat_huy' => 'Xuất hủy',
                                'tra_ncc' => 'Trả nhà cung cấp'
                            ];
                            ?>
                            <tr>
                                <td><?= hienThiGiaTriKho($phieu['id_phieu']); ?></td>
                                <td><?= $phieu['ngayxuat'] ? hienThiGiaTriKho(date('d/m/Y H:i', strtotime($phieu['ngayxuat']))) : '—'; ?></td>
                                <td><?= hienThiGiaTriKho($loai_xuat[$phieu['loai_xuat']] ?? $phieu['loai_xuat']); ?></td>
                                <td><?= hienThiGiaTriKho($phieu['ten_nhanvien'] ?? ''); ?></td>
                                <td><?= hienThiGiaTriKho($phieu['ma_lo'] ?? '—'); ?></td>
                                <td><?= hienThiGiaTriKho($phieu['tensp'] ?? '—'); ?><?= !empty($phieu['donvi']) ? ' (' . hienThiGiaTriKho($phieu['donvi']) . ')' : ''; ?></td>
                                <td><?= isset($phieu['soluongxuat']) ? number_format((int) $phieu['soluongxuat'], 0, ',', '.') : '—'; ?></td>
                                <td><?= hienThiGiaTriKho($phieu['lydo'] ?? ''); ?></td>
                                <td><?= hienThiGiaTriKho($phieu['ghichu'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td class="empty" colspan="9">Chưa có phiếu xuất kho.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
