<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../models/inventory/KhoHangModel.php";

class KhoHangController {
    private $conn;
    private $khoHangModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->khoHangModel = new KhoHangModel($db);
        $this->kiemTraQuyenKho();
    }

    private function kiemTraQuyenKho() {
        $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        
        $nv = $this->khoHangModel->getChucVuNhanVien($current_user_id);

        if ($role !== 'admin' && $role !== 'storekeeper' && (!$nv || strpos($nv['chucvu'], 'thủ kho') === false)) {
            echo "<script>alert('Bạn không có quyền truy cập phân hệ kho!'); window.location='/quanlysieuthi/controller/DiemDanhController.php';</script>";
            exit();
        }
    }

    public function xuLyYeuCau() {
        $hanh_dong = isset($_GET['hanh_dong']) ? $_GET['hanh_dong'] : 'danh_sach';

        switch ($hanh_dong) {
            case 'form_nhap':
                $this->hienThiFormNhapKho();
                break;
            case 'xu_ly_nhap':
                $this->xuLyThemPhieuNhap();
                break;
            case 'form_xuat':
                $this->hienThiFormXuatKho();
                break;
            case 'lich_su':
                $this->lichSuKho();
                break;
            case 'xu_ly_xuat':
                $this->xuLyThemPhieuXuat();
                break;
            default:
                $this->danhSachKho();
                break;
        }
    }

    public function danhSachKho() {
        $danh_sach_kho = $this->khoHangModel->getDanhSachKho();

        require_once __DIR__ . "/../../views/inventory/quan_ly_kho.php";
    }

    public function lichSuKho() {
        $lich_su_nhap = $this->khoHangModel->getLichSuNhapKho();
        $lich_su_xuat = $this->khoHangModel->getLichSuXuatKho();

        require_once __DIR__ . "/../../views/inventory/lich_su_kho.php";
    }

    public function hienThiFormNhapKho() {
        $ds_ncc = $this->khoHangModel->getDanhSachNhaCungCap();
        $ds_sp = $this->khoHangModel->getDanhSachSanPham();
        require_once __DIR__ . "/../../views/inventory/lap_phieu_nhap.php";
    }

   public function xuLyThemPhieuNhap() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_nhacungcap = $_POST['id_nhacungcap'];
        $danh_sach_sp = $_POST['id_sp']; 
        $danh_sach_sl = $_POST['soluongnhap']; 
        $danh_sach_gia = $_POST['gianhap']; 
        $danh_sach_hsd = $_POST['hsd']; 
        
        $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        $nhanVien = $this->khoHangModel->getNhanVienIdTheoTaiKhoan($current_user_id);
        $id_nhanvien = $nhanVien ? $nhanVien['id'] : 'NV01';

        $tongtien = 0;
        for ($i = 0; $i < count($danh_sach_sp); $i++) {
            $tongtien += floatval($danh_sach_sl[$i]) * floatval($danh_sach_gia[$i]);
        }
        $ghichu = "Nhập kho nhiều sản phẩm";

        $this->conn->begin_transaction();
        try {
            
            $id_phieu_nhap = $this->khoHangModel->taoPhieuNhap($id_nhacungcap, $id_nhanvien, $tongtien, $ghichu);

            
            for ($i = 0; $i < count($danh_sach_sp); $i++) {
                $id_sp = $danh_sach_sp[$i];
                $soluongnhap = intval($danh_sach_sl[$i]);
                $gianhap = floatval($danh_sach_gia[$i]);
                $hsd = $danh_sach_hsd[$i];

                if ($soluongnhap <= 0) continue;

                
                $this->khoHangModel->themChiTietPhieuNhap($id_phieu_nhap, $id_sp, $soluongnhap, $gianhap, $hsd);

                $ma_lo = 'LO' . date('Ymd') . rand(100, 999);
                $this->khoHangModel->themLoHang($ma_lo, $id_sp, $id_phieu_nhap, $gianhap, $hsd, $soluongnhap);
            }

            $this->conn->commit();
            header("Location: KhoHangController.php?msg=nhap_nhieu_thanh_cong");
            exit();
        } catch (Exception $e) {
            $this->conn->rollback();
            echo "<script>alert('Lỗi nhập kho: " . $e->getMessage() . "'); window.history.back();</script>";
            exit();
        }
    }
}

    public function hienThiFormXuatKho() {
        $ds_kho = $this->khoHangModel->getDanhSachLoCoTheXuat();
        require_once __DIR__ . "/../../views/inventory/lap_phieu_xuat.php";
    }

   public function xuLyThemPhieuXuat() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $danh_sach_lo = $_POST['id_lo']; 
            $danh_sach_sl = $_POST['soluongxuat']; 
            $loai_xuat = $_POST['loai_xuat'];
            $lydo = trim($_POST['lydo']);

            $current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
            $nhanVien = $this->khoHangModel->getNhanVienIdTheoTaiKhoan($current_user_id);
            $id_nhanvien = $nhanVien ? $nhanVien['id'] : 'NV01';

            $this->conn->begin_transaction();
            try {
               
                $id_phieu_xuat = $this->khoHangModel->taoPhieuXuat($id_nhanvien, $loai_xuat, $lydo);

                for ($i = 0; $i < count($danh_sach_lo); $i++) {
                    $id_lo = intval($danh_sach_lo[$i]);
                    $soluongxuat = intval($danh_sach_sl[$i]);

                    if ($soluongxuat <= 0) continue;

                    $lo_hang = $this->khoHangModel->getSoLuongTonLo($id_lo);

                    if (!$lo_hang || $lo_hang['sl_kho'] < $soluongxuat) {
                        throw new Exception("Lô hàng ID $id_lo không đủ số lượng tồn kho để xuất!");
                    }

                   
                    $this->khoHangModel->themChiTietPhieuXuat($id_phieu_xuat, $id_lo, $soluongxuat, $lydo);
                    $this->khoHangModel->capNhatTonLo($soluongxuat, $id_lo, $loai_xuat === 'len_quay_ke');
                }

                $this->conn->commit();
                header("Location: KhoHangController.php?msg=xuat_nhieu_thanh_cong");
                exit();
            } catch (Exception $e) {
                $this->conn->rollback();
                echo "<script>alert('Lỗi xuất kho hàng loạt: " . $e->getMessage() . "'); window.history.back();</script>";
                exit();
            }
        }
    }
}

$controller = new KhoHangController($conn);
$controller->xuLyYeuCau();
?>