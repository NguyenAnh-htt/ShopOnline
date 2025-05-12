<style>
    /* Tạo đường kẻ ngang cho bảng */
    table {
        width: 100%;
        text-align: center;
        border-collapse: collapse;
        /* Gộp các đường biên để không có khoảng cách */
    }

    /* Đường kẻ ngang trong bảng */
    table th,
    table td {
        border-bottom: 1px solid #ddd;
        /* Đường kẻ ngang */
        padding: 10px;
    }

    /* Định dạng thêm cho tiêu đề bảng */
    table th {
        background-color: #f9f9f9;
        font-weight: bold;
    }

    .btn-primarys {
        margin-top: 10px;
        background-color: darkorange;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
    }

    .btn-primarys:hover {
        background-color: #ff5e62;
    }
</style>

<?php
session_start();
require 'conn/connect.php';
include 'template/header.php';
include 'template/nav.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.product_id, c.quantity, p.title, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";

$res = mysqli_query($conn, $sql);

?>
<div class="container">
    <h1>View Cart</h1>
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($res->num_rows > 0) {
            ?>
                <form method="POST" action="checkout.php" id="cart-form">
                    <table class="table" style="width:90%" align="center">
                        <tr>
                            <th>S.Number</th>
                            <th>Check</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        $i = 1;
                        while ($row = $res->fetch_assoc()) {
                            $product_id = $row['product_id'];
                            $product_name = $row['title'];
                            $quantity = $row['quantity'];
                            $price = $row['price'];
                            $subtotal = $price * $quantity;
                        ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="item-check"
                                        data-subtotal="<?php echo $subtotal; ?>"
                                        name="selected_items[]"
                                        value="<?php echo $product_id . '|' . $product_name . '|' . $quantity . '|' . $price; ?>">
                                </td>
                                <td><?php echo $product_name; ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td><?php echo number_format($price, 2) . "$"; ?></td>
                                <td><?php echo number_format($subtotal, 2) . "$"; ?></td>
                                <td><a style="color: #FF9707;" href="delcart.php?product_id=<?= $product_id ?>">Remove</a></td>
                            </tr>
                        <?php
                            $i++;
                        }
                        ?>
                        <tr>
                            <th>Total Price</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th id="total-price">0.00$</th>
                            <th></th>
                            <th><input type="submit" value="Checkout" class="btn-primarys"></th>
                        </tr>
                    </table>
                </form>
            <?php
            } else {
                echo "<div class='alert alert-danger'>
          <strong>Bạn chưa có sản phẩm nào trong giỏ</strong>
</div>";
            }
            ?>
        </div>
    </div>
</div>
<?php
$conn->close();
include 'template/footer.php';
?>

<script>
    // Script tính tổng tiền
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.item-check'); // Tất cả checkbox
        const totalPriceElement = document.getElementById('total-price'); // Phần hiển thị tổng tiền

        // Hàm tính tổng tiền khi check hoặc bỏ check
        const calculateTotalPrice = () => {
            let total = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.getAttribute('data-subtotal'));
                }
            });
            totalPriceElement.textContent = total.toFixed(2) + "$"; // Cập nhật tổng tiền
        };

        // Gắn sự kiện 'change' cho mỗi checkbox
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', calculateTotalPrice);
        });
    });
</script>