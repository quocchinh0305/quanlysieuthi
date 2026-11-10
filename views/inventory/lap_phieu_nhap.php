<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập Phiếu Nhập Kho</title>
</head>
<body>

    <h2>Lập Phiếu Nhập Kho (Nhiều Sản Phẩm)</h2>
    <p><a href="/quanlysieuthi/controller/KhoHangController.php">&larr; Quay lại danh sách kho</a></p>
    <hr>

    <form action="/quanlysieuthi/controller/KhoHangController.php?hanh_dong=xu_ly_nhap" method="POST">
        <p>
            <label><b>Nhà cung cấp:</b></label><br>
            <select name="id_nhacungcap" required style="padding: 5px; width: 300px;">
                <option value="">-- Chọn nhà cung cấp --</option>
                <?php while ($ncc = $ds_ncc->fetch_assoc()): ?>
                    <option value="<?= $ncc['id']; ?>"><?= htmlspecialchars($ncc['tennhacungcap']); ?></option>
                <?php endwhile; ?>
            </select>
        </p>

        <h3>Danh sách sản phẩm nhập</h3>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng nhập</th>
                    <th>Giá nhập (đ)</th>
                    <th>Hạn sử dụng (HSD)</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="ds-san-pham">
                <tr>
                    <td>
                        <select name="id_sp[]" required style="padding: 4px;">
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php 
                            if ($ds_sp->num_rows > 0) {
                                $ds_sp->data_seek(0);
                                while ($sp = $ds_sp->fetch_assoc()) {
                                    echo '<option value="' . $sp['id'] . '">' . htmlspecialchars($sp['tensp']) . ' (' . $sp['donvi'] . ')</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="number" name="soluongnhap[]" min="1" value="1" required style="width: 90px; padding: 4px;"></td>
                    <td><input type="number" name="gianhap[]" min="0" step="1000" value="0" required style="width: 110px; padding: 4px;"></td>
                    <td><input type="date" name="hsd[]" required style="padding: 4px;"></td>
                    <td><button type="button" onclick="xoaDong(this)">Xóa</button></td>
                </tr>
            </tbody>
        </table>

        <p>
            <button type="button" onclick="themDong()">+ Thêm sản phẩm</button>
        </p>
        <hr>
        <p>
            <button type="submit" style="padding: 8px 15px; font-weight: bold;">Hoàn Tất Lập Phiếu Nhập</button>
        </p>
    </form>

    <script>
        function themDong() {
            const tbody = document.getElementById('ds-san-pham');
            const rowFirst = tbody.querySelector('tr');
            const newRow = rowFirst.cloneNode(true);
            
            // Reset giá trị các trường input trong dòng mới sao chép
            newRow.querySelectorAll('input').forEach(input => {
                if (input.type === 'number') {
                    input.value = input.name.includes('soluong') ? 1 : 0;
                } else {
                    input.value = '';
                }
            });
            newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
            
            tbody.appendChild(newRow);
        }

        function xoaDong(btn) {
            const tbody = document.getElementById('ds-san-pham');
            if (tbody.rows.length > 1) {
                btn.closest('tr').remove();
            } else {
                alert('Phiếu nhập phải có ít nhất một sản phẩm!');
            }
        }
    </script>

</body>
</html>