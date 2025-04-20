<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET email = '$email', password = '$hashed_password' WHERE user_id = '$user_id'";
        } else {
            $sql = "UPDATE users SET email = '$email' WHERE user_id = '$user_id'";
        }

        if (!$conn->query($sql)) {
            throw new Exception("프로필 업데이트에 실패했습니다: " . $conn->error);
        }

        $conn->commit();
        echo "프로필이 성공적으로 업데이트되었습니다.";
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
}

$sql = "SELECT username, email FROM users WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
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
        <div class="profile-form">
            <h1>프로필 정보</h1>
            <form method="post" action="profile.php">
                <div class="form-group">
                    <label for="username">사용자 이름:</label>
                    <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">이메일:</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">비밀번호:</label>
                    <input type="password" id="password" name="password" placeholder="비밀번호 변경 시 입력">
                </div>
                <div class="form-group">
                    <input type="submit" value="프로필 업데이트">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
