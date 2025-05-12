<?php
session_start();
require 'conn/connect.php';
include 'template/header.php';
include 'template/nav.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['type'])) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['user_id'];
$type = $_SESSION['type']; // admin hoặc member

// Truy vấn thông tin đơn hàng
if ($type === 'admin') {
    // Admin có thể xem mọi đơn hàng
    $sql = "SELECT o.id AS order_id, o.timestamp AS order_date, o.orderstatus AS status, 
                   o.totalprice AS total_price, o.paymentmode AS payment_method, 
                   u.username AS customer_name
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?";
} else {
    // Thành viên chỉ được xem đơn hàng của mình
    $sql = "SELECT o.id AS order_id, o.timestamp AS order_date, o.orderstatus AS status, 
                   o.totalprice AS total_price, o.paymentmode AS payment_method
            FROM orders o
            WHERE o.id = ? AND o.user_id = ?";
}

$stmt = $conn->prepare($sql);
if ($type === 'admin') {
    $stmt->bind_param('i', $order_id);
} else {
    $stmt->bind_param('ii', $order_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Kiểm tra xem đơn hàng có tồn tại hay không
if (!$order) {
    echo "<div class='container'><h1>Order not found</h1></div>";
    exit;
}

// Truy vấn chi tiết sản phẩm trong đơn hàng
$sql_items = "SELECT p.title AS product_name, oi.product_price AS unit_price, 
                     oi.product_quantity AS quantity, 
                     (oi.product_price * oi.product_quantity) AS subtotal
              FROM orderitems oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param('i', $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <style>
        body {
            background-color: #f9f9f9;
        }

        .container {
            margin-top: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .order-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .order-info p {
            font-size: 16px;
            line-height: 1.6;
            margin: 5px 0;
        }

        .table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .table td {
            text-align: center;
        }

        .table {
            margin-top: 20px;
        }

        .btn-back {
            margin-top: 20px;
            display: block;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Order Details</h1>

        <!-- Thông tin đơn hàng -->
        <div class="order-info">
            <h4>Order Information</h4>
            <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
            <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
            <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
            <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
            <p><strong>Total Price:</strong> <?php echo $order['total_price']; ?> USD</p>
            <?php if ($type === 'admin') { ?>
                <p><strong>Customer Name:</strong> <?php echo $order['customer_name']; ?></p>
            <?php } ?>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="order-items">
            <h4>Items in Order</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($items_result->num_rows > 0) {
                        while ($item = $items_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $item['product_name'] . "</td>";
                            echo "<td>" . $item['unit_price'] . " USD</td>";
                            echo "<td>" . $item['quantity'] . "</td>";
                            echo "<td>" . $item['subtotal'] . " USD</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No items found in this order</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Nút quay lại -->
        <div class="btn-back">
            <a href="<?php echo ($type === 'admin') ? 'ad-index.php' : 'index.php'; ?>" class="btn btn-primary">Back to Orders</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>


<?php
$stmt->close();
$stmt_items->close();
$conn->close();
include 'template/footer.php';
?>