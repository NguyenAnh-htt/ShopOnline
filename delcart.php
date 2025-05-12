<?php
session_start();
require 'conn/connect.php'; // Đảm bảo kết nối tới cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (isset($_GET['product_id']) && isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $product_id = intval($_GET['product_id']); // Chuyển giá trị thành số nguyên để tránh lỗi SQL Injection

    // Kiểm tra xem sản phẩm có tồn tại trong giỏ hàng của người dùng này không
    $sql_check = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $result = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result) > 0) {
        // Xóa sản phẩm khỏi giỏ hàng
        $sql_delete = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id";
        if (mysqli_query($conn, $sql_delete)) {
            header('Location: card.php'); // Quay lại trang giỏ hàng sau khi xóa
            exit;
    } else {
        echo "Lỗi khi xóa sản phẩm: " . mysqli_error($conn);
    }
} else {
    // Nếu không có tham số remove, chuyển hướng về trang giỏ hàng
    header('Location: card.php');
    exit;
}
}
$conn->close(); // Đảm bảo đóng kết nối sau khi thực hiện xong
?>