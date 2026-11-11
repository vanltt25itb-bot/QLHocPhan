
<?php

// 1. Thông tin kết nối Database
$host = "localhost";
$dbname = "QLHocPhan";
$username = "root";
$password = "";

// 2. Kết nối MySQL bằng PDO
try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // 3. Thiết lập chế độ báo lỗi
    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Connection failed: " . $e->getMessage());

}

?>
