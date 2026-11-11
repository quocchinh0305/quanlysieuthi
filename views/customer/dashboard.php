<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../config/database.php";

$sql_danhmuc = "SELECT * FROM danhmucsanpham";
$result_danhmuc = $conn->query($sql_danhmuc);

$danh_muc_list = [];
if ($result_danhmuc) {
    while ($row = $result_danhmuc->fetch_assoc()) {
        $danh_muc_list[] = $row;
    }
}

$id_dm_chon = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$sql_ton_kho_join = "
    LEFT JOIN (
        SELECT id_sp, SUM(GREATEST(sl_quay, 0) + GREATEST(sl_kho, 0)) AS so_luong_ton
        FROM kho_hang
        GROUP BY id_sp
    ) ton ON ton.id_sp = sp.id
";

if (!empty($id_dm_chon)) {
    if (in_array($id_dm_chon, ['DM01', 'DM02', 'DM03', 'DM04', 'DM05'])) {
        $stmt = $conn->prepare("SELECT sp.*, dm.tendanhmuc, COALESCE(ton.so_luong_ton, 0) AS so_luong_ton FROM sanpham sp JOIN danhmucsanpham dm ON sp.id_danhmuc = dm.id $sql_ton_kho_join WHERE sp.id_danhmuc LIKE ?");
        $like_dm = $id_dm_chon . "%";
        $stmt->bind_param("s", $like_dm);
    } else {
        $stmt = $conn->prepare("SELECT sp.*, dm.tendanhmuc, COALESCE(ton.so_luong_ton, 0) AS so_luong_ton FROM sanpham sp JOIN danhmucsanpham dm ON sp.id_danhmuc = dm.id $sql_ton_kho_join WHERE sp.id_danhmuc = ?");
        $stmt->bind_param("s", $id_dm_chon);
    }
    $stmt->execute();
    $result_sanpham = $stmt->get_result();
} elseif (!empty($keyword)) {
    $searchTerm = "%" . $keyword . "%";
    $stmt = $conn->prepare("SELECT sp.*, dm.tendanhmuc, COALESCE(ton.so_luong_ton, 0) AS so_luong_ton FROM sanpham sp JOIN danhmucsanpham dm ON sp.id_danhmuc = dm.id $sql_ton_kho_join WHERE sp.tensp LIKE ?");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result_sanpham = $stmt->get_result();
} else {
    $sql_sanpham = "SELECT sp.*, dm.tendanhmuc, COALESCE(ton.so_luong_ton, 0) AS so_luong_ton FROM sanpham sp JOIN danhmucsanpham dm ON sp.id_danhmuc = dm.id $sql_ton_kho_join";
    $result_sanpham = $conn->query($sql_sanpham);
}

$total_cart_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_cart_items += $item['soluong'];
    }
}

$ten_khachhang = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Anh chính';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng VQC - Siêu thị mini</title>
</head>
<body>

    <header>
        <h2><a href="dashboard.php">VQC - Quản lý Siêu thị</a></h2>
        
        <form action="dashboard.php" method="GET">
            <input type="search" name="keyword" value="<?= htmlspecialchars($keyword); ?>" placeholder="Tìm kiếm sản phẩm...">
            <button type="submit">Tìm kiếm</button>
        </form>

        <nav>
            <a href="../../controller/customer/CartController.php?action=index">Giỏ hàng (<?= $total_cart_items; ?>)</a> | 
            <span>Tài khoản: <?= htmlspecialchars($ten_khachhang); ?></span> | 
            <a href="/quanlysieuthi/controller/customer/KhachHangController.php?action=taiKhoan">Tài khoản của tôi</a> | 
            <a href="/quanlysieuthi/controller/customer/ThanhToanController.php?action=lich_su">Lịch sử đơn hàng</a> | 
            <a href="../auth/login.php?action=logout">Đăng xuất</a>
        </nav>
    </header>

    <hr>
    <main>
        <aside>
            <h3>DANH MỤC SẢN PHẨM</h3>
            <ul>
                <li><a href="dashboard.php">Tất cả sản phẩm</a></li>
                
                <li>
                    <strong>THỊT, CÁ, TRỨNG, HẢI SẢN</strong>
                    <ul>
                        <li><a href="dashboard.php?danhmuc=DM01_1">Thịt heo</a></li>
                        <li><a href="dashboard.php?danhmuc=DM01_2">Thịt bò</a></li>
                        <li><a href="dashboard.php?danhmuc=DM01_3">Thịt gà, vịt</a></li>
                        <li><a href="dashboard.php?danhmuc=DM01_4">Cá, hải sản</a></li>
                        <li><a href="dashboard.php?danhmuc=DM01_5">Trứng gà, vịt, cút</a></li>
                    </ul>
                </li>

                <li>
                    <strong>RAU, CỦ, NẤM, TRÁI CÂY</strong>
                    <ul>
                        <li><a href="dashboard.php?danhmuc=DM02_1">Rau xanh các loại</a></li>
                        <li><a href="dashboard.php?danhmuc=DM02_2">Củ và quả tươi</a></li>
                        <li><a href="dashboard.php?danhmuc=DM02_3">Nấm các loại</a></li>
                        <li><a href="dashboard.php?danhmuc=DM02_4">Trái cây tươi</a></li>
                    </ul>
                </li>

                <li>
                    <strong>DẦU ĂN, GIA VỊ, NƯỚC CHẤM</strong>
                    <ul>
                        <li><a href="dashboard.php?danhmuc=DM03_1">Dầu ăn các loại</a></li>
                        <li><a href="dashboard.php?danhmuc=DM03_2">Nước chấm & Nước mắm</a></li>
                        <li><a href="dashboard.php?danhmuc=DM03_3">Gia vị nêm nếm</a></li>
                    </ul>
                </li>

                <li>
                    <strong>SỮA CÁC LOẠI</strong>
                    <ul>
                        <li><a href="dashboard.php?danhmuc=DM04_1">Sữa tươi tiệt trùng</a></li>
                        <li><a href="dashboard.php?danhmuc=DM04_2">Sữa chua các loại</a></li>
                        <li><a href="dashboard.php?danhmuc=DM04_3">Sữa hạt & Dinh dưỡng</a></li>
                    </ul>
                </li>

                <li>
                    <strong>MÌ, MIẾN, CHÁO, PHỞ</strong>
                    <ul>
                        <li><a href="dashboard.php?danhmuc=DM05_1">Mì ăn liền</a></li>
                        <li><a href="dashboard.php?danhmuc=DM05_2">Miến, phở, hủ tiếu khô</a></li>
                        <li><a href="dashboard.php?danhmuc=DM05_3">Cháo ăn liền</a></li>
                    </ul>
                </li>
            </ul>
        </aside>

        <hr>

        <!-- KHU VỰC HIỂN THỊ SẢN PHẨM -->
        <section>
            <h3>SẢN PHẨM THIẾT YẾU</h3>
            <p>Phân loại theo danh mục </p>

            <?php if(isset($_SESSION['error'])): ?>
                <div>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>Danh mục</th>
                        <th>Tên sản phẩm</th>
                        <th>Mô tả</th>
                        <th>Giá tiền</th>
                        <th>Số lượng còn</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_sanpham && $result_sanpham->num_rows > 0): ?>
                        <?php while($sp = $result_sanpham->fetch_assoc()): ?>
                            <tr>
                                <td>[<?= htmlspecialchars($sp['tendanhmuc']); ?>]</td>
                                <td><strong><?= htmlspecialchars($sp['tensp']); ?></strong></td>
                                <td><?= htmlspecialchars($sp['mota']); ?></td>
                                <td>
                                    <?= number_format($sp['giasp'], 0, ',', '.'); ?>đ / <?= htmlspecialchars($sp['donvi']); ?>
                                </td>
                                <td><?= number_format((int) $sp['so_luong_ton'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="../../controller/customer/CartController.php?action=add&id=<?= $sp['id']; ?>">Thêm vào giỏ</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" align="center">Không tìm thấy sản phẩm nào trong danh mục này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>