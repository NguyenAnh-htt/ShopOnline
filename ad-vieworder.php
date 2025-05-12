<?php
session_start();
require 'conn/connect.php';
include 'template/header.php';
include 'template/nav.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Lấy thông tin đơn hàng
    $sql = "
        SELECT 
            o.id AS order_id,
            o.timestamp AS order_date,
            o.orderstatus AS status,
            o.totalprice AS total_price,
            o.paymentmode AS payment_method,
            u.username AS customer_name,
            u.email AS customer_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    // Lấy chi tiết sản phẩm trong đơn hàng
    $sql_items = "
        SELECT 
            oi.product_quantity,
            oi.product_price,
            p.title AS product_name
        FROM orderitems oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param('i', $order_id);
    $stmt_items->execute();
    $order_items = $stmt_items->get_result();
} else {
    header("Location: admin-orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
</head>
<body>

<div class="container">
    <h1>Order Details</h1>
    <h3>Order ID: <?= $order['order_id'] ?></h3>
    <p>Customer: <?= $order['customer_name'] ?> (<?= $order['customer_email'] ?>)</p>
    <p>Date: <?= $order['order_date'] ?></p>
    <p>Status: <?= $order['status'] ?></p>
    <p>Payment: <?= $order['payment_method'] ?></p>
    <p>Total: <?= $order['total_price'] ?> USD</p>

    <h3>Products</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $order_items->fetch_assoc()): ?>
                <tr>
                    <td><?= $item['product_name'] ?></td>
                    <td><?= $item['product_quantity'] ?></td>
                    <td><?= $item['product_price'] ?> USD</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
$stmt_items->close();
$conn->close();
?>
