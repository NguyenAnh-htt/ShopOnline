<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>Thanh toán qua PayPal</title>
</head>
<body>

    <!-- Form thanh toán qua PayPal -->
    <fieldset>
        <legend>Thanh toán qua cổng PayPal</legend>
        <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
            <!-- Nhập địa chỉ email người nhận tiền (người bán) -->
            <input type="hidden" name="business" value="sb-yzzno34262679@business.example.com">

            <!-- Tham số cmd có giá trị _xclick chỉ rõ cho PayPal biết là người dùng nhấn nút thanh toán -->
            <input type="hidden" name="cmd" value="_xclick">

            <!-- Thông tin mua hàng -->
            <input type="hidden" name="item_name" value="HoaDonMuaHang">

            <!-- Trị giá của giỏ hàng (USD) -->
            Nhập số tiền hóa đơn: 
            <input type="number" name="amount" placeholder="Nhập số tiền vào" value="">

            <!-- Loại tiền -->
            <input type="hidden" name="currency_code" value="USD">

            <!-- Đường link cung cấp cho PayPal biết để sau khi xử lý thành công, sẽ chuyển về link này -->
            <input type="hidden" name="return" value="http://localhost/ShopOnline/thanhtoanpaypal/thanhcong.html">

            <!-- Đường link cung cấp cho PayPal biết nếu xử lý KHÔNG thành công, sẽ chuyển về link này -->
            <input type="hidden" name="cancel_return" value="http://localhost/ShopOnline/thanhtoanpaypal/loi.html">

            <input type="submit" name="submit" value="Thanh toán qua PayPal">
        </form>
    </fieldset>

    <!-- Thông báo lỗi và tự động chuyển hướng về trang chủ -->
    <script type="text/javascript">
        // Đợi 5 giây (5000ms) và chuyển hướng về trang index.php
        setTimeout(function() {
            window.location.href = 'index.php'; // Chuyển hướng về trang chủ
        }, 5000);
    </script>

    <h1>Đã xảy ra lỗi, bạn sẽ được chuyển về trang chủ trong 5 giây...</h1>
</body>

</html>
