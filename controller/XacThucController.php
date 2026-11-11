<?php
$thoigianSession = 604800;
session_set_cookie_params($thoigianSession);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";
require_once "../models/XacThucKhachHang.php";

$xuLyXacThuc = new XacThucKhachHang($conn);
$hanhDong = isset($_GET['action']) ? $_GET['action'] : '';

if ($hanhDong == 'register') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $hoten = trim($_POST['hoten']);
        $email = trim($_POST['email']);
        $matkhau = trim($_POST['matkhau']);
        $sodienthoai = trim($_POST['sodienthoai']);
        $diachi = trim($_POST['diachi']);

        if (empty($hoten) || empty($email) || empty($matkhau) || empty($sodienthoai)) {
            $_SESSION['register_error'] = "Vui lòng điền đầy đủ các thông tin bắt buộc!";
            $_SESSION['show_register_modal'] = true;
            header("Location: ../views/auth/login.php");
            exit();
        }

        if ($xuLyXacThuc->kiemTraEmailTonTai($email)) {
            $_SESSION['register_error'] = "Email này đã được sử dụng!";
            $_SESSION['show_register_modal'] = true; 
            header("Location: ../views/auth/login.php");
            exit();
        }

        if ($xuLyXacThuc->dangKy($hoten, $email, $matkhau, $sodienthoai, $diachi)) {
            $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: ../views/auth/login.php");
            exit();
        } else {
            $_SESSION['register_error'] = "Có lỗi xảy ra khi đăng ký!";
            $_SESSION['show_register_modal'] = true; 
            header("Location: ../views/auth/login.php");
            exit();
        }
    }
} 
elseif ($hanhDong == 'login') {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $email = trim($_POST['email'] ?? '');
        $matkhau = trim($_POST['matkhau'] ?? '');

        if (empty($email) || empty($matkhau)) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ email và mật khẩu!";
            header("Location: ../views/auth/login.php");
            exit();
        }

        $nguoidung = $xuLyXacThuc->dangNhap($email, $matkhau);

        if ($nguoidung) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $nguoidung['id'];
            $_SESSION['user_email'] = $nguoidung['email'];
            $_SESSION['user_role'] = $nguoidung['role'];
            $_SESSION['customer_id'] = $nguoidung['id_khachhang'];
            $_SESSION['user_name'] = $nguoidung['hoten'];

            switch ($nguoidung['role']) {

                case 'admin':
                    header("Location: ../views/admin/dashboard.php");
                    exit();

                case 'staff':
                    header("Location: staff/BanHangController.php");
                    exit();

                case 'storekeeper':
                    header("Location: inventory/KhoHangController.php");
                    exit();

                case 'customer':
                    header("Location: ../views/customer/dashboard.php");
                    exit();

                default:
                    $_SESSION['error'] = "Vai trò không hợp lệ!";
                    header("Location: ../views/auth/login.php");
                    exit();
            }

        } else {
            header("Location: ../views/auth/login.php");
            exit();
        }
    }
}
elseif ($hanhDong == 'logout') {
    session_destroy();
    header("Location: ../views/auth/login.php");
    exit();
}