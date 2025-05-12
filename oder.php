<?php
session_start();
require 'conn/connect.php';
include 'template/header.php';
include 'template/nav.php';

//khach hang chua dang nhap
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
	exit;
}
//$user_id = $_SESSION['user_id'];
?>
<div class="content">
<div class="container" style="padding-left: 50px; padding-right: 50px">
<form action="process_checkout.php" method="post">z
	<!-- Thông tin giao hang -->
	<div class="row">
		<div class="col-md-offset-3 col-md-6">
			<h3>Billing Details</h3><br>
			<label>Country</label>
			<select name="country" class="form-control">
				<option value="">Select Country</option>
				<option value="VN">Việt Nam</option>
				<option value="CH">China</option>
			</select><br>
	
			<div class="row">
				<div class="col-md-6">
					<label>First Name</label>
					<input class="form-control" type="text" name="fname" value="" required>
					</div>
					<div class="col-md-6">
						<label>Last Name</label>
						<input class="form-control" type="text" name="lname" value="" required>
					</div>
			</div><br>
			
			<label>Company</label>
			<input type="text" class="form-control" name="company"><br>
			
			<label>Address</label>
			<input type="text" class="form-control" name="address1" required><br>

			<label>Delivery address</label>
			<input type="text" class="form-control" name="address2"><br>

			<div class="row">
				<div class="col-md-4">
					<label>City</label>
					<input class="form-control" type="text" name="city" required>
				</div>
				<div class="col-md-4">
					<label>State</label>
					<input class="form-control" type="text" name="state" value="" required>
				</div>
				<div class="col-md-4">
					<label>Postcode</label>
					<input class="form-control" type="text" name="zipcode" placeholder="Postcode/Zipcode" value="">
				</div>
			</div><br>

			<label>Phone</label>
		    <input type="text" class="form-control" name="phone" required>
		</div>
	</div></br>
	<?php
	// Kiểm tra form có submit và có check sản phẩm cần đặt
	$total_price = 0;
	$countproduct=0;
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_items'])) {
		$selected_items = $_POST['selected_items']; //lấy sp có check
		?>
		<!-- Lưu mảng chứa sản phẩm check -->
		<input type="hidden" name="array" value='<?php echo json_encode($selected_items); ?>'>
		<div class="row">
			<div class="col-md-offset-1 col-md-10">
				<h3>Products order</h3>
				<table class="table">
					<?php
					// lặp qua sản phẩm có check và hiện thị dạng bảng
					foreach ($selected_items as $item) {
						list($item_id, $item_name, $quantity, $price) = explode('|', $item);
						$countproduct +=1;
						$subtotal = $quantity * $price;
						echo "<tr>
										<td>$countproduct</td>
										<td>$item_name</td>
										<td>$quantity</td>
										<td>$price</td>
										<td>$subtotal</td>
									</tr>";
						$total_price += $subtotal;// Cộng thành tiền vào tổng tiền
}
?>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-md-offset-1 col-md-10">
			<h3>Your order</h3><br>
			<table class="table">
				<tr> <!--tổng giỏ hàng-->
					<th width="30%">Cart Subtotal</th>
					<td width="30%"><?php echo $countproduct; ?></td>
				</tr>
				<tr>
					<th>Shipping and Handling</th>
					<td>Free</td>
				</tr>
				<tr>
					<th>Order Total</th>
					<td><?php echo $total_price; ?></td>
				</tr>
			</table>	
			</div>
		</div>

		<!--Phương thức thanh toán-->
		<div class="row">
			<div class="col-md-offset-1 col-md-5">
				<h3>Payment method</h3><br>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-5">
				<input type="radio" name="payment" value="cod" checked="checked">
				<!--Thanh toán khi giao hàng -->
				<label>Cash On Delivery</label>
				<p>Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won't be shipped until the funds have cleared in our account.</p>			
			</div>
			<div class="col-md-5">
				<input type="radio" name="payment" value="pal">
				<!--Thanh toán bằng tài khoản Paypal -->
				<label>Paypal</label>
				<p>Pay via PayPal; you can pay with your credit card if you don't have a PayPal account</p>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-offset-1 col-md-10">
				<input type="checkbox" name="agree" value="true" class="css-checkbox" required>
				I've read and accept the terms conditions
				<br><br>
				<input type="submit" value="Pay Now" class="btn btn-primary">
				<br><br>
    		</div>
		</div>
</div>

	<?php
	} else {
		// Nếu không có sản phẩm nào được chọn
		echo "<h1 style='text-align:center'>No items selected for checkout!</h1>";
		echo "<p style='text-align: center;'><a href='cart.php' class='btn btn-danger' >Back to View cart</a></p>";
	}
	?>
</form>
</div>
</div>
<?php
include('template/footer.php');
?>