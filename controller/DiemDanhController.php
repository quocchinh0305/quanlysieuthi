<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";
require_once __DIR__ . "/../models/DiemDanhModel.php";

class DiemDanhController {
    private $conn;
    private $diemDanhModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->diemDanhModel = new DiemDanhModel($db);
    }

    public function xuLyYeuCau() {
        $hanh_dong = isset($_GET['hanh_dong']) ? $_GET['hanh_dong'] : 'danh_sach';
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        switch ($hanh_dong) {
            case 'kiemtra':
                $this->kiemTraGioCheckIn($id);
                break;
            case 'luu_diemdanh':
                $this->luudiemDanhVoiLyDo($id);
                break;
            default:
                $this->danhSachLichLam();
                break;
        }
    }

    public function danhSachLichLam() {
        $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        
        $week_start = $this->layNgayDauTuan();
        $week_end = $week_start->modify('+6 days');
        
        $ngay_bat_dau = $week_start->format('Y-m-d');
        $ngay_ket_thuc = $week_end->format('Y-m-d');
        $nhan_vien = $this->diemDanhModel->getNhanVienTheoTaiKhoan($current_user_id);

        if ($role === 'admin' || (isset($nhan_vien['chucvu']) && strpos($nhan_vien['chucvu'], 'Quản lý') !== false)) {
            $danh_sach_lich = $this->diemDanhModel->getLichLamTheoTuan($ngay_bat_dau, $ngay_ket_thuc);
        } else {
            $id_nv = $nhan_vien ? $nhan_vien['id'] : '';
            $danh_sach_lich = $this->diemDanhModel->getLichLamNhanVienTheoTuan($id_nv, $ngay_bat_dau, $ngay_ket_thuc);
        }

        $form_nhap_ly_do_id = isset($_GET['nhap_ly_do']) ? intval($_GET['nhap_ly_do']) : null;
        
        $tuan_truoc = $week_start->modify('-7 days')->format('Y-m-d');
        $tuan_sau = (clone $week_start)->modify('+7 days')->format('Y-m-d');
        $tuan_hien_tai = (new DateTimeImmutable('today'))->modify('monday this week')->format('Y-m-d');
        $week_start = $this->layNgayDauTuan(); 

        if ($role === 'storekeeper' || (isset($nhan_vien['chucvu']) && strpos($nhan_vien['chucvu'], 'thủ kho') !== false)) {
            require_once "../views/inventory/diem_danh.php";
        } else {
            require_once "../views/staff/diem_danh.php";
        }
    }

    private function layNgayDauTuan() {
        $ngay = isset($_GET['week']) ? $_GET['week'] : date('Y-m-d');
        $week_start = DateTimeImmutable::createFromFormat('!Y-m-d', $ngay);

        if (!$week_start || $week_start->format('Y-m-d') !== $ngay) {
            $week_start = new DateTimeImmutable('today');
        }

        return $week_start->modify('monday this week');
    }

    private function urlLich($tham_so = array()) {
        $tham_so['week'] = $this->layNgayDauTuan()->format('Y-m-d');
        return 'DiemDanhController.php?' . http_build_query($tham_so);
    }

    public function kiemTraGioCheckIn($id) {
        $res_ca = $this->diemDanhModel->getThongTinCaLam($id);
        
        if ($res_ca->num_rows > 0) {
            $row_ca = $res_ca->fetch_assoc();

            if (in_array($row_ca['trangthai_diemdanh'], ['dunggio', 'dimuon', 'vangmat'], true)) {
                header("Location: " . $this->urlLich());
                exit();
            }

            $ngay_lam = $row_ca['ngaylam'];
            $gio_bat_dau = $row_ca['giobatdau'];
            
            $ngay_hien_tai = date('Y-m-d');
            $gio_hien_tai = date('H:i:s');
            
            if ($ngay_hien_tai > $ngay_lam) {
                header("Location: " . $this->urlLich(array('nhap_ly_do' => $id)));
                exit();
            } elseif ($ngay_hien_tai < $ngay_lam) {
                echo "<script>alert('Chưa đến ngày làm việc!'); window.location='" . htmlspecialchars($this->urlLich(), ENT_QUOTES, 'UTF-8') . "';</script>";
                exit();
            }

            $gio_mo_cong = date('H:i:s', strtotime($gio_bat_dau . ' -30 minutes'));

            if ($gio_hien_tai < $gio_mo_cong) {
                echo "<script>alert('Chưa đến giờ điểm danh! Bạn chỉ được phép check-in sớm tối đa 30 phút trước giờ bắt đầu ca.'); window.location='" . htmlspecialchars($this->urlLich(), ENT_QUOTES, 'UTF-8') . "';</script>";
                exit();
            } elseif ($gio_hien_tai > $gio_bat_dau) {
                header("Location: " . $this->urlLich(array('nhap_ly_do' => $id)));
                exit();
            } else {
                $this->diemDanhModel->danhDauDungGio($id);
                
                header("Location: " . $this->urlLich());
                exit();
            }
        }
        
        header("Location: " . $this->urlLich());
        exit();
    }

    public function luudiemDanhVoiLyDo($id) {
        $ghichu = isset($_POST['ghichu']) ? trim($_POST['ghichu']) : 'Đi muộn không có lý do';
        if (empty($ghichu)) {
            $ghichu = 'Đi muộn không có lý do';
        }

        $this->diemDanhModel->danhDauDiMuon($ghichu, $id);

        header("Location: " . $this->urlLich());
        exit();
    }
}

$controller = new DiemDanhController($conn);
$controller->xuLyYeuCau();
?>
