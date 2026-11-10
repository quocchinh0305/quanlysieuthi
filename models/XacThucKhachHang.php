<?php
class XacThucKhachHang {
    private $ketNoiDB;

    public function __construct($db) { 
        $this->ketNoiDB = $db; 
    }

    public function kiemTraEmailTonTai($email) {
        $stmt = $this->ketNoiDB->prepare("SELECT id FROM nguoidung WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $ketQua = $stmt->get_result();
        return $ketQua->num_rows > 0;
    }

    public function dangKy($hoten, $email, $matkhau, $sodienthoai, $diachi) {
        $this->ketNoiDB->begin_transaction();
        try {
            $res_id = $this->ketNoiDB->query("SELECT id FROM nguoidung ORDER BY CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC LIMIT 1");
            if ($res_id->num_rows > 0) {
                $row_id = $res_id->fetch_assoc();
                $num = intval(substr($row_id['id'], 2)) + 1;
                $maTaikhoanMoi = "TK" . str_pad($num, max(2, strlen($num)), "0", STR_PAD_LEFT);
            } else {
                $maTaikhoanMoi = "TK01";
            }

            $matkhauMaHoa = password_hash($matkhau, PASSWORD_DEFAULT);

            $stmt_nd = $this->ketNoiDB->prepare("INSERT INTO nguoidung (id, email, matkhau, role, trangthai) VALUES (?, ?, ?, 'customer', 'hoatdong')");
            $stmt_nd->bind_param("sss", $maTaikhoanMoi, $email, $matkhauMaHoa);
            $stmt_nd->execute();

            $res_kh = $this->ketNoiDB->query("SELECT id FROM khachhang ORDER BY CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC LIMIT 1");
            if ($res_kh->num_rows > 0) {
                $row_kh = $res_kh->fetch_assoc();
                $num_kh = intval(substr($row_kh['id'], 2)) + 1;
                $maKhachhangMoi = "KH" . str_pad($num_kh, max(2, strlen($num_kh)), "0", STR_PAD_LEFT);
            } else {
                $maKhachhangMoi = "KH01";
            }
            
            $stmt_kh = $this->ketNoiDB->prepare("INSERT INTO khachhang (id, id_taikhoan, hoten, sodienthoai, diachi, hangthanhvien) VALUES (?, ?, ?, ?, ?, 'Dong')");
            $stmt_kh->bind_param("sssss", $maKhachhangMoi, $maTaikhoanMoi, $hoten, $sodienthoai, $diachi);
            $stmt_kh->execute();

            $this->ketNoiDB->commit();
            return true;
        } catch (Exception $e) {
            $this->ketNoiDB->rollback();
            return false;
        }
    }

  public function dangNhap($email, $matkhau) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

   $stmt = $this->ketNoiDB->prepare("
            SELECT nd.*, kh.id AS id_khachhang, kh.hoten 
            FROM nguoidung nd 
            LEFT JOIN khachhang kh ON nd.id = kh.id_taikhoan 
            WHERE nd.email = ?
        ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Email không tồn tại trong hệ thống!";
        return false;
    }

    $user = $result->fetch_assoc();

    if ($matkhau !== $user['matkhau']) {
        $_SESSION['error'] = "Mật khẩu không chính xác!";
        return false;
    }

    $roles = ['admin', 'staff', 'storekeeper', 'customer'];

    if (!in_array($user['role'], $roles, true)) {
        $_SESSION['error'] = "Vai trò tài khoản không hợp lệ!";
        return false;
    }

    $customer_id = null;

    if ($user['role'] === 'customer') {

        $stmt_kh = $this->ketNoiDB->prepare(
            "SELECT id FROM khachhang WHERE id_taikhoan = ?"
        );

        $stmt_kh->bind_param("s", $user['id']);
        $stmt_kh->execute();

        $res_kh = $stmt_kh->get_result();

        if ($row_kh = $res_kh->fetch_assoc()) {
            $customer_id = $row_kh['id'];
        }

        $stmt_kh->close();
    }

    $user['id_khachhang'] = $customer_id;

    $stmt->close();

    return $user;
}
}
?>