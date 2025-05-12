<?php
session_start();
require 'conn/connect.php';
$user_id = $_SESSION['user_id'];
$total_price=0;
$countproduct=0;


// // Kiểm tra form có submit và có check sản phẩm cần đặt
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_items'])) {
//     $selected_items = $_POST['selected_items']; // Lấy sản phẩm đã chọn
//     $_SESSION['selected_items'] = $selected_items; // Lưu sản phẩm vào session

//     // Chuyển hướng đến trang my_order.php
//     header('Location: my_order.php');
//     exit;
// } else {
//     // Nếu không có sản phẩm nào được chọn, chuyển hướng về trang giỏ hàng
//     header('Location: cart.php');
//     exit;
// }


if(isset($_POST) & !empty($_POST) & isset($_POST['agree']))
{
	if($_POST['agree'] == true){
		//lấy dữ liệu từ form 
		$country=$_POST['country'];
		$firstname=$_POST['fname'];
		$lastname=$_POST['lname'];
		$company=$_POST['company'];
		$address1=$_POST['address1'];
		$address2=$_POST['address2'];
		$city=$_POST['city'];
		$state=$_POST['state'];
		$zip=$_POST['zipcode'];
		$mobile=$_POST['phone'];
		$payment=$_POST['payment'];

		//Kiem tra khach hang đã có trong usermeta hay chưa
		$sql_sel="select * from usersmeta where user_id=$user_id";
		$res=mysqli_query($conn,$sql_sel);
		$r=mysqli_fetch_assoc($res); //mảng kết hợp
		$count=mysqli_num_rows($res); 
		//Nếu đã lưu khach hang --> cập nhật thông tin
		if($count==1){
			$sql_usersmeta="update usersmeta set country='$country', firstname='$firstname', 
											lastname='$lastname', company='$company', address1='$address1', 
											address2='$address2', city='$city', state='$state', zip='$zip', 
											mobile='$mobile' where user_id=$user_id";
		}
		else{ //Nếu chưa lưu khách hàng thì thêm vào usermeta
			$sql_usersmeta="insert into usersmeta(country, firstname, lastname, company, address1, address2, city, state, zip, mobile, user_id)
			values('$country', '$firstname','$lastname', '$company','$address1','$address2', '$city', '$state','$zip','$mobile', '$user_id')";
		}
		
		$res_usersmeta=mysqli_query($conn,$sql_usersmeta);
		//Nếu cập nhật usermeta thanh công --> insert vào bảng order
		if($res_usersmeta){
			//insert vào order
			$selected_items = json_decode($_POST['array'], true); // Giải mã JSON thành mảng PHP
			foreach ($selected_items as $item) {
				list($item_id, $item_name, $quantity, $price) = explode('|', $item);
				$total_price += $quantity * $price;// Cộng thành tiền vào tổng tiền
			}
			$sql_order="insert into orders(user_id, totalprice, paymentmode, orderstatus) 
						values('$user_id', $total_price, '$payment', 'Order placed')";
			$res_order=mysqli_query($conn,$sql_order);

			//Nếu insert bảng order thành công --> insert vào bảng orderitems
			if($res_order){
				//lấy giá trị khóa chính của field vừa insert
				$order_id= mysqli_insert_id($conn);
				//insert vào bảng orderitems
				foreach ($selected_items as $item) {
					list($product_id, $product_name, $quantity, $price) = explode('|', $item);
					$sql_orderitems="insert into orderitems(order_id, product_id, product_quantity, product_price) 
													values($order_id, $product_id, $quantity, $price)";
					$res_orderitems=mysqli_query($conn,$sql_orderitems);

					$sql_cart="delete from cart where user_id = $user_id and product_id = '$product_id'";
					mysqli_query($conn,$sql_cart);
				}
			}
			// Nếu người dùng chọn thanh toán qua paypal
			if ($payment=="pal"){    
			?>
				<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" id="autoSubmitForm">
					<!-- Nhập địa chỉ email người nhận tiền (người bán) business--> 
					<input type="hidden" name="business" value="sb-yzzno34262679@business.example.com">

					<!-- tham số cmd có giá trị _xclick chỉ rõ cho paypal biết là người dùng nhất nút thanh toán -->
					<input type="hidden" name="cmd" value="_xclick">

					<!-- Thông tin mua hàng. -->
					<input type="hidden" name="item_name" value="HoaDonMuaHang">

					<!--Trị giá của giỏ hàng, vì paypal không hỗ trợ tiền Việt nên phải đổi ra tiền $-->
					<input type="hidden" name="amount" placeholder="Nhập số tiền vào" value="<?php echo $total_price ?>">
					
					<!--Loại tiền-->
					<input type="hidden" name="currency_code" value="USD">

					<!--Đường link cung cấp cho Paypal biết để sau khi xử lí thành công nó sẽ chuyển về link này-->
					<input type="hidden" name="return" value="http://localhost/ShopOnline/thanhtoanpaypal/thanhcong.html">

					<!--Đường link cung cấp cho Paypal biết để nếu  xử lí KHÔNG thành công nó sẽ chuyển về link này-->
					<input type="hidden" name="cancel_return" value="http://localhost/ShopOnline/thanhtoanpaypal/loi.html">
				</form>

				<!-- Form điều hướng paypal tự submit -->
				<script>
				document.getElementById("autoSubmitForm").submit();
				</script>
			<?php
			}
		}else{
            echo "Lỗi cập nhật thông tin khách hàng";
    }
	}else{
      header('location:cart.php');
  }
}else{
  header('location:cart.php');
}
