<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// POST 요청인지 확인
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $uniform_id = $_POST['uniform_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size']; // 선택한 사이즈
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $total_price = $_POST['total_price']; // 총 가격

    // 입력된 유니폼 ID 확인
    echo "입력된 유니폼 ID: " . $uniform_id . "<br>";

    // 유니폼 ID가 uniforms 테이블에 존재하는지 확인
    $uniform_check_sql = "SELECT * FROM uniforms WHERE uniform_id = '$uniform_id'";
    $uniform_result = $conn->query($uniform_check_sql);

    if ($uniform_result->num_rows > 0) {
        // 주문 정보 삽입
        $order_sql = "INSERT INTO orders (user_id, uniform_id, quantity, order_date, total_price, size) VALUES ('$user_id', '$uniform_id', '$quantity', NOW(), '$total_price', '$size')";
        
        if ($conn->query($order_sql) === TRUE) {
            $order_id = $conn->insert_id;
            
            // 배송 정보 삽입
            $shipping_sql = "INSERT INTO shipping (order_id, address, phone, status) VALUES ('$order_id', '$address', '$phone', 'Processing')";
            if ($conn->query($shipping_sql) === TRUE) {
                echo "주문이 성공적으로 완료되었습니다.";
            } else {
                echo "배송 정보 추가 중 오류가 발생했습니다: " . $conn->error;
            }
            
            // 재고 감소 처리 (옵션)
            $update_stock_sql = "UPDATE uniforms SET stock = stock - $quantity WHERE uniform_id = '$uniform_id'";
            if ($conn->query($update_stock_sql) !== TRUE) {
                echo "재고 업데이트 중 오류가 발생했습니다: " . $conn->error;
            }
        } else {
            echo "주문 정보 추가 중 오류가 발생했습니다: " . $conn->error;
        }
    } else {
        echo "유니폼 ID가 유효하지 않습니다.";
    }
}
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
        <p><a href="index.php">메인 페이지로 돌아가기</a></p>
    </div>
</body>
</html>
