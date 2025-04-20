<?php
$servername = "localhost";
$username = "db2022320301";
$password = "emily21@korea.ac.kr";
$dbname = "db2022320301";

// 연결 생성
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
