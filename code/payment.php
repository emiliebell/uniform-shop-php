<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'];

$sql = "SELECT o.*, u.name AS uniform_name, u.price, s.address, s.detailed_address, s.recipient_name, s.contact_number
        FROM orders o 
        JOIN uniforms u ON o.uniform_id = u.uniform_id 
        JOIN shipping s ON o.order_id = s.order_id 
        WHERE o.order_id = $order_id";
$result = $conn->query($sql);
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>주문 확인 및 결제</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>주문 확인 및 결제</h1>
		<nav>
            <a href="index.php">홈</a>
            <a href="order_status.php">주문 상태 확인</a>
            <a href="profile.php">프로필</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
		        <a href="admin.php">관리자 페이지</a>
		        <a href="stock_management.php">재고 관리</a>
		    <?php } ?>
            <a href="logout.php">로그아웃</a>
		</nav>

    </header>
    <div class="container">
        <p>상품명: <?php echo $order['uniform_name']; ?></p>
        <p>수량: <?php echo $order['quantity']; ?></p>
        <p>가격: <?php echo number_format($order['price'] * $order['quantity']); ?>원</p>
        <p>사이즈: <?php echo $order['size']; ?></p>
        <p>수령인 이름: <?php echo $order['recipient_name']; ?></p>
        <p>연락처: <?php echo $order['contact_number']; ?></p>
        <p>주소: <?php echo $order['address']; ?></p>
        <p>상세 주소: <?php echo $order['detailed_address']; ?></p>
        <p>주문 날짜: <?php echo $order['order_date']; ?></p>
        <form method="post" action="confirm_order.php">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <p><input type="submit" value="결제하기"></p>
        </form>
    </div>
</body>
</html>
