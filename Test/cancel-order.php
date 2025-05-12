<!-- <?php
session_start();
require 'conn/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'];

// Update order status to 'Cancelled'
$sql = "UPDATE orders SET orderstatus = 'Cancelled' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();

//Trở về my-order.php
header('Location: my-order.php');
exit;
?> -->
