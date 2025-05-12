<?php
session_start();
include('conn\connect.php');
include('template\header.php');
include('template\nav.php');

// Kiểm tra nếu người dùng không phải admin
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header("Location: index.php"); // Chuyển hướng về trang chủ
    exit();
}
?>
<style>
/* Định dạng hình ảnh */
.image-container {
    width: 100%;
    height: 180px;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 10px;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease-in-out;
}

.image-container:hover img {
    transform: scale(1.1);
}

/* Định dạng phần caption */
.caption {
    text-align: center;
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

.caption:hover {
    background-color: #f0f0f0;
}

.caption h4 {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.caption p {
    margin: 5px 0;
    font-size: 16px;
    color: #555;
}

/* Định dạng cho các nút */
.caption .btn-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.caption .btn-group a {
    font-size: 14px;
    font-weight: bold;
    border-radius: 20px;
    padding: 5px 15px;
}

.btn-success {
    background-color: #28a745;
    border: none;
}

.btn-warning {
    background-color: #ffc107;
    border: none;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
}

.btn:hover {
    opacity: 0.85;
}

/* Định dạng chung */
.container {
    margin-top: 30px;
}

h1 {
    font-size: 32px;
    color: #333;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.row {
    gap: 20px;
}
</style>

<div class="container">
    <h1 class="text-center">Quản lý sản phẩm</h1>
    <div class="text-left mb-3">
        <a href="addproduct.php" class="btn btn-success">Thêm sản phẩm mới</a>
    </div>
    <br>
    <div class="row">
        <?php
        // Truy vấn danh sách sản phẩm
        $sql = "SELECT * FROM products";
        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            while ($r = mysqli_fetch_array($res)) {
                ?>
                <div class="col-sm-6 col-md-3">
                    <div class="image-container">
                        <img src="images/<?php echo htmlspecialchars($r['image']) ?>" class="rounded" alt="<?php echo htmlspecialchars($r['title']) ?>">
                    </div>
                    <div class="caption">
                        <h4><?php echo htmlspecialchars($r['title']) ?></h4>
                        <p>Giá: <?php echo htmlspecialchars($r['price']) ?> USD</p>
                        <div class="btn-group">
                            <a href="editproduct.php?id=<?php echo $r['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="deleteproduct.php?id=<?php echo $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">Xóa</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center'>Không tìm thấy sản phẩm nào.</p>";
        }
        ?>
    </div>
</div>



<?php
include('template\footer.php');
?>
