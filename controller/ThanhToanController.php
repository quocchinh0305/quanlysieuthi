<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";
require_once __DIR__ . "/../models/ThanhToanModel.php";

class ThanhToanController {
    private $conn;
    private $thanhToanModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->thanhToanModel = new ThanhToanModel($db);
    }

    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'form_thanh_toan';

        switch ($action) {
            case 'xu_ly':
                $this->xuLyThanhToan();
                break;
            case 'hoan_tat':
                $this->hienThiHoaDon();
                break;
            case 'lich_su':
                $this->lichSuDonHang();
                break;
            case 'huy_don':
                $this->huyDonHang();
                break;
            case 'mua_lai':
                $this->muaLaiDonHang();
                break;
            default:
                $this->formThanhToan();
                break;
        }
    }

    public function formThanhToan() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            header("Location: /quanlysieuthi/views/customer/dashboard.php");
            exit();
        }
        
        $tong_tien = 0;
        foreach ($cart as $item) {
            $tong_tien += $item['giasp'] * $item['soluong'];
        }

        require_once "../views/customer/thanhtoan.php";
    }

    public function xuLyThanhToan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ten_nguoinhan = trim($_POST['ten_nguoinhan'] ?? '');
            $sdt_nguoinhan = trim($_POST['sdt_nguoinhan'] ?? '');
            $diachi = trim($_POST['diachi'] ?? '');
            $phuongthuc_thanhtoan = $_POST['phuongthuc_thanhtoan'] ?? '';

            if (!in_array($phuongthuc_thanhtoan, ['tienmat', 'chuyenkhoan'], true)) {
                http_response_code(400);
                exit('Phương thức thanh toán không hợp lệ.');
            }

            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            if (empty($cart)) {
                header("Location: /quanlysieuthi/views/customer/dashboard.php");
                exit();
            }

            $tong_tien = 0;
            foreach ($cart as $item) {
                $tong_tien += $item['giasp'] * $item['soluong'];
            }

            $this->conn->begin_transaction();

            try {
                $row_kh = $this->thanhToanModel->getKhachHangIdTheoSoDienThoai($sdt_nguoinhan);
                if ($row_kh) {
                    $id_khachhang = $row_kh['id'];
                } else {
                    $id_khachhang = 'KH' . rand(100, 999);
                    $this->thanhToanModel->themKhachHang($id_khachhang, $ten_nguoinhan, $sdt_nguoinhan, $diachi);
                }

                $id_donhang = 'DH' . rand(1000, 9999);
                $loai_don = 'online';
                $trangthai_donhang = 'choxacnhan';
                $trangthai_thanhtoan = 'chuathanhtoan';

                $this->thanhToanModel->taoDonHang($id_donhang, $id_khachhang, $loai_don, $trangthai_donhang, $ten_nguoinhan, $sdt_nguoinhan, $diachi, $tong_tien, $phuongthuc_thanhtoan, $trangthai_thanhtoan);

                foreach ($cart as $id_sp => $item) {
                    $soluong = $item['soluong'];
                    $gia = $item['giasp'];

                    $this->thanhToanModel->themSanPhamDonHang($id_donhang, $id_sp, $soluong, $gia);
                }

                $this->conn->commit();

                $_SESSION['last_order_id'] = $id_donhang;
                unset($_SESSION['cart']);

                header("Location: /quanlysieuthi/controller/ThanhToanController.php?action=hoan_tat");
                exit();

            } catch (Exception $e) {
                $this->conn->rollback();
                echo "Lỗi đặt hàng: " . $e->getMessage();
            }
        }
    }

    public function hienThiHoaDon() {
        $id_donhang = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : '';
        if (empty($id_donhang)) {
            header("Location: /quanlysieuthi/views/customer/dashboard.php");
            exit();
        }

        $order = $this->thanhToanModel->getDonHang($id_donhang);
        $order_items = $this->thanhToanModel->getSanPhamDonHang($id_donhang);

        require_once "../views/customer/order_success.php";
    }

public function lichSuDonHang() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $id_khachhang = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';

        $result_orders = $this->thanhToanModel->getDonHangTheoKhachHang($id_khachhang);

        require_once __DIR__ . "/../views/customer/lich_su_don_hang.php";
    }

    public function huyDonHang() {
        $id_donhang = isset($_GET['id']) ? $_GET['id'] : '';

        if (!empty($id_donhang)) {
            $result = $this->thanhToanModel->getTrangThaiDonHang($id_donhang);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($row['trangthai_donhang'] == 'choxacnhan') {
                    $this->thanhToanModel->huyDonHang($id_donhang);
                }
            }
        }

        
        header("Location: /quanlysieuthi/controller/ThanhToanController.php?action=lich_su");
        exit();
    }
   public function muaLaiDonHang() {
        $id_donhang = isset($_GET['id']) ? $_GET['id'] : '';

        if (!empty($id_donhang)) {
           
            $result = $this->thanhToanModel->getChiTietDonHangDeMuaLai($id_donhang);

            if ($result->num_rows > 0) {
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                while ($item = $result->fetch_assoc()) {
                    $id_sp = $item['id_sp'];
                    $soluong = $item['soluong'];
                    $gia = $item['gia'];

                    $tensp = $item['tensp'] ? $item['tensp'] : 'Sản phẩm';
                    $donvi = $item['donvi'] ? $item['donvi'] : '';

                    if (isset($_SESSION['cart'][$id_sp])) {
                        $_SESSION['cart'][$id_sp]['soluong'] += $soluong;
                    } else {
                        $_SESSION['cart'][$id_sp] = [
                            'tensp' => $tensp,
                            'soluong' => $soluong,
                            'giasp' => $gia,
                            'donvi' => $donvi 
                        ];
                    }
                }
            }
        }
        session_write_close();
        
       header("Location: /quanlysieuthi/controller/CartController.php?action=index");
exit();
    }
}

$thanhToanController = new ThanhToanController($conn);
$thanhToanController->handleRequest();
?>