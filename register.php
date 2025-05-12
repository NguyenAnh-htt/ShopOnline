<script>
function validatePassword() {
    var password = document.getElementById("password").value;
    var repassword = document.getElementById("repassword").value;   

    var message = document.getElementById("error-message");   


    if (password === repassword) {
        message.textContent = ""; // Clear the error message
        return true; // Allow form submission
    } else {
        message.textContent = "Mật khẩu không khớp!"; // Show error message
        return false; // Prevent form submission
    }
}
</script>
<?php
session_start();
include('D:\Examp\htdocs\ShopOnline\conn\connect.php');
include('D:\Examp\htdocs\ShopOnline\template\header.php');
include('D:\Examp\htdocs\ShopOnline\template\nav.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $e = $_POST['email'];

    // Sử dụng prepared statements để tránh SQL Injection
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u, $e);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $mess = "Username or email already exists";
    } else {
        $sql = "INSERT INTO users (username, password, email, type) VALUES (?, ?, ?, 'admin')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $u, $p, $e);
        $res = $stmt->execute();

        if ($res) {
            $mess = "Register successful";
        } else {
            $mess = "Register failed";
        }
    }
}
?>

<div class="container">
    <div style="width: 50%; margin: 0 auto;">
        <h1>Register member</h1>
        <form action="" method="POST" onsubmit="return validatePassword()">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" placeholder="Enter Username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" placeholder="Enter email" name="email" required>
            </div>

            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control" placeholder="Enter password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="pwd">Repassword:</label>
                <input type="password" class="form-control" placeholder="Enter Repassword" name="repassword" id="repassword" required>
                <span id="error-message" class="error" style="color: red;"></span><br><br>
                <?php
                if (isset($mess) && $mess != "") {
                    ?>
                    <div class="alert <?php echo $res ? 'alert-success' : 'alert-danger'; ?>">
                        <strong>Info!</strong> <?php echo $mess; ?>.
                    </div>
                    <?php
                }
                ?>
            </div>
            <div style="margin: 0 auto;">
                <button type="submit" class="btn btn-primary">Register</button> <a href="./login.php" class="btn btn-info">Login</a>
            </div>
        </form>
    </div>
</div>
<?php
include('D:\Examp\htdocs\ShopOnline\template\footer.php');
?>
