<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";
require_once __DIR__ . "/../models/QuanLyDonHangModel.php";

class QuanLyDonHangController {
    private $conn;
    private $donHangModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->donHangModel = new QuanLyDonHangModel($db);
    }

    public function xuLyYeuCau() {
        $hanh_dong = isset($_GET['hanh_dong']) ? $_GET['hanh_dong'] : 'danh_sach';
        $id = isset($_GET['id']) ? $_GET['id'] : '';

        switch ($hanh_dong) {
            case 'duyet':
                $this->duyetDon($id);
                break;
            case 'huy':
                $this->huyDon($id);
                break;
            case 'cap_nhat_thanh_toan':
                $this->capNhatThanhToan();
                break;
            default:
                $this->danhSach();
                break;
        }
    }

    public function danhSach() {
        $danh_sach_don = $this->donHangModel->getDonChoXacNhan();
        require_once "../views/staff/quan_ly_don_hang.php";
    }

    public function duyetDon($id) {
        $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

        $res_nv = $this->donHangModel->getNhanVienIdTheoTaiKhoan($current_user_id);

        if ($res_nv->num_rows > 0) {
            $row_nv = $res_nv->fetch_assoc();
            $id_nhan_vien = $row_nv['id']; 
        } else {
            $id_nhan_vien = 'NV01'; 
        }

        $this->conn->begin_transaction();

        try {
            $result_items = $this->donHangModel->getSanPhamTrongDonHang($id);

            while ($row_item = $result_items->fetch_assoc()) {
                $id_sp = $row_item['id_sp'];
                $sl_mua = $row_item['soluong'];

                $this->donHangModel->truTonKho($sl_mua, $id_sp);
            }

            $this->donHangModel->xacNhanDonHang($id_nhan_vien, $id);

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            http_response_code(409);
            exit('Không thể duyệt đơn hàng: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        }

        header("Location: QuanLyDonHangController.php");
        exit();
    }

    public function huyDon($id) {
        $this->donHangModel->huyDonHang($id);

        header("Location: QuanLyDonHangController.php");
        exit();
    }

    public function capNhatThanhToan() {
        if (!in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'], true)) {
            http_response_code(403);
            exit('Bạn không có quyền cập nhật thanh toán.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Phương thức yêu cầu không được hỗ trợ.');
        }

        $id = trim($_POST['id'] ?? '');
        $trangThai = $_POST['trangthai_thanhtoan'] ?? '';
        if ($id === '' || !in_array($trangThai, ['dathanhtoan', 'chuathanhtoan'], true)) {
            http_response_code(400);
            exit('Thông tin trạng thái thanh toán không hợp lệ.');
        }

        $this->donHangModel->capNhatTrangThaiThanhToan($id, $trangThai);
        header("Location: QuanLyDonHangController.php");
        exit();
    }
}

$quan_ly = new QuanLyDonHangController($conn);
$quan_ly->xuLyYeuCau();
?>