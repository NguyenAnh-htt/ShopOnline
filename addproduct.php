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

// Xử lý form thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        // Di chuyển ảnh lên thư mục images
        move_uploaded_file($temp_image, "images/" . $image);

        // Thực hiện câu lệnh SQL để thêm sản phẩm vào cơ sở dữ liệu
        $sql = "INSERT INTO products (title, price, catid, image, description) VALUES ('$title', '$price', '$category', '$image', '$description')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Sản phẩm đã được thêm thành công!'); window.location.href = 'ad-index.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra. Vui lòng thử lại.');</script>";
        }
    }
}

// Lấy danh sách các danh mục sản phẩm để hiển thị trong form
$sql_cat = "SELECT * FROM category";
$res_cat = mysqli_query($conn, $sql_cat);
?>
<style>
/* Định dạng container */
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

/* Định dạng form */
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
    border-color: #28a745;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
}

/* Định dạng nút */
.btn {
    font-size: 16px;
    font-weight: bold;
    border-radius: 20px;
    padding: 10px 20px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-success {
    background-color: #28a745;
    border: none;
    color: #fff;
}

.btn-success:hover {
    background-color: #218838;
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

/* Định dạng thông báo lỗi */
.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 20px;
    font-size: 14px;
}

/* Định dạng mô tả */
textarea.form-control {
    resize: none;
    min-height: 120px;
}
</style>

<div class="container">
    <h1 class="text-center">Thêm Sản Phẩm Mới</h1>

    <?php
    // Hiển thị thông báo lỗi nếu có
    if (isset($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Tên sản phẩm</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Nhập tên sản phẩm" required>
        </div>
        <div class="form-group">
            <label for="price">Giá sản phẩm</label>
            <input type="number" class="form-control" id="price" name="price" placeholder="Nhập giá sản phẩm" required>
        </div>
        <div class="form-group">
            <label for="category">Danh mục sản phẩm</label>
            <select class="form-control" id="category" name="category" required>
                <option value="" disabled selected>Chọn danh mục</option>
                <?php while ($r_cat = mysqli_fetch_assoc($res_cat)) { ?>
                    <option value="<?php echo $r_cat['id']; ?>"><?php echo $r_cat['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Ảnh sản phẩm</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <div class="form-group">
            <label for="description">Mô tả sản phẩm</label>
            <textarea class="form-control" id="description" name="description" placeholder="Nhập mô tả sản phẩm" required></textarea>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
            <a href="ad-index.php" class="btn btn-danger">Hủy</a>
        </div>
    </form>
</div>


<?php
include('template\footer.php');
?>
