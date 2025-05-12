<?php
session_start();
include('conn\connect.php');

// Kiểm tra nếu người dùng không phải admin
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php"); // Chuyển hướng về trang chủ
    exit();
}

// Lấy id sản phẩm từ URL
$product_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Kiểm tra sản phẩm đã từng được mua hay chưa
$sql_check = "SELECT COUNT(*) AS total FROM orderitems WHERE product_id = $product_id";
$result = mysqli_query($conn, $sql_check);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row['total'] > 0) {
        // Nếu sản phẩm đã từng được mua, hiển thị thông báo
        echo "<script>alert('Không thể xóa sản phẩm vì đã từng được mua.'); window.location.href = 'ad-index.php';</script>";
        exit();
    } else {
        // Nếu sản phẩm chưa từng được mua, tiến hành xóa
        $sql_delete = "DELETE FROM products WHERE id = $product_id";
        if (mysqli_query($conn, $sql_delete)) {
            echo "<script>alert('Sản phẩm đã được xóa thành công!'); window.location.href = 'ad-index.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi xóa sản phẩm.'); window.location.href = 'ad-index.php';</script>";
        }
    }
} else {
    echo "<script>alert('Có lỗi xảy ra khi kiểm tra sản phẩm.'); window.location.href = 'ad-index.php';</script>";
}
?>
