<?php
class KhoHangModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getChucVuNhanVien($idTaiKhoan) {
        $stmt = $this->conn->prepare("SELECT chucvu FROM nhanvien WHERE id_taikhoan = ?");
        $stmt->bind_param("s", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getDanhSachKho() {
        return $this->conn->query("
            SELECT k.*, sp.tensp, sp.donvi, ncc.tennhacungcap
            FROM kho_hang k
            JOIN sanpham sp ON k.id_sp = sp.id
            LEFT JOIN nhacungcap ncc ON sp.id_nhacungcap = ncc.id
            WHERE k.sl_kho > 0 OR k.sl_quay > 0
            ORDER BY k.hsd ASC
        ");
    }

    public function getDanhSachNhaCungCap() {
        return $this->conn->query("SELECT * FROM nhacungcap");
    }

    public function getDanhSachSanPham() {
        return $this->conn->query("SELECT * FROM sanpham");
    }

    public function getDanhSachLoCoTheXuat() {
        return $this->conn->query("SELECT k.*, sp.tensp FROM kho_hang k JOIN sanpham sp ON k.id_sp = sp.id WHERE k.sl_kho > 0 ORDER BY k.hsd ASC");
    }

    public function getLichSuNhapKho() {
        return $this->conn->query("
            SELECT pn.id AS id_phieu, pn.ngaynhap, pn.tongtien, pn.ghichu,
                ncc.tennhacungcap, nv.hoten AS ten_nhanvien,
                ct.soluongnhap, ct.gianhap, ct.hsd, sp.tensp, sp.donvi
            FROM phieunhap pn
            LEFT JOIN nhacungcap ncc ON pn.id_nhacungcap = ncc.id
            LEFT JOIN nhanvien nv ON pn.id_nhanvien = nv.id
            LEFT JOIN chitietphieunhap ct ON pn.id = ct.id_phieu
            LEFT JOIN sanpham sp ON ct.id_sp = sp.id
            ORDER BY pn.ngaynhap DESC, pn.id DESC
        ");
    }

    public function getLichSuXuatKho() {
        return $this->conn->query("
            SELECT px.id AS id_phieu, px.ngayxuat, px.loai_xuat, px.ghichu,
                nv.hoten AS ten_nhanvien, ct.soluongxuat, ct.lydo,
                kh.ma_lo, sp.tensp, sp.donvi
            FROM phieuxuat px
            LEFT JOIN nhanvien nv ON px.id_nhanvien = nv.id
            LEFT JOIN chitietphieuxuat ct ON px.id = ct.id_phieu
            LEFT JOIN kho_hang kh ON ct.id_lo = kh.id
            LEFT JOIN sanpham sp ON kh.id_sp = sp.id
            ORDER BY px.ngayxuat DESC, px.id DESC
        ");
    }

    public function getNhanVienIdTheoTaiKhoan($idTaiKhoan) {
        $stmt = $this->conn->prepare("SELECT id FROM nhanvien WHERE id_taikhoan = ?");
        $stmt->bind_param("s", $idTaiKhoan);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function taoPhieuNhap($idNhaCungCap, $idNhanVien, $tongTien, $ghiChu) {
        $stmt = $this->conn->prepare("INSERT INTO phieunhap (id_nhacungcap, id_nhanvien, ngaynhap, tongtien, ghichu) VALUES (?, ?, NOW(), ?, ?)");
        $stmt->bind_param("ssds", $idNhaCungCap, $idNhanVien, $tongTien, $ghiChu);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function themChiTietPhieuNhap($idPhieu, $idSanPham, $soLuong, $giaNhap, $hsd) {
        $stmt = $this->conn->prepare("INSERT INTO chitietphieunhap (id_phieu, id_sp, soluongnhap, gianhap, hsd) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isids", $idPhieu, $idSanPham, $soLuong, $giaNhap, $hsd);
        $stmt->execute();
    }

    public function themLoHang($maLo, $idSanPham, $idPhieu, $giaNhap, $hsd, $soLuong) {
        $stmt = $this->conn->prepare("INSERT INTO kho_hang (ma_lo, id_sp, id_phieunhap, gianhap, hsd, sl_kho, sl_quay, trangthai) VALUES (?, ?, ?, ?, ?, ?, 0, 'con_hang')");
        $stmt->bind_param("ssidsi", $maLo, $idSanPham, $idPhieu, $giaNhap, $hsd, $soLuong);
        $stmt->execute();
    }

    public function taoPhieuXuat($idNhanVien, $loaiXuat, $ghiChu) {
        $stmt = $this->conn->prepare("INSERT INTO phieuxuat (id_nhanvien, ngayxuat, loai_xuat, ghichu) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("sss", $idNhanVien, $loaiXuat, $ghiChu);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function getSoLuongTonLo($idLo) {
        $stmt = $this->conn->prepare("SELECT sl_kho FROM kho_hang WHERE id = ?");
        $stmt->bind_param("i", $idLo);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function themChiTietPhieuXuat($idPhieu, $idLo, $soLuong, $lyDo) {
        $stmt = $this->conn->prepare("INSERT INTO chitietphieuxuat (id_phieu, id_lo, soluongxuat, lydo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $idPhieu, $idLo, $soLuong, $lyDo);
        $stmt->execute();
    }

    public function capNhatTonLo($soLuong, $idLo, $chuyenLenQuay) {
        if ($chuyenLenQuay) {
            $stmt = $this->conn->prepare("UPDATE kho_hang SET sl_kho = sl_kho - ?, sl_quay = sl_quay + ? WHERE id = ?");
            $stmt->bind_param("iii", $soLuong, $soLuong, $idLo);
        } else {
            $stmt = $this->conn->prepare("UPDATE kho_hang SET sl_kho = sl_kho - ?, trangthai = IF(sl_kho - ? = 0, 'da_huy', trangthai) WHERE id = ?");
            $stmt->bind_param("iii", $soLuong, $soLuong, $idLo);
        }
        $stmt->execute();
    }
}
?>
