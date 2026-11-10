<?php
class BanHangModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDanhSachSanPham() {
        return $this->conn->query("SELECT * FROM sanpham");
    }

    public function getSanPhamTheoMaVach($maVach) {
        $stmt = $this->conn->prepare("SELECT * FROM sanpham WHERE mavach = ? OR id = ?");
        $stmt->bind_param("ss", $maVach, $maVach);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getNhanVienIdTheoTaiKhoan($idTaiKhoan) {
        $stmt = $this->conn->prepare("SELECT id FROM nhanvien WHERE id_taikhoan = ?");
        $stmt->bind_param("s", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getThongKeBanHangHomNay($idNhanVien) {
        $stmt = $this->conn->prepare("
            SELECT
                COUNT(*) AS so_don,
                COALESCE(SUM(thucthu), 0) AS tong_doanh_thu,
                COUNT(DISTINCT id_khachhang) AS so_khach_hang
            FROM donhang
            WHERE id_nhanvien = ?
                AND loai_don IN ('tai_quay', 'online')
                AND trangthai_donhang != 'dahuy'
                AND trangthai_thanhtoan = 'dathanhtoan'
                AND DATE(COALESCE(thoigian_xacnhan, created_at)) = CURDATE()
        ");
        $stmt->bind_param("s", $idNhanVien);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getLichSuBanHang($idNhanVien) {
        $stmt = $this->conn->prepare("
            SELECT id, loai_don, COALESCE(thoigian_xacnhan, created_at) AS thoigian_ban,
                ten_nguoinhan, sdt_nguoinhan, tongtien, thucthu,
                phuongthuc_thanhtoan, trangthai_thanhtoan, trangthai_donhang
            FROM donhang
            WHERE id_nhanvien = ? AND loai_don IN ('tai_quay', 'online')
            AND DATE(COALESCE(thoigian_xacnhan, created_at)) = CURDATE()
            ORDER BY COALESCE(thoigian_xacnhan, created_at) DESC
        ");
        $stmt->bind_param("s", $idNhanVien);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getKhachHangIdTheoSoDienThoai($soDienThoai) {
        $stmt = $this->conn->prepare("SELECT id FROM khachhang WHERE sodienthoai = ?");
        $stmt->bind_param("s", $soDienThoai);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function themKhachHang($id, $ten, $soDienThoai) {
        $stmt = $this->conn->prepare("INSERT INTO khachhang (id, hoten, sodienthoai, diachi) VALUES (?, ?, ?, 'Mua trực tiếp tại siêu thị')");
        $stmt->bind_param("sss", $id, $ten, $soDienThoai);
        $stmt->execute();
    }

    public function taoDonHang($idDonHang, $idKhachHang, $idNhanVien, $loaiDon, $trangThaiDon, $tenKhach, $soDienThoai, $tongTien, $phuongThuc, $trangThaiThanhToan) {
        $stmt = $this->conn->prepare("INSERT INTO donhang (id, id_khachhang, id_nhanvien, loai_don, trangthai_donhang, ten_nguoinhan, sdt_nguoinhan, diachi, tongtien, thucthu, phuongthuc_thanhtoan, trangthai_thanhtoan, thoigian_xacnhan) VALUES (?, ?, ?, ?, ?, ?, ?, 'Tại siêu thị', ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssssssss", $idDonHang, $idKhachHang, $idNhanVien, $loaiDon, $trangThaiDon, $tenKhach, $soDienThoai, $tongTien, $tongTien, $phuongThuc, $trangThaiThanhToan);
        $stmt->execute();
    }

    public function themSanPhamDonHang($idDonHang, $idSanPham, $soLuong, $gia) {
        $stmt = $this->conn->prepare("INSERT INTO items_donhang (id_donhang, id_sp, soluong, gia) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $idDonHang, $idSanPham, $soLuong, $gia);
        $stmt->execute();
    }

    public function truTonQuay($soLuong, $idSanPham) {
        $stmt = $this->conn->prepare("UPDATE kho_hang SET sl_quay = sl_quay - ? WHERE id_sp = ?");
        $stmt->bind_param("is", $soLuong, $idSanPham);
        $stmt->execute();
    }
}
?>
