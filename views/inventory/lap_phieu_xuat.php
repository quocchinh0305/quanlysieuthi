<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập Phiếu Xuất Kho</title>
</head>
<body>

    <h2>Lập Phiếu Xuất Kho / Chuyển Quầy (Nhiều Sản Phẩm)</h2>
    <p><a href="/quanlysieuthi/controller/KhoHangController.php">&larr; Quay lại danh sách kho</a></p>
    <hr>

    <form action="/quanlysieuthi/controller/KhoHangController.php?hanh_dong=xu_ly_xuat" method="POST">
        <p>
            <label><b>Loại xuất kho chung cho phiếu:</b></label><br>
            <select name="loai_xuat" required style="padding: 5px; width: 300px;">
                <option value="len_quay_ke">Xuất chuyển lên quầy kệ bán hàng</option>
                <option value="xuat_huy">Xuất hủy (Hỏng/Hết hạn)</option>
                <option value="tra_ncc">Xuất trả lại nhà cung cấp</option>
            </select>
        </p>

        <p>
            <label><b>Lý do xuất / Ghi chú chung:</b></label><br>
            <textarea name="lydo" rows="2" style="width: 400px; padding: 5px;" placeholder="Nhập lý do xuất kho..."></textarea>
        </p>

        <h3>Danh sách các lô cần xuất</h3>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Chọn lô hàng trong kho</th>
                    <th>Số lượng xuất</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="ds-xuat-kho">
                <tr>
                    <td>
                        <select name="id_lo[]" required style="padding: 4px; width: 400px;">
                            <option value="">-- Chọn lô hàng --</option>
                            <?php 
                            if ($ds_kho->num_rows > 0) {
                                $ds_kho->data_seek(0);
                                while ($lo = $ds_kho->fetch_assoc()) {
                                    echo '<option value="' . $lo['id'] . '">Mã lô: ' . $lo['ma_lo'] . ' | SP: ' . htmlspecialchars($lo['tensp']) . ' | Tồn kho: ' . $lo['sl_kho'] . ' | HSD: ' . $lo['hsd'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="number" name="soluongxuat[]" min="1" value="1" required style="width: 100px; padding: 4px;"></td>
                    <td><button type="button" onclick="xoaDong(this)">Xóa</button></td>
                </tr>
            </tbody>
        </table>

        <p>
            <button type="button" onclick="themDong()">+ Thêm lô xuất</button>
        </p>
        <hr>
        <p>
            <button type="submit" style="padding: 8px 15px; font-weight: bold;">Xác Nhận Xuất Kho Hàng Loạt</button>
        </p>
    </form>

    <script>
        function themDong() {
            const tbody = document.getElementById('ds-xuat-kho');
            const rowFirst = tbody.querySelector('tr');
            const newRow = rowFirst.cloneNode(true);
            
            newRow.querySelector('input[type="number"]').value = 1;
            newRow.querySelector('select').selectedIndex = 0;
            
            tbody.appendChild(newRow);
        }

        function xoaDong(btn) {
            const tbody = document.getElementById('ds-xuat-kho');
            if (tbody.rows.length > 1) {
                btn.closest('tr').remove();
            } else {
                alert('Phiếu xuất phải có ít nhất một sản phẩm!');
            }
        }
    </script>

</body>
</html>