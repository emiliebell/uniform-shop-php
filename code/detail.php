<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uniform_id = $_GET['uniform_id'];
$sql = "SELECT u.*, c.category_name 
        FROM uniforms u 
        JOIN categories c ON u.category_id = c.category_id 
        WHERE uniform_id = $uniform_id";
$result = $conn->query($sql);
$uniform = $result->fetch_assoc();

$user_id = $_SESSION['user_id'];
$sql = "SELECT COALESCE(SUM(quantity), 0) as total_quantity FROM orders WHERE user_id = '$user_id' AND uniform_id = '$uniform_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_quantity = $row['total_quantity'];

$remaining_quantity = 3 - $total_quantity;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = $_POST['quantity'];
    $size = isset($_POST['size']) ? $_POST['size'] : null;

    if ($quantity > $remaining_quantity) {
        $error_message = "최대 구매 수량은 품목당 3개입니다. 현재 구매 가능한 수량은 " . max(0, $remaining_quantity) . "개입니다.";
    } else {
        $conn->begin_transaction();
        try {
            $order_date = date('Y-m-d H:i:s');
            $total_price = $uniform['price'] * $quantity;

            // 주문 정보 삽입
            $order_sql = "INSERT INTO orders (user_id, uniform_id, size, quantity, order_date, total_price) VALUES ('$user_id', '$uniform_id', '$size', '$quantity', '$order_date', '$total_price')";
            if (!$conn->query($order_sql)) {
                throw new Exception("주문 삽입 실패: " . $conn->error);
            }
            $order_id = $conn->insert_id;

            // 재고 감소 처리
            $update_stock_sql = "UPDATE uniforms SET stock = stock - $quantity WHERE uniform_id = $uniform_id";
            if (!$conn->query($update_stock_sql)) {
                throw new Exception("재고 업데이트 실패: " . $conn->error);
            }

            // 모든 작업이 성공하면 커밋
            $conn->commit();

            // 배송 페이지로 리디렉션
            header("Location: shipping.php?order_id=$order_id");
            exit();
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $conn->rollback();
            $error_message = "주문 처리 중 오류 발생: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>망곰베어스 팝업스토어</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-message {
            color: red;
            font-weight: bold;
        }
        .product-detail form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 10px;
        }
        .form-group select, .form-group input {
            flex-grow: 1;
            margin-left: 10px;
            max-width: 150px; /* 입력란의 최대 너비 설정 */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group label {
            width: 70px; /* 라벨의 고정 너비 설정 */
        }
        .form-group input[type="submit"] {
            width: auto;
            padding: 10px 20px;
            margin-top: 20px;
            align-self: center; /* 버튼을 가운데 정렬 */
        }
    </style>
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
        <h1><?php echo $uniform['name']; ?> 상세 정보</h1>
        <?php if (isset($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <?php if ($remaining_quantity <= 0) { ?>
            <p class="error-message">최대 구매 수량은 품목당 3개입니다. 현재 구매 가능한 수량은 0개입니다.</p>
        <?php } ?>
        <div class="product-detail">
            <img src="<?php echo $uniform['thumbnail']; ?>" alt="<?php echo $uniform['name']; ?>">
            <div>
                <p>가격: <?php echo number_format($uniform['price']); ?>원</p>
                <!--<p>설명: <?php echo nl2br($uniform['description']); ?></p>-->
                <form method="post" action="">
                    <input type="hidden" name="uniform_id" value="<?php echo $uniform['uniform_id']; ?>">
                    <?php if (in_array($uniform['name'], ['유니폼', '모자(네이비)', '모자(스카이블루)'])) { ?>
                    <div class="form-group">
                        <label for="size">사이즈:</label>
                        <select name="size" id="size" required>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                        </select>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="quantity">수량:</label>
                        <input type="number" name="quantity" id="quantity" min="1" max="<?php echo min($uniform['stock'], $remaining_quantity); ?>" required <?php echo ($remaining_quantity <= 0) ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="주문하기" <?php echo ($remaining_quantity <= 0) ? 'disabled' : ''; ?>>
                    </div>
                </form>
            </div>
        </div>
        <p><a href="index.php">상품 목록으로 돌아가기</a></p>
    </div>
</body>
</html>
