<?php
class QuanLyDonHangModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDonChoXacNhan() {
        return $this->conn->query("SELECT * FROM donhang WHERE loai_don = 'online' AND trangthai_donhang != 'dahuy' AND (trangthai_donhang = 'choxacnhan' OR trangthai_thanhtoan = 'chuathanhtoan') ORDER BY created_at DESC");
    }

    public function getNhanVienIdTheoTaiKhoan($idTaiKhoan) {
        $stmt = $this->conn->prepare("SELECT id FROM nhanvien WHERE id_taikhoan = ?");
        $stmt->bind_param("s", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getSanPhamTrongDonHang($idDonHang) {
        $stmt = $this->conn->prepare("SELECT id_sp, soluong FROM items_donhang WHERE id_donhang = ?");
        $stmt->bind_param("s", $idDonHang);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function truTonKho($soLuong, $idSanPham) {
        $stmt = $this->conn->prepare("
            SELECT id, sl_kho
            FROM kho_hang
            WHERE id_sp = ? AND sl_kho > 0
            ORDER BY hsd ASC, id ASC
            FOR UPDATE
        ");
        $stmt->bind_param("s", $idSanPham);
        $stmt->execute();
        $result = $stmt->get_result();
        $loHang = [];
        while ($row = $result->fetch_assoc()) {
            $loHang[] = $row;
        }
        $stmt->close();

        $conThieu = (int) $soLuong;
        foreach ($loHang as $lo) {
            if ($conThieu <= 0) {
                break;
            }

            $soLuongTru = min($conThieu, (int) $lo['sl_kho']);
            $idLo = (int) $lo['id'];
            $stmt = $this->conn->prepare("
                UPDATE kho_hang
                SET sl_kho = sl_kho - ?
                WHERE id = ? AND sl_kho >= ?
            ");
            $stmt->bind_param("iii", $soLuongTru, $idLo, $soLuongTru);
            $stmt->execute();
            if ($stmt->affected_rows !== 1) {
                $stmt->close();
                throw new Exception("Không thể cập nhật tồn kho cho sản phẩm $idSanPham.");
            }
            $stmt->close();
            $conThieu -= $soLuongTru;
        }

        if ($conThieu > 0) {
            throw new Exception("Số lượng trong kho không đủ cho sản phẩm $idSanPham.");
        }
    }

    public function xacNhanDonHang($idNhanVien, $idDonHang) {
        $stmt = $this->conn->prepare("UPDATE donhang SET trangthai_donhang = 'daxacnhan', id_nhanvien = ?, thoigian_xacnhan = NOW() WHERE id = ?");
        $stmt->bind_param("ss", $idNhanVien, $idDonHang);
        $stmt->execute();
    }

    public function huyDonHang($id) {
        $stmt = $this->conn->prepare("UPDATE donhang SET trangthai_donhang = 'dahuy', trangthai_thanhtoan = 'dahuy' WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
    }

    public function capNhatTrangThaiThanhToan($idDonHang, $trangThaiThanhToan) {
        $stmt = $this->conn->prepare("UPDATE donhang SET trangthai_thanhtoan = ? WHERE id = ? AND loai_don = 'online' AND trangthai_donhang != 'dahuy'");
        $stmt->bind_param("ss", $trangThaiThanhToan, $idDonHang);
        $stmt->execute();
    }
}
?>
