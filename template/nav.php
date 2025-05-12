<div class="container">
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">Tin Tức</a>
      </div>
      <ul class="nav navbar-nav">
        <li class="active">
          <a href="<?php echo (isset($_SESSION['type']) && $_SESSION['type'] === 'admin') ? 'ad-index.php' : 'index.php'; ?>">Trang Chủ</a>
        </li>

        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">My Account
            <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="my_order.php">Cart</a></li>
            <li><a href="#">Yêu Thích</a></li>
            <li><a href="my_order.php">Lịch Sử </a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Danh Mục
            <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <?php
            $sql_c = "SELECT * FROM category";
            $result_c = mysqli_query($conn, $sql_c);

            while ($r_c = mysqli_fetch_assoc($result_c)) {
            ?>
              <li><a href="index.php?id=<?php echo $r_c['id']; ?>"><?php echo $r_c['name']; ?></a></li>
            <?php
            }

            ?>
          </ul>
        </li>
        <li><a href="edit_profile.php">Edit Profile</a></li>
      </ul>
      <!-- Menu bên phải -->
      <ul class="nav navbar-nav navbar-right">
        <?php
        if (!isset($_SESSION['user_id'])) { // Nếu chưa đăng nhập, hiển thị Đăng Kí và Đăng nhập
        ?>
          <li><a href="register.php">Đăng Kí</a></li>
          <li><a href="login.php">Đăng nhập</a></li>
        <?php
        } else { // Nếu đã đăng nhập, hiển thị Đăng xuất
        ?>
          <li><a href="logout.php">Đăng xuất</a></li>
        <?php
        }
        ?>
      </ul>

      <?php
      if (isset($_SESSION['user_id'])) {
      ?>
        <div class="navbar-header" style="float: right;">
          <a class="navbar-brand" href="profile.php">
            <?php
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM users WHERE id = $user_id";
            $result = mysqli_query($conn, $sql);
            $r = mysqli_fetch_assoc($result);
            echo "User: " . $r['username'];
            ?>
          </a>
        </div>
      <?php
      } else {
      ?>
      <?php
      }
      ?>

    </div>
  </nav>
</div>