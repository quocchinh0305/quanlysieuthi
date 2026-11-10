<?php
class KhachHangModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getThongTinKhachHang($id) {
        $stmt = $this->conn->prepare("SELECT k.*, n.email FROM khachhang k LEFT JOIN nguoidung n ON k.id_taikhoan = n.id WHERE k.id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function capNhatThongTin($hoTen, $soDienThoai, $diaChi, $id) {
        $stmt = $this->conn->prepare("UPDATE khachhang SET hoten = ?, sodienthoai = ?, diachi = ? WHERE id = ?");
        $stmt->bind_param("ssss", $hoTen, $soDienThoai, $diaChi, $id);
        return $stmt->execute();
    }

    public function xoaTaiKhoanKhachHang($idKhachHang, $idTaiKhoan) {
        $stmt = $this->conn->prepare("DELETE FROM khachhang WHERE id = ?");
        $stmt->bind_param("s", $idKhachHang);
        $stmt->execute();

        $stmt = $this->conn->prepare("DELETE FROM nguoidung WHERE id = ?");
        $stmt->bind_param("s", $idTaiKhoan);
        $stmt->execute();
    }
}
?>
