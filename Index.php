<?php
session_start();
include('conn/connect.php');
include('template/header.php');
include('template/nav.php');
?>
<style>
/* Thiết kế container */
.container {
    margin-top: 30px;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    position: relative; /* Để định vị nút "Xem giỏ hàng" */
}

/* Hàng sản phẩm */
.row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Tạo khoảng cách giữa các sản phẩm */
    justify-content: center;
}

/* Card sản phẩm */
.col-sm-6.col-md-3 {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.col-sm-6.col-md-3:hover {
    transform: translateY(-10px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Hình ảnh sản phẩm */
.image-container {
    width: 100%;
    height: 180px;
    overflow: hidden;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-container img:hover {
    transform: scale(1.1);
}

/* Caption sản phẩm */
.caption {
    padding: 15px;
    text-align: center;
}

.caption h4 {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.caption p {
    font-size: 14px;
    color: #777;
    margin: 5px 0;
}

.caption input[type="number"] {
    width: 60px;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 3px;
    text-align: center;
}

.caption button {
    background-color: #007bff;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.caption button:hover {
    background-color: #0056b3;
}

/* Nút giỏ hàng */
.cart-button {
    position: absolute;
    top: -10px; /* Đẩy nút lên gần mép trên */
    right: -10px; /* Đẩy nút sang phải gần mép container */
    padding: 10px 15px;
    background-color: #ff4d4d;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    transition: background-color 0.3s ease, transform 0.3s ease;
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.cart-button:hover {
    background-color: #b71c1c;
    transform: scale(1.05);
}
</style>

<div class="container">
    <!-- Nút "Xem giỏ hàng" đặt ở góc phải trên -->
    <a href="card.php" class="cart-button">Xem giỏ hàng</a>
    
    <div class="row">
        <?php
        // Thực hiện câu truy vấn
        $sql = "SELECT * FROM products";
        $res = mysqli_query($conn, $sql);

        // Kiểm tra nếu có dữ liệu trả về
        if ($res && mysqli_num_rows($res) > 0) {
            while ($r = mysqli_fetch_array($res)) {
                ?>
                <div class="col-sm-6 col-md-3"> <!-- 4 sản phẩm trên một dòng -->
                    <div class="image-container">
                        <a href="deltailitem.php?id=<?php echo $r['id'] ?>">
                            <img src="images/<?php echo htmlspecialchars($r['image']) ?>" class="rounded" alt="<?php echo htmlspecialchars($r['title']) ?>">
                        </a>
                    </div>
                    <div class="caption">
                        <form method="POST" action="addtocart.php">
                            <h4><?php echo htmlspecialchars($r['title']) ?></h4>
                            <p>Giá: <strong><?php echo htmlspecialchars($r['price']) ?> VND</strong></p>
                            <p>
                                Số lượng:
                                <input type="number" name="quantity" value="1" min="1">
                            </p>
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($r['id']) ?>">
                            <p>
                                <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
                            </p>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>Không tìm thấy sản phẩm nào.</p>";
        }
        ?>
    </div>
</div>

<?php
include('template/footer.php');
?>