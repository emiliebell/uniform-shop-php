<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'];

// 주문 정보를 가져오기 위한 쿼리
$sql = "SELECT o.*, u.name as uniform_name, s.address, s.phone, s.status 
        FROM orders o 
        JOIN uniforms u ON o.uniform_id = u.uniform_id 
        JOIN shipping s ON o.order_id = s.order_id 
        WHERE o.order_id = '$order_id' AND o.user_id = '" . $_SESSION['user_id'] . "'";
$result = $conn->query($sql);
$order = $result->fetch_assoc();
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
            <a href="logout.php">로그아웃</a>
        </nav>
    </header>
    <div class="container">
        <h1>주문 완료</h1>
        <p>주문이 성공적으로 완료되었습니다. 감사합니다!</p>
        <p>주문 번호: <?php echo $order['order_id']; ?></p>
        <p>상품명: <?php echo $order['uniform_name']; ?></p>
        <p>사이즈: <?php echo $order['size']; ?></p>
        <p>수량: <?php echo $order['quantity']; ?></p>
        <p>총 가격: <?php echo number_format($order['total_price']); ?>원</p>
        <p>주소: <?php echo $order['address']; ?></p>
        <p>전화번호: <?php echo $order['phone']; ?></p>
        <p>상태: <?php echo $order['status']; ?></p>
        <p><a href="index.php">메인 페이지로 돌아가기</a></p>
    </div>
</body>
</html>
