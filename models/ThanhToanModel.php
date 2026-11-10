<?php
class ThanhToanModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getKhachHangIdTheoSoDienThoai($soDienThoai) {
        $stmt = $this->conn->prepare("SELECT id FROM khachhang WHERE sodienthoai = ?");
        $stmt->bind_param("s", $soDienThoai);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function themKhachHang($id, $hoTen, $soDienThoai, $diaChi) {
        $stmt = $this->conn->prepare("INSERT INTO khachhang (id, hoten, sodienthoai, diachi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $id, $hoTen, $soDienThoai, $diaChi);
        $stmt->execute();
    }

    public function taoDonHang($id, $idKhachHang, $loaiDon, $trangThaiDonHang, $tenNguoiNhan, $soDienThoai, $diaChi, $tongTien, $phuongThuc, $trangThaiThanhToan) {
        $stmt = $this->conn->prepare("INSERT INTO donhang (id, id_khachhang, loai_don, trangthai_donhang, ten_nguoinhan, sdt_nguoinhan, diachi, tongtien, thucthu, phuongthuc_thanhtoan, trangthai_thanhtoan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $id, $idKhachHang, $loaiDon, $trangThaiDonHang, $tenNguoiNhan, $soDienThoai, $diaChi, $tongTien, $tongTien, $phuongThuc, $trangThaiThanhToan);
        $stmt->execute();
    }

    public function themSanPhamDonHang($idDonHang, $idSanPham, $soLuong, $gia) {
        $stmt = $this->conn->prepare("INSERT INTO items_donhang (id_donhang, id_sp, soluong, gia) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $idDonHang, $idSanPham, $soLuong, $gia);
        $stmt->execute();
    }

    public function getDonHang($id) {
        $stmt = $this->conn->prepare("SELECT * FROM donhang WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getSanPhamDonHang($id) {
        $stmt = $this->conn->prepare("SELECT i.*, s.tensp, s.donvi FROM items_donhang i JOIN sanpham s ON i.id_sp = s.id WHERE i.id_donhang = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getDonHangTheoKhachHang($idKhachHang) {
        $stmt = $this->conn->prepare("SELECT * FROM donhang WHERE id_khachhang = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $idKhachHang);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTrangThaiDonHang($id) {
        $stmt = $this->conn->prepare("SELECT trangthai_donhang FROM donhang WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function huyDonHang($id) {
        $stmt = $this->conn->prepare("UPDATE donhang SET trangthai_donhang = 'dahuy' WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
    }

    public function getChiTietDonHangDeMuaLai($id) {
        $stmt = $this->conn->prepare("SELECT i.id_sp, i.soluong, i.gia, s.tensp, s.donvi FROM items_donhang i LEFT JOIN sanpham s ON i.id_sp = s.id WHERE i.id_donhang = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
