<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // 사용자 권한 설정

                // 커밋
                $conn->commit();
                header('Location: index.php');
                exit;
            } else {
                throw new Exception("잘못된 비밀번호입니다.");
            }
        } else {
            throw new Exception("사용자 이름이 잘못되었습니다.");
        }
    } catch (Exception $e) {
        // 롤백
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
            <a href="register.php">회원가입</a>
        </nav>
    </header>
    <div class="container">
        <div class="login-form">
            <h1>로그인</h1>
            <form method="post" action="login.php">
                <div class="form-group">
                    <input type="text" name="username" id="username" placeholder="사용자 이름" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="비밀번호" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="로그인">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
