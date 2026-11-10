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
        $stmt = $this->conn->prepare("SELECT sl_quay, sl_kho FROM kho_hang WHERE id_sp = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$data) {
            return 0;
        }

        return (int)$data['sl_quay'] + (int)$data['sl_kho'];
    }
}
?>