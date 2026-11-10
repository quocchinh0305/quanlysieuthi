<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";
require_once __DIR__ . "/../models/BanHangModel.php";

class BanHangController {
    private $conn;
    private $banHangModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->banHangModel = new BanHangModel($db);
    }

    public function xuLyYeuCau() {
        $hanh_dong = isset($_GET['hanh_dong']) ? $_GET['hanh_dong'] : 'trang_chu';

        switch ($hanh_dong) {
            case 'them':
                $this->themSanPham();
                break;
            case 'thanhtoan':
                $this->thanhToan();
                break;
            case 'lich_su':
                $this->lichSuBanHang();
                break;
            case 'xoagio':
                unset($_SESSION['gio_hang_pos']);
             header("Location: /quanlysieuthi/controller/BanHangController.php");
                exit();
            default:
                $this->trangChu();
                break;
        }
    }

    public function trangChu() {
        $ket_qua = $this->banHangModel->getDanhSachSanPham();
        $gio_hang_pos = isset($_SESSION['gio_hang_pos']) ? $_SESSION['gio_hang_pos'] : [];
        
        require_once "../views/staff/ban_hang.php";
    }

    public function lichSuBanHang() {
        if (($_SESSION['user_role'] ?? '') !== 'staff' || empty($_SESSION['user_id'])) {
            http_response_code(403);
            exit('Bạn cần đăng nhập bằng tài khoản nhân viên để xem lịch sử bán hàng.');
        }

        $nhanVien = $this->banHangModel->getNhanVienIdTheoTaiKhoan($_SESSION['user_id']);
        if (!$nhanVien) {
            http_response_code(403);
            exit('Không tìm thấy thông tin nhân viên cho tài khoản này.');
        }

        $thong_ke = $this->banHangModel->getThongKeBanHangHomNay($nhanVien['id']);
        $lich_su_ban_hang = $this->banHangModel->getLichSuBanHang($nhanVien['id']);
        require_once "../views/staff/lich_su_ban_hang.php";
    }

    public function themSanPham() {
        $ma_vach = isset($_POST['ma_vach']) ? trim($_POST['ma_vach']) : '';
        
        if (!empty($ma_vach)) {
            $san_pham = $this->banHangModel->getSanPhamTheoMaVach($ma_vach);

            if ($san_pham) {
                $id_sp = $san_pham['id'];
                if (!isset($_SESSION['gio_hang_pos'])) {
                    $_SESSION['gio_hang_pos'] = [];
                }

                if (isset($_SESSION['gio_hang_pos'][$id_sp])) {
                    $_SESSION['gio_hang_pos'][$id_sp]['so_luong'] += 1;
                } else {
                    $_SESSION['gio_hang_pos'][$id_sp] = [
                        'id' => $san_pham['id'],
                        'ten_sp' => $san_pham['tensp'],
                        'gia_sp' => $san_pham['giasp'],
                        'don_vi' => $san_pham['donvi'],
                        'so_luong' => 1
                    ];
                }
            }
        }
        header("Location: BanHangController.php");
        exit();
    }

   public function thanhToan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sdt_khach = trim($_POST['sdt_khach']);
            $ten_khach = trim($_POST['ten_khach']);
            $phuong_thuc = $_POST['phuong_thuc_thanh_toan'];

            $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
            $row_nv = $this->banHangModel->getNhanVienIdTheoTaiKhoan($current_user_id);
            if ($row_nv) {
                $id_nhanvien = $row_nv['id'];
            } else {
                $id_nhanvien = 'NV01'; 
            }

            $gio_hang_pos = isset($_SESSION['gio_hang_pos']) ? $_SESSION['gio_hang_pos'] : [];
            if (empty($gio_hang_pos)) {
                header("Location: BanHangController.php");
                exit();
            }

            $tong_tien = 0;
            foreach ($gio_hang_pos as $item) {
                $tong_tien += $item['gia_sp'] * $item['so_luong'];
            }

            $this->conn->begin_transaction();

            try {
                $row_kh = $this->banHangModel->getKhachHangIdTheoSoDienThoai($sdt_khach);
                if ($row_kh) {
                    $id_khach_hang = $row_kh['id'];
                } else {
                    $id_khach_hang = 'KH' . rand(100, 999);
                    $this->banHangModel->themKhachHang($id_khach_hang, $ten_khach, $sdt_khach);
                }

                $id_don_hang = 'DH' . rand(1000, 9999);
                $loai_don = 'tai_quay';
                $trang_thai_don = 'dagiao';
                $trang_thai_tt = 'dathanhtoan';

                $this->banHangModel->taoDonHang($id_don_hang, $id_khach_hang, $id_nhanvien, $loai_don, $trang_thai_don, $ten_khach, $sdt_khach, $tong_tien, $phuong_thuc, $trang_thai_tt);

                foreach ($gio_hang_pos as $id_sp => $item) {
                    $so_luong = $item['so_luong'];
                    $gia = $item['gia_sp'];

                    $this->banHangModel->themSanPhamDonHang($id_don_hang, $id_sp, $so_luong, $gia);
                    $this->banHangModel->truTonQuay($so_luong, $id_sp);
                }

                $this->conn->commit();
                unset($_SESSION['gio_hang_pos']);
                ?>
                <!DOCTYPE html>
                <html lang="vi">
                <head>
                    <meta charset="UTF-8">
                    <title>Hóa Đơn Thanh Toán - Siêu Thị</title>
                </head>
                <body>
                    <h2>SIÊU THỊ MINI</h2>
                    <p>Hóa Đơn Bán Lẻ Tại Quầy</p>
                    <p>--------------------------------</p>
                    <p><b>Mã đơn:</b> <?= $id_don_hang; ?></p>
                    <p><b>Khách hàng:</b> <?= htmlspecialchars($ten_khach); ?> (<?= $sdt_khach; ?>)</p>
                    <p><b>Thu ngân:</b> <?= $id_nhanvien; ?></p>
                    <p><b>Thời gian:</b> <?= date('d/m/Y H:i:s'); ?></p>
                    <p>--------------------------------</p>
                    
                    <table border="1" cellpadding="5" cellspacing="0">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>SL</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                        <?php foreach ($gio_hang_pos as $item): 
                            $thanh_tien_sp = $item['gia_sp'] * $item['so_luong'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['ten_sp']); ?></td>
                                <td><?= $item['so_luong']; ?></td>
                                <td><?= number_format($item['gia_sp'], 0, ',', '.'); ?></td>
                                <td><?= number_format($thanh_tien_sp, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <p>--------------------------------</p>
                    <p><b>Tổng tiền:</b> <?= number_format($tong_tien, 0, ',', '.'); ?>đ</p>
                    <p><b>Hình thức:</b> <?= strtoupper($phuong_thuc); ?></p>
                    <p>*** CẢM ƠN QUÝ KHÁCH & HẸN GẶP LẠI! ***</p>

                    <br>
                    <button onclick="window.print()">In Hóa Đơn</button>
                    <br><br>
                    <a href="BanHangController.php">&larr; Quay lại bán hàng</a>
                </body>
                </html>
                <?php
                exit();

            } catch (Exception $e) {
                $this->conn->rollback();
                echo "Lỗi thanh toán: " . $e->getMessage();
            }
        }
    }
}

$controller = new BanHangController($conn);
$controller->xuLyYeuCau();
?>