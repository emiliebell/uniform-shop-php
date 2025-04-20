<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'];

// 주문 정보 가져오기
$order_sql = "SELECT * FROM orders WHERE order_id = $order_id";
$order_result = $conn->query($order_sql);
$order = $order_result->fetch_assoc();

// 유니폼 정보 가져오기
$uniform_sql = "SELECT * FROM uniforms WHERE uniform_id = " . $order['uniform_id'];
$uniform_result = $conn->query($uniform_sql);
$uniform = $uniform_result->fetch_assoc();

// 배송 정보 가져오기
$shipping_sql = "SELECT * FROM shipping WHERE order_id = $order_id";
$shipping_result = $conn->query($shipping_sql);
$shipping = $shipping_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>주문 완료 - 망곰베어스 팝업스토어</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>망곰베어스 팝업스토어</h1>
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
        <h1>주문 완료</h1>
        <p>주문이 성공적으로 완료되었습니다. 감사합니다!</p>
        <h2>주문 정보</h2>
        <p>주문 번호: <?php echo $order['order_id']; ?></p>
        <p>상품명: <?php echo $uniform['name']; ?></p>
        <p>수량: <?php echo $order['quantity']; ?></p>
        <?php if (in_array($uniform['name'], ['유니폼', '모자(네이비)', '모자(스카이블루)'])) { ?>
        	<p>사이즈: <?php echo $order['size']; ?></p>
        <?php } ?>
        <p>총 가격: <?php echo number_format($order['total_price']); ?>원</p>
        <h2>배송 정보</h2>
        <p>수령인 이름: <?php echo $shipping['recipient_name']; ?></p>
        <p>연락처: <?php echo $shipping['contact_number']; ?></p>
        <p>주소: <?php echo $shipping['address']; ?></p>
        <p>상세 주소: <?php echo $shipping['detailed_address']; ?></p>
        <p><a href="index.php">메인 페이지로 돌아가기</a></p>
    </div>
</body>
</html>
