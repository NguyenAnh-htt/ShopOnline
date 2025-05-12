<?php
session_start();
include 'conn/connect.php'; // Kết nối cơ sở dữ liệu

// Kiểm tra người dùng có đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra nếu người dùng đồng ý thanh toán
if (isset($_POST['submit_order']) && isset($_POST['i_read']) && $_POST['i_read'] == 'on') {
    $user_id = $_SESSION['user_id'];
    
    // Lấy dữ liệu từ form
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method']; // Ví dụ: Cash on Delivery

    // Kiểm tra thông tin người dùng
    $checkUserQuery = "SELECT * FROM usermeta WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $checkUserQuery);

    if (mysqli_num_rows($result) > 0) {
        // Người dùng đã tồn tại, cập nhật thông tin
        $updateUserQuery = "UPDATE usermeta SET address='$address', phone='$phone' WHERE user_id='$user_id'";
        mysqli_query($conn, $updateUserQuery);
    } else {
        // Người dùng mới, thêm thông tin
        $insertUserQuery = "INSERT INTO usermeta (user_id, address, phone) VALUES ('$user_id', '$address', '$phone')";
        mysqli_query($conn, $insertUserQuery);
    }

    // Tính tổng tiền giỏ hàng
    $cartTotalQuery = "SELECT SUM(price * quantity) as total FROM cart WHERE user_id='$user_id'";
    $cartTotalResult = mysqli_query($conn, $cartTotalQuery);
    $cartTotal = mysqli_fetch_assoc($cartTotalResult)['total'];

    // Thêm thông tin vào bảng Orders
    $orderQuery = "INSERT INTO orders (user_id, total_amount, payment_method) VALUES ('$user_id', '$cartTotal', '$payment_method')";
    mysqli_query($conn, $orderQuery);
    $order_id = mysqli_insert_id($conn);

    // Thêm chi tiết đơn hàng vào bảng OrderItems
    $cartItemsQuery = "SELECT * FROM cart WHERE user_id='$user_id'";
    $cartItemsResult = mysqli_query($conn, $cartItemsQuery);
    while ($item = mysqli_fetch_assoc($cartItemsResult)) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$product_id', '$quantity', '$price')";
        mysqli_query($conn, $orderItemQuery);
    }

    // Xóa sản phẩm đã mua khỏi giỏ hàng
    $deleteCartQuery = "DELETE FROM cart WHERE user_id='$user_id'";
    mysqli_query($conn, $deleteCartQuery);

    // Chuyển hướng đến trang my-order.php
    header("Location: my-order.php");
    exit();
} else {
    // Nếu checkbox "I read..." chưa được chọn, thông báo lỗi
    echo "Vui lòng đồng ý với các điều kiện thanh toán!";
}
?>
