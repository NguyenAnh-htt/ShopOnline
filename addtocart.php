<?php
session_start();
include('conn/connect.php');

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") { // Kiểm tra xem form có được submit hay không
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Kiểm tra sản phẩm có tồn tại trong giỏ hàng chưa
    $sql = "SELECT id FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $res = mysqli_query($conn, $sql);

    if ($res && $res->num_rows > 0) {
        // Nếu sản phẩm đã tồn tại, cập nhật số lượng
        $sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id";
        $res = mysqli_query($conn, $sql);
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm sản phẩm mới
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        $res = mysqli_query($conn, $sql);
    }

    // Chuyển hướng đến trang giỏ hàng
    header("Location: card.php");
    exit;
}
?>