<?php
require_once __DIR__ . "/../models/KhachHangModel.php";

class KhachHangController {
    private $khachHangModel;

    public function __construct($dbConnection) {
        $this->khachHangModel = new KhachHangModel($dbConnection);
    }

    public function taiKhoan() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_khachhang = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';
        if (empty($id_khachhang)) {
            header("Location: /quanlysieuthi/views/customer/login.php");
            exit();
        }

        $khachhang = $this->khachHangModel->getThongTinKhachHang($id_khachhang);

        require_once __DIR__ . "/../views/customer/tai_khoan.php";
    }

    public function updateTaiKhoan() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_khachhang = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';
        if (empty($id_khachhang)) {
            header("Location: /quanlysieuthi/views/customer/login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoten = trim($_POST['hoten']);
            $sodienthoai = trim($_POST['sodienthoai']);
            $diachi = trim($_POST['diachi']);

            if ($this->khachHangModel->capNhatThongTin($hoten, $sodienthoai, $diachi, $id_khachhang)) {
                $_SESSION['success_msg'] = "Cập nhật thông tin tài khoản thành công!";
            } else {
                $_SESSION['error_msg'] = "Cập nhật thất bại, vui lòng thử lại!";
            }

            header("Location: /quanlysieuthi/controller/KhachHangController.php?action=tai_khoan");
            exit();
        }
    }

    public function deleteTaiKhoan() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_khachhang = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

        if (empty($id_khachhang) || empty($user_id)) {
            header("Location: /quanlysieuthi/views/auth/login.php");
            exit();
        }
        $this->khachHangModel->xoaTaiKhoanKhachHang($id_khachhang, $user_id);

        session_unset();
        session_destroy();
        header("Location: /quanlysieuthi/views/auth/login.php");
        exit();
    }
}

require_once __DIR__ . "/../config/database.php"; 

$controller = new KhachHangController($conn);
$action = isset($_GET['action']) ? $_GET['action'] : 'taiKhoan';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->taiKhoan();
}