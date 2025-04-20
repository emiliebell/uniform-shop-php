<?php
include 'config.php';

// 기존 상품 삭제
$delete_sql = "DELETE FROM uniforms";
if ($conn->query($delete_sql) === TRUE) {
    echo "기존 상품이 성공적으로 삭제되었습니다.<br>";
} else {
    echo "상품 삭제 중 오류가 발생했습니다: " . $conn->error . "<br>";
}

// 새로운 상품 추가
$insert_sql = "
INSERT INTO uniforms (name, price, thumbnail) VALUES
('유니폼', 85000, 'images/uniform.jpg'),
('모자(네이비)', 31000, 'images/cap_navy.jpg'),
('모자(스카이블루)', 31000, 'images/cap_skyblue.jpg'),
('기념구', 25000, 'images/commemorative_ball.jpg'),
('응원배트', 12000, 'images/cheering_bat.jpg'),
('머리띠', 15000, 'images/headband.jpg'),
('미니 크로스백', 19000, 'images/mini_crossbag.jpg'),
('키링 인형', 17000, 'images/keyring_doll.jpg'),
('스탠딩 인형', 30000, 'images/standing_doll.jpg'),
('그립톡', 14000, 'images/grip_tok.jpg'),
('스티커팩', 4000, 'images/sticker_pack.jpg'),
('아크릴 키링', 8000, 'images/acrylic_keyring.jpg'),
('콜드컵', 13000, 'images/coldcup.jpg'),
('손수건', 9000, 'images/handkerchief.jpg');
";

if ($conn->query($insert_sql) === TRUE) {
    echo "새로운 상품이 성공적으로 추가되었습니다.";
} else {
    echo "상품 추가 중 오류가 발생했습니다: " . $conn->error;
}

$conn->close();
?>
