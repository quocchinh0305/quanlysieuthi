<?php
class ProductModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM sanpham WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getSoLuongTon($id) {
        $stmt = $this->conn->prepare("
            SELECT COALESCE(SUM(GREATEST(sl_quay, 0) + GREATEST(sl_kho, 0)), 0) AS so_luong_ton
            FROM kho_hang
            WHERE id_sp = ?
        ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) $data['so_luong_ton'];
    }
}
?>