<?php
class DiemDanhModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getNhanVienTheoTaiKhoan($idTaiKhoan) {
        $stmt = $this->conn->prepare("SELECT id, hoten, chucvu FROM nhanvien WHERE id_taikhoan = ?");
        $stmt->bind_param("s", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getLichLamTheoTuan($ngayBatDau, $ngayKetThuc) {
        $stmt = $this->conn->prepare("SELECT l.*, n.hoten as ten_nhanvien, c.tenca, c.giobatdau, c.gioketthuc FROM lichlamviec l JOIN nhanvien n ON l.id_nhanvien = n.id JOIN calamviec c ON l.id_ca = c.id WHERE l.ngaylam BETWEEN ? AND ? ORDER BY l.ngaylam ASC, c.giobatdau ASC");
        $stmt->bind_param("ss", $ngayBatDau, $ngayKetThuc);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getLichLamNhanVienTheoTuan($idNhanVien, $ngayBatDau, $ngayKetThuc) {
        $stmt = $this->conn->prepare("SELECT l.*, n.hoten as ten_nhanvien, c.tenca, c.giobatdau, c.gioketthuc FROM lichlamviec l JOIN nhanvien n ON l.id_nhanvien = n.id JOIN calamviec c ON l.id_ca = c.id WHERE l.id_nhanvien = ? AND l.ngaylam BETWEEN ? AND ? ORDER BY l.ngaylam ASC, c.giobatdau ASC");
        $stmt->bind_param("sss", $idNhanVien, $ngayBatDau, $ngayKetThuc);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getThongTinCaLam($id) {
        $stmt = $this->conn->prepare("SELECT l.ngaylam, l.trangthai_diemdanh, c.giobatdau FROM lichlamviec l JOIN calamviec c ON l.id_ca = c.id WHERE l.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function danhDauDungGio($id) {
        $stmt = $this->conn->prepare("UPDATE lichlamviec SET trangthai_diemdanh = 'dunggio', thoigian_diemdanh = NOW(), ghichu = 'Điểm danh đúng giờ' WHERE id = ? AND (trangthai_diemdanh IS NULL OR trangthai_diemdanh NOT IN ('dunggio', 'dimuon', 'vangmat'))");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function danhDauDiMuon($ghiChu, $id) {
        $stmt = $this->conn->prepare("UPDATE lichlamviec SET trangthai_diemdanh = 'dimuon', thoigian_diemdanh = NOW(), ghichu = ? WHERE id = ? AND (trangthai_diemdanh IS NULL OR trangthai_diemdanh NOT IN ('dunggio', 'dimuon', 'vangmat'))");
        $stmt->bind_param("si", $ghiChu, $id);
        $stmt->execute();
    }
}
?>
