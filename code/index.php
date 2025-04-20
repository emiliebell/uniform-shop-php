<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';

$sql = "SELECT u.*, c.category_name 
        FROM uniforms u 
        JOIN categories c ON u.category_id = c.category_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>망곰베어스 팝업스토어</title>
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
        <div class="product-list">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<a href='detail.php?uniform_id=" . $row['uniform_id'] . "'>";
                echo "<img src='" . $row['thumbnail'] . "' alt='" . $row['name'] . "'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p>" . $row['category_name'] . "</p>"; // 카테고리 이름 표시
                echo "<p>" . number_format($row['price']) . "원</p>";
                echo "</a>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
