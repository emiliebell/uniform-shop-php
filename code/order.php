<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        $customer_name = $_POST['customer_name'];
        $customer_email = $_POST['customer_email'];
        $uniform_id = $_POST['uniform_id'];
        $size = $_POST['size'];
        $quantity = $_POST['quantity'];
        $order_date = date('Y-m-d H:i:s');
        $user_id = $_SESSION['user_id'];

        // 사용자가 이미 구매한 수량 확인
        $sql = "SELECT COALESCE(SUM(quantity), 0) as total_quantity FROM orders WHERE user_id = '$user_id' AND uniform_id = '$uniform_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $total_quantity = $row['total_quantity'];

        $remaining_quantity = 3 - $total_quantity;

        if ($quantity > $remaining_quantity) {
            throw new Exception("최대 구매 수량은 품목당 3개입니다. 현재 구매 가능한 수량은 " . max(0, $remaining_quantity) . "개입니다.");
        }

        // 총 가격 계산 (예: 단가 * 수량)
        $sql = "SELECT price, stock FROM uniforms WHERE uniform_id = '$uniform_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $price = $row['price'];
        $stock = $row['stock'];
        $total_price = $price * $quantity;

        if ($quantity > $stock) {
            throw new Exception("재고가 부족하여 구매할 수 없습니다.");
        }

        // 주문 정보 삽입
        $order_sql = "INSERT INTO orders (user_id, uniform_id, size, quantity, order_date, total_price) VALUES ('$user_id', '$uniform_id', '$size', '$quantity', '$order_date', '$total_price')";
        if (!$conn->query($order_sql)) {
            throw new Exception("주문에 실패했습니다: " . $conn->error);
        }
        $order_id = $conn->insert_id; // 방금 삽입된 주문 ID 가져오기

        // 재고 감소 처리
        $update_stock_sql = "UPDATE uniforms SET stock = stock - $quantity WHERE uniform_id = $uniform_id";
        if (!$conn->query($update_stock_sql)) {
            throw new Exception("재고 감소 실패: " . $conn->error);
        }

        // 트랜잭션 커밋
        $conn->commit();

        header("Location: shipping.php?order_id=$order_id"); // 배송지 입력 페이지로 리디렉션
        exit();
    } catch (Exception $e) {
        // 롤백
        $conn->rollback();
        echo $e->getMessage();
    }
} else {
    $uniform_id = $_GET['uniform_id'];
    $sql = "SELECT * FROM uniforms WHERE uniform_id = $uniform_id";
    $result = $conn->query($sql);
    $uniform = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $uniform['name']; ?> 구매하기</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo $uniform['name']; ?> 구매하기</h1>
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
        <form method="post" action="order.php?uniform_id=<?php echo $uniform['uniform_id']; ?>">
            <input type="hidden" name="uniform_id" value="<?php echo $uniform['uniform_id']; ?>">
            <p>이름: <input type="text" name="customer_name" required></p>
            <p>이메일: <input type="email" name="customer_email" required></p>
            <p>사이즈:
                <select name="size" required>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </p>
            <p>수량: <input type="number" name="quantity" min="1" max="<?php echo min($uniform['stock'], 3); ?>" required></p>
            <p><input type="submit" value="주문"></p>
        </form>
    </div>
</body>
</html>
