<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
        
        if (!$conn->query($sql)) {
            throw new Exception("회원가입 중 오류가 발생했습니다: " . $conn->error);
        }

        $conn->commit();
        echo "회원가입이 성공적으로 완료되었습니다.";
        header('Location: login.php');
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
            <a href="login.php">로그인</a>
        </nav>
    </header>
    <div class="container">
        <div class="register-form">
            <h1>회원가입</h1>
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="username">사용자 이름:</label>
                    <input type="text" name="username" id="username" placeholder="사용자 이름" required>
                </div>
                <div class="form-group">
                    <label for="email">이메일:</label>
                    <input type="email" name="email" id="email" placeholder="이메일" required>
                </div>
                <div class="form-group">
                    <label for="password">비밀번호:</label>
                    <input type="password" name="password" id="password" placeholder="비밀번호" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="회원가입">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
