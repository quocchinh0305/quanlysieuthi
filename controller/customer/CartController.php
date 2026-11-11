
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../models/customer/ProductModel.php";

class CartController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'add':
                $this->add();
                break;

            case 'update':
                $this->update();
                break;

            case 'delete':
                $this->delete();
                break;

            default:
                $this->index();
                break;
        }
    }

    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $tong_tien = 0;

        foreach ($cart as $item) {
            $tong_tien += $item['giasp'] * $item['soluong'];
        }

        require_once __DIR__ . "/../../views/customer/cart.php";
    }

    public function add() {
        $id = trim($_GET['id'] ?? '');

        if ($id === '') {
            header("Location: /quanlysieuthi/views/customer/dashboard.php");
            exit();
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $product = $this->productModel->getProductById($id);

        if (!$product) {
            $_SESSION['error'] = "Sản phẩm không tồn tại!";
            header("Location: /quanlysieuthi/views/customer/dashboard.php");
            exit();
        }

        $so_luong_ton = $this->productModel->getSoLuongTon($id);

        $so_luong_hien_tai = $_SESSION['cart'][$id]['soluong'] ?? 0;
        $so_luong_moi = $so_luong_hien_tai + 1;

        if ($so_luong_ton <= 0) {
            $_SESSION['error'] = "Sản phẩm đã hết hàng!";
        } elseif ($so_luong_moi > $so_luong_ton) {
            $_SESSION['error'] = "Không đủ hàng! Hiện chỉ còn "
                . $so_luong_ton . " sản phẩm trong kho và quầy.";
        } else {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['soluong']++;
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => $product['id'],
                    'tensp' => $product['tensp'],
                    'giasp' => $product['giasp'],
                    'donvi' => $product['donvi'],
                    'soluong' => 1
                ];
            }

            $_SESSION['success'] = "Thêm sản phẩm vào giỏ hàng thành công!";
        }

        header("Location: /quanlysieuthi/views/customer/dashboard.php");
        exit();
    }

    public function update() {
        if (isset($_POST['soluong']) && is_array($_POST['soluong'])) {
            foreach ($_POST['soluong'] as $id => $sl) {
                $id = trim((string)$id);
                $sl = filter_var($sl, FILTER_VALIDATE_INT);

                if (!isset($_SESSION['cart'][$id])) {
                    continue;
                }

                if ($sl === false || $sl < 1) {
                    $_SESSION['error'] = "Số lượng sản phẩm không hợp lệ!";
                    continue;
                }

                $so_luong_ton = $this->productModel->getSoLuongTon($id);

                if ($sl > $so_luong_ton) {
                    $_SESSION['error'] = "Sản phẩm "
                        . $_SESSION['cart'][$id]['tensp']
                        . " chỉ còn " . $so_luong_ton
                        . " sản phẩm trong kho và quầy.";

                    continue;
                }

                $_SESSION['cart'][$id]['soluong'] = $sl;
            }

            if (!isset($_SESSION['error'])) {
                $_SESSION['success'] = "Cập nhật giỏ hàng thành công!";
            }
        }

        header("Location: /quanlysieuthi/controller/customer/CartController.php?action=index");
        exit();
    }

    public function delete() {
        $id = trim($_GET['id'] ?? '');

        if ($id !== '' && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng!";
        }

        header("Location: /quanlysieuthi/controller/customer/CartController.php?action=index");
        exit();
    }
}

$cartController = new CartController($conn);
$cartController->handleRequest();
?>
