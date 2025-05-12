<?php
session_start();
require 'conn/connect.php'; 
include 'template/header.php'; 
include 'template/nav.php';

if (isset($_SESSION['user_id'])) {
    $id = intval($_SESSION['user_id']);
    $sql = "SELECT * FROM users WHERE id = $id"; 
    $res = mysqli_query($conn, $sql);
    $user = $res->fetch_assoc();
    $username = $user['username']; 
    $email = $user['email'];
    $user_password = $user['password'];
} else {
    header ('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $email = $_POST['email'];
    $old_password = $_POST['old_password']; 
    $new_password = $_POST['new_password'];

    // Kiểm tra nếu email đã tồn tại
    $sql_email_check = "SELECT * FROM users WHERE email = '$email' AND id != $id"; 
    $email_check_result = mysqli_query($conn, $sql_email_check);

    if (mysqli_num_rows($email_check_result) > 0) {
        // Nếu email đã tồn tại, trả về lỗi
        $mess = "Email đã tồn tại";
    } else {
        // Kiểm tra mật khẩu cũ
        if (password_verify($old_password, $user_password)) { 
            // Kiểm tra nếu không nhập mật khẩu mới
            if (empty($new_password)) {
                $mess = "Bạn chưa nhập mật khẩu mới để cập nhật.";
                $mess_type = "danger";
            } else {
                // Mã hóa mật khẩu mới
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Mã hóa mật khẩu mới
                // Cập nhật email và mật khẩu
                $sql = "UPDATE users SET email = '$email', password = '$new_hashed_password' WHERE id = $id"; 
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $mess = "Update thành công";
                    $mess_type = "success";
                } else {
                    $mess = "Update không thành công";
                }
            }
        } else {
            $mess = "Sai mật khẩu";
        }
    }
}

?>

<div class="container">
<div class="row">
<div class="col-md-2"></div>
<div class="col-md-8">
<h2 style="background-color: #00FF00; color: white; padding: 5px; text-align: center;">Cập nhật thông tin người dùng</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" value="<?= $username ?>" disabled> 
                </div>
                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" placeholder="Enter email" name="email" value="<?= $email ?>" required> 
                </div>
                <div class="form-group">
                    <label for="oldpwd">Old Password:</label>
                    <input type="password" class="form-control" placeholder="Enter old password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="newpwd">New Password:</label>
                    <input type="password" class="form-control" placeholder="Enter new password" name="new_password"> 
                </div>
                <div class="text-center">
                    <button style="background-color: yellow; border: none; padding: 10px 20px;" type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
            <?php if (isset($mess) && $mess != ""): ?>
                <div class="alert alert-danger mt-3">
                    <strong>Info!</strong> <?php echo $mess; ?>.
                </div>
            <?php endif; ?>
        </div> 
    </div>
</div>

<?php
$conn->close();
include 'template/footer.php';
?>
