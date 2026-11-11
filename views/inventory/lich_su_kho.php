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
</head>
<body>
<main>
    <a href="/quanlysieuthi/controller/inventory/KhoHangController.php">&larr; Quay lại quản lý kho</a>
    <h1>Lịch sử nhập xuất kho</h1>
    <p>Theo dõi các phiếu nhập từ nhà cung cấp và phiếu xuất kho theo thời gian.</p>

    <section>
        <h2>Lịch sử nhập kho</h2>
        <div>
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
                        <tr><td colspan="10">Chưa có phiếu nhập kho.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <h2>Lịch sử xuất kho</h2>
        <div>
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
                        <tr><td colspan="9">Chưa có phiếu xuất kho.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>