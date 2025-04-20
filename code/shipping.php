<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        $recipient_name = $_POST['recipient_name'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];
        $detailed_address = $_POST['detailed_address'];
        $shipping_date = date('Y-m-d H:i:s');
        $status = "배송 준비 중";

        $sql = "INSERT INTO shipping (order_id, recipient_name, contact_number, address, detailed_address, shipping_date, status) VALUES ('$order_id', '$recipient_name', '$contact_number', '$address', '$detailed_address', '$shipping_date', '$status')";
        
        if (!$conn->query($sql)) {
            throw new Exception("배송 정보 입력에 실패했습니다: " . $conn->error);
        }

        $conn->commit();
        header("Location: order_complete.php?order_id=$order_id");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>배송지 입력</title>
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
        <h2 class="sub-header">배송지 입력</h2>
        <div class="shipping-form">
            <form method="post" action="shipping.php?order_id=<?php echo $order_id; ?>">
                <div class="form-group">
                    <label for="recipient_name">수령인 이름:</label>
                    <input type="text" name="recipient_name" id="recipient_name" placeholder="수령인 이름" required>
                </div>
                <div class="form-group">
                    <label for="contact_number">연락처:</label>
                    <input type="text" name="contact_number" id="contact_number" placeholder="연락처" required>
                </div>
                <div class="form-group">
                    <label for="address">주소:</label>
                    <input type="text" name="address" id="address" placeholder="주소" required>
                </div>
                <div class="form-group">
                    <label for="detailed_address">상세 주소:</label>
                    <input type="text" name="detailed_address" id="detailed_address" placeholder="상세 주소" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="배송지 입력">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
