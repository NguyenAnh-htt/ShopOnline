<?php
session_start();
require 'conn\connect.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'template\header.php';
include 'template\nav.php';

// Lấy danh sách các đơn hàng và thông tin khách hàng
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
    ORDER BY o.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders - Admin</title>
    <style>
        .container { margin-top: 30px; }
        .donhang { text-align: center; margin-bottom: 20px; color: #007bff; }
        .table th, .table td { text-align: center; vertical-align: middle; }
        .table th { background-color: #f8f9fa; }
        .btn-view, .btn-cancel {
            text-decoration: none; padding: 5px 10px; border-radius: 5px;
        }
        .btn-view { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #dc3545; color: white; }
        .btn-view:hover, .btn-cancel:hover { opacity: 0.8; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="donhang">All Orders</h1>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Date</th>
                <th>Status</th>
                <th>Payment method</th>
                <th>Total</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['order_id'] . "</td>";
                    echo "<td>" . $row['customer_name'] . "</td>";
                    echo "<td>" . $row['customer_email'] . "</td>";
                    echo "<td>" . $row['order_date'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>" . $row['payment_method'] . "</td>";
                    echo "<td>" . $row['total_price'] . " USD</td>";
                    echo "<td><a href='ad-vieworder.php?order_id=" . $row['order_id'] . "' class='btn-view'>View</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
include 'template\footer.php';
?>
