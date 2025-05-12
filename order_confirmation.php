<?php
session_start();
require 'conn/connect.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Lấy thông tin đơn hàng từ bảng orders
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo "<h1>Đơn hàng của bạn đã được xác nhận!</h1>";
        echo "<p>Mã đơn hàng: " . $order['order_id'] . "</p>";
        echo "<p>Tổng giá trị đơn hàng: " . number_format($order['total_price'], 2) . " VND</p>";
        echo "<p>Phương thức thanh toán: " . $order['payment_method'] . "</p>";
        // Các thông tin khác nếu cần
    } else {
        echo "<p>Đơn hàng không tồn tại.</p>";
    }
}
?>