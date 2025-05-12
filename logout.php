<?php
// Bắt đầu session
session_start();
// Hủy toàn bộ session
session_unset(); // Xóa tất cả các biến session
session_destroy(); // Hủy session
// Chuyển hướng về trang đăng nhập hoặc trang chính
header('Location: login.php');
exit;
?>
