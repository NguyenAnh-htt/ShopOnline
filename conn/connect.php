<?php
// Tạo kết nối
$conn = mysqli_connect("localhost", "root", "");

if (!$conn) { // Không kết nối được
    die("Kết nối CSDL không thành công: " . mysqli_connect_error());
}

// Chọn database
$select_db = mysqli_select_db($conn, 'e-commm');
if (!$select_db) {
    die("Không thể chọn database: " . mysqli_error($conn));   
}

// Thiết lập bộ ký tự
mysqli_query($conn, "SET CHARACTER SET 'utf8'"); 

//echo 'Kết nối thành công';
?>