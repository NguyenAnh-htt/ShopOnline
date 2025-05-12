<?php
session_start();
require 'conn/connect.php';
include 'template/header.php';
include 'template/nav.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || !isset($_SESSION['type'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_SESSION['type']; // Loại người dùng: admin hoặc member

// Tạo truy vấn dựa trên loại người dùng
if ($type === 'admin') {
    $sql = "SELECT o.id AS order_id, o.timestamp AS order_date, o.orderstatus AS status, 
                   o.totalprice AS total_price, o.paymentmode AS payment_method, 
                   u.username AS customer_name
            FROM orders o
            JOIN users u ON o.user_id = u.id"; // Admin xem tất cả đơn hàng
} else {
    $sql = "SELECT o.id AS order_id, o.timestamp AS order_date, o.orderstatus AS status, 
                   o.totalprice AS total_price, o.paymentmode AS payment_method
            FROM orders o
            WHERE o.user_id = ?"; // Thành viên chỉ xem đơn hàng của mình
}

$stmt = $conn->prepare($sql);

// Gắn tham số nếu là thành viên
if ($type !== 'admin') {
    $stmt->bind_param('i', $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        /* Thêm màu nền cho trang */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
        }

        .container {
            margin-top: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        /* Cải thiện bảng */
        .table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        .table td {
            font-size: 14px;
        }

        /* Các nút hành động */
        .btn-view,
        .btn-cancel {
            padding: 6px 12px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-view {
            background-color: #28a745;
        }

        .btn-cancel {
            background-color: #dc3545;
        }

        /* Hiệu ứng hover cho các nút */
        .btn-view:hover {
            background-color: #218838;
        }

        .btn-cancel:hover {
            background-color: #c82333;
        }

        /* Bảng khi không có dữ liệu */
        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #6c757d;
            padding: 20px;
        }

        /* Điều chỉnh layout khi màn hình nhỏ */
        @media (max-width: 768px) {
            .table th,
            .table td {
                font-size: 12px;
                padding: 8px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><?php echo $type === 'admin' ? "All Orders" : "My Orders"; ?></h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>OrderID</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Total</th>
                    <?php if ($type === 'admin') echo "<th>Customer</th>"; ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['order_id'] . "</td>";
                        echo "<td>" . $row['order_date'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['payment_method'] . "</td>";
                        echo "<td>" . $row['total_price'] . " USD</td>";
                        if ($type === 'admin') {
                            echo "<td>" . $row['customer_name'] . "</td>";
                        }
                        echo "<td><a href='view-order.php?order_id=" . $row['order_id'] . "' class='btn-view'>View</a>";
                        if ($row['status'] !== 'Cancelled') {
                            // echo " | <button class='btn-cancel' onclick='cancelOrder(" . $row['order_id'] . ")'>Cancel</button>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='" . ($type === 'admin' ? 7 : 6) . "' class='no-orders'>No orders found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
include 'template/footer.php';
?>
