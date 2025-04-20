<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        $uniform_id = $_POST['uniform_id'];
        $stock = $_POST['stock'];

        $sql = "UPDATE uniforms SET stock = $stock WHERE uniform_id = $uniform_id";
        if (!$conn->query($sql)) {
            throw new Exception("재고 업데이트 실패: " . $conn->error);
        }

        $conn->commit();
        echo "재고가 성공적으로 업데이트되었습니다.";
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
}

// 모든 상품 조회
$sql = "SELECT * FROM uniforms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>재고 관리 - 망곰베어스 팝업스토어</title>
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
        <h1>재고 관리</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>상품명</th>
                <th>재고</th>
                <th>수정</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['uniform_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td>
                        <form method="post" action="stock_management.php">
                            <input type="hidden" name="uniform_id" value="<?php echo $row['uniform_id']; ?>">
                            <input type="number" name="stock" value="<?php echo $row['stock']; ?>" required>
                            <input type="submit" value="수정">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
