<?php
session_start();
include('D:\Examp\htdocs\ShopOnline\conn\connect.php'); 
include 'D:\Examp\htdocs\ShopOnline\template\header.php'; 
include 'D:\Examp\htdocs\ShopOnline\template\nav.php';

$mess = ""; // Biến chứa thông báo lỗi hoặc thành công

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && $_POST['username'] != "") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password, type FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password, $user_type);

		
        if ($stmt->num_rows > 0) { // Nếu tìm thấy username
            $stmt->fetch();

            // Kiểm tra mật khẩu
            if (password_verify($password, $hashed_password)) {
                // Lưu thông tin vào session
                $_SESSION['user_id'] = $user_id; 
                $_SESSION['username'] = $username;
                $_SESSION['type'] = $user_type; // Lưu loại tài khoản
                
                // Chuyển hướng dựa trên loại tài khoản
                if ($user_type === 'admin') {
                    header('Location: ad-index.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $mess = "Sai mật khẩu";
            }
        } else {
            $mess = "Tên người dùng không tồn tại.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="content-blog">
            <div class="col-md-offset-3 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form method="post">
                            <div class="text-center">
                                <h3>Login Form</h3>
                                <img src="https://i.pinimg.com/originals/8f/16/56/8f165632f8234feea1a7590c2435b113.gif" alt="Footer Image" style="width:150px;height:150px;">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter User name" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Enter Password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <button type="button" class="btn btn-info">Cancel</button>
                            <a href="register.php">Sign Up</a> (If you do not already have an account)
                        </form>
                        <br>
                        <!-- Hiển thị thông báo lỗi nếu có -->
                        <?php if (isset($mess) && $mess != "") { ?>
                            <div class="alert alert-danger">
                                <strong>Info!</strong> <?php echo $mess; ?>.
                            </div>
                        <?php } ?>
                    </div>
                </div>
</div>
        </div>
    </div>
</div>

<?php
include 'template/footer.php';
?>
