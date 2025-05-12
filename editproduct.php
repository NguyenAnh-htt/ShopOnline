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

// Lấy thông tin sản phẩm từ cơ sở dữ liệu
$product_id = $_GET['id'];  // ID của sản phẩm cần sửa
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// Kiểm tra nếu không tìm thấy sản phẩm
if (!$product) {
    echo "<p>Sản phẩm không tồn tại!</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $title = $_POST['title'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_FILES['image']['name'];
    $temp_image = $_FILES['image']['tmp_name'];
    $description = $_POST['description'];

    // Kiểm tra nếu giá sản phẩm là số âm
    if ($price < 0) {
        $error_message = "Giá sản phẩm không thể là số âm!";
    } else {
        // Nếu có ảnh mới, di chuyển ảnh lên thư mục images
        if (!empty($image)) {
            move_uploaded_file($temp_image, "images/" . $image);
            $image_sql = ", image = '$image'"; // Cập nhật ảnh mới
        } else {
            $image_sql = ""; // Không thay đổi ảnh nếu không chọn ảnh mới
        }

        // Thực hiện câu lệnh SQL để sửa thông tin sản phẩm
        $sql_update = "UPDATE products SET title = '$title', price = '$price', catid = '$category', description = '$description' $image_sql WHERE id = $product_id";
        if (mysqli_query($conn, $sql_update)) {
            echo "<script>alert('Sản phẩm đã được cập nhật thành công!'); window.location.href = 'ad-index.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra. Vui lòng thử lại.');</script>";
        }
    }
}
?>

<style>
    /* Container */
    .container {
        margin-top: 30px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Tiêu đề */
    h1 {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Form group */
    .form-group label {
        font-weight: bold;
        color: #555;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px;
        font-size: 14px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 5px rgba(255, 193, 7, 0.3);
    }

    /* Ảnh sản phẩm */
    .img-fluid {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px;
    }

    /* Nút */
    .btn {
        font-size: 16px;
        font-weight: bold;
        border-radius: 20px;
        padding: 10px 20px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-warning {
        background-color: #ffc107;
        border: none;
        color: #fff;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        transform: scale(1.05);
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #c82333;
        transform: scale(1.05);
    }

    /* Lỗi */
    .text-danger {
        font-size: 14px;
        margin-top: 5px;
    }

    /* Mô tả */
    textarea.form-control {
        resize: none;
        min-height: 120px;
    }
</style>

<div class="container">
    <h1 class="text-center">Cập nhật thông tin sản phẩm</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Tên sản phẩm</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Giá sản phẩm (USD)</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required oninput="checkPrice()">
            <small id="priceError" class="text-danger" style="display:none;">Giá sản phẩm không thể là số âm!</small>
        </div>

        <div class="form-group">
            <label for="category">Danh mục sản phẩm</label>
            <select class="form-control" id="category" name="category" required>
                <?php
                $sql_cat = "SELECT * FROM category";
                $res_cat = mysqli_query($conn, $sql_cat);
                while ($r_cat = mysqli_fetch_assoc($res_cat)) {
                    echo '<option value="' . $r_cat['id'] . '" ' . ($r_cat['id'] == $product['catid'] ? 'selected' : '') . '>' . $r_cat['name'] . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Ảnh sản phẩm</label>
            <input type="file" class="form-control" id="image" name="image">
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="img-fluid mt-2" style="width: 100px;">
        </div>

        <div class="form-group">
            <label for="description">Mô tả sản phẩm</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group text-center">
            <button type="submit" class="btn btn-warning" id="submitBtn">Cập nhật sản phẩm</button>
            <a href="ad-index.php" class="btn btn-danger">Hủy</a>
        </div>
    </form>
</div>

<script>
    // Kiểm tra giá nhập vào có phải là số âm hay không
    function checkPrice() {
        var price = document.getElementById('price').value;
        var errorMsg = document.getElementById('priceError');
        var submitBtn = document.getElementById('submitBtn');

        if (price < 0) {
            errorMsg.style.display = "block"; // Hiển thị lỗi
            submitBtn.disabled = true; // Vô hiệu hóa nút submit
        } else {
            errorMsg.style.display = "none"; // Ẩn lỗi
            submitBtn.disabled = false; // Bật lại nút submit
        }
    }
</script>


<?php
include('template\footer.php');
?>