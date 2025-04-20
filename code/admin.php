<?php
include 'config.php';
session_start();

// 관리자 인증
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 트랜잭션 시작
    $conn->begin_transaction();

    try {
        // 카테고리 추가
        if (isset($_POST['add_category'])) {
            $category_name = $_POST['category_name'];
            $sql = "INSERT INTO categories (category_name) VALUES ('$category_name')";
            if (!$conn->query($sql)) {
                throw new Exception("카테고리 추가 중 오류가 발생했습니다: " . $conn->error);
            }
            echo "카테고리가 성공적으로 추가되었습니다.";
        }

        // 상품 추가
        if (isset($_POST['add_product'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $thumbnail = $_POST['thumbnail'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];

            $sql = "INSERT INTO uniforms (name, price, thumbnail, description, category_id) VALUES ('$name', $price, '$thumbnail', '$description', $category_id)";
            if (!$conn->query($sql)) {
                throw new Exception("상품 추가 중 오류가 발생했습니다: " . $conn->error);
            }
            echo "상품이 성공적으로 추가되었습니다.";
        }

        // 상품 수정
        if (isset($_POST['update_product'])) {
            $uniform_id = $_POST['uniform_id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $thumbnail = $_POST['thumbnail'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];

            $sql = "UPDATE uniforms SET name='$name', price=$price, thumbnail='$thumbnail', description='$description', category_id=$category_id WHERE uniform_id=$uniform_id";
            if (!$conn->query($sql)) {
                throw new Exception("상품 수정 중 오류가 발생했습니다: " . $conn->error);
            }
            echo "상품이 성공적으로 수정되었습니다.";
        }

        // 상품 삭제
        if (isset($_POST['delete_product'])) {
            $uniform_id = $_POST['uniform_id'];

            $sql = "DELETE FROM uniforms WHERE uniform_id=$uniform_id";
            if (!$conn->query($sql)) {
                throw new Exception("상품 삭제 중 오류가 발생했습니다: " . $conn->error);
            }
            echo "상품이 성공적으로 삭제되었습니다.";
        }

        // 트랜잭션 커밋
        $conn->commit();
    } catch (Exception $e) {
        // 트랜잭션 롤백
        $conn->rollback();
        echo $e->getMessage();
    }
}

// 모든 카테고리 조회
$category_sql = "SELECT * FROM categories";
$category_result = $conn->query($category_sql);

// 모든 상품 조회
$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT u.*, c.category_name FROM uniforms u JOIN categories c ON u.category_id = c.category_id WHERE u.name LIKE '%$search%'";
} else {
    $sql = "SELECT u.*, c.category_name FROM uniforms u JOIN categories c ON u.category_id = c.category_id";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>관리자 페이지 - 망곰베어스 팝업스토어</title>
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
        <h1>상품 및 카테고리 관리</h1>

        <h2>카테고리 추가</h2>
        <form method="post" action="admin.php">
            <input type="hidden" name="add_category" value="1">
            <p>카테고리명: <input type="text" name="category_name" required></p>
            <p><input type="submit" value="추가하기"></p>
        </form>

        <h2>상품 추가</h2>
        <form method="post" action="admin.php">
            <input type="hidden" name="add_product" value="1">
            <p>상품명: <input type="text" name="name" required></p>
            <p>가격: <input type="number" name="price" required></p>
            <p>썸네일 URL: <input type="text" name="thumbnail" required></p>
            <p>설명: <textarea name="description" required></textarea></p>
            <p>카테고리:
                <select name="category_id" required>
                    <?php
                    while ($category = $category_result->fetch_assoc()) {
                        echo "<option value='" . $category['category_id'] . "'>" . $category['category_name'] . "</option>";
                    }
                    ?>
                </select>
            </p>
            <p><input type="submit" value="추가하기"></p>
        </form>

        <h2>상품 검색</h2>
        <form method="get" action="admin.php">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="검색">
        </form>

        <h2>상품 목록</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>상품명</th>
                <th>가격</th>
                <th>썸네일</th>
                <th>설명</th>
                <th>카테고리</th>
                <th>수정</th>
                <th>삭제</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['uniform_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo number_format($row['price']); ?>원</td>
                    <td><img src="<?php echo $row['thumbnail']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px;"></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['category_name']; ?></td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="uniform_id" value="<?php echo $row['uniform_id']; ?>">
                            <input type="hidden" name="update_product" value="1">
                            <p>상품명: <input type="text" name="name" value="<?php echo $row['name']; ?>" required></p>
                            <p>가격: <input type="number" name="price" value="<?php echo $row['price']; ?>" required></p>
                            <p>썸네일 URL: <input type="text" name="thumbnail" value="<?php echo $row['thumbnail']; ?>" required></p>
                            <p>설명: <textarea name="description" required><?php echo $row['description']; ?></textarea></p>
                            <p>카테고리:
                                <select name="category_id" required>
                                    <?php
                                    // 모든 카테고리 옵션을 다시 출력
                                    $category_result->data_seek(0);
                                    while ($category = $category_result->fetch_assoc()) {
                                        $selected = ($category['category_id'] == $row['category_id']) ? 'selected' : '';
                                        echo "<option value='" . $category['category_id'] . "' $selected>" . $category['category_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </p>
                            <p><input type="submit" value="수정하기"></p>
                        </form>
                    </td>
                    <td>
                        <form method="post" action="admin.php">
                            <input type="hidden" name="uniform_id" value="<?php echo $row['uniform_id']; ?>">
                            <input type="hidden" name="delete_product" value="1">
                            <input type="submit" value="삭제하기">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
