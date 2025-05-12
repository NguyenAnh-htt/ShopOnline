<!-- <?php
// session_start();
// require 'conn/connect.php';

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     // Kiểm tra nếu giỏ hàng không trống
//     if (isset($_SESSION['cart_items']) && count($_SESSION['cart_items']) > 0) {
//         // Lấy dữ liệu người dùng và giỏ hàng
//         $user_id = $_SESSION['user_id'];
//         $name = $_POST['name'];
//         $address = $_POST['address'];
//         $phone = $_POST['phone'];
//         $payment_method = $_POST['payment_method'];
//         $total_price = $_SESSION['total_price'];

//         // Lưu đơn hàng vào bảng orders
//         $stmt = $conn->prepare("INSERT INTO orders (user_id, name, address, phone, payment_method, total_price) 
//                                 VALUES (?, ?, ?, ?, ?, ?)");
//         $stmt->bind_param("issssi", $user_id, $name, $address, $phone, $payment_method, $total_price);
//         $stmt->execute();

//         $order_id = $stmt->insert_id; // Lấy ID đơn hàng vừa tạo

//         // Lưu chi tiết đơn hàng vào bảng order_items
//         foreach ($_SESSION['cart_items'] as $item) {
//             $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
//                                     VALUES (?, ?, ?, ?)");
//             $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
//             $stmt->execute();
//         }

//         // Xóa giỏ hàng sau khi đặt hàng
//         unset($_SESSION['cart_items']);
//         unset($_SESSION['total_price']);

//         // Chuyển hướng người dùng đến trang xác nhận đơn hàng
//         header("Location: order_confirmation.php?order_id=$order_id");
//         exit;
//     } else {
//         echo '<div class="alert alert-danger"><strong>Giỏ hàng của bạn hiện tại trống</strong></div>';
//     }
// }
// ?> -->
<?php
session_start();
require 'conn/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra nếu giỏ hàng không trống
    if (isset($_SESSION['cart_items']) && count($_SESSION['cart_items']) > 0) {
        // Lấy dữ liệu người dùng và giỏ hàng
        $user_id = $_SESSION['user_id'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $payment_method = $_POST['payment_method'];
        $total_price = $_SESSION['total_price'];

        // // Lưu đơn hàng vào bảng orders
        // $stmt = $conn->prepare("INSERT INTO orders (user_id, name, address, phone, payment_method, total_price) 
        //                         VALUES (?, ?, ?, ?, ?, ?)");
        // $stmt->bind_param("issssi", $user_id, $name, $address, $phone, $payment_method, $total_price);
        // $stmt->execute();

        // $order_id = $stmt->insert_id; // Lấy ID đơn hàng vừa tạo

        // Lưu chi tiết đơn hàng vào bảng order_items
        foreach ($_SESSION['cart_items'] as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price']; // Giá sản phẩm đã lưu trong giỏ hàng

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                    VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        }

        // Xóa giỏ hàng sau khi đặt hàng
        unset($_SESSION['cart_items']);
        unset($_SESSION['total_price']);

        // Chuyển hướng người dùng đến trang xác nhận đơn hàng
        header("Location: order_confirmation.php?order_id=$order_id");
        exit;
    } else {
        echo '<div class="alert alert-danger"><strong>Giỏ hàng của bạn hiện tại trống</strong></div>';
    }
}
?>
