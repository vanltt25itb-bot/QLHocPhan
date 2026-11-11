
<?php

// 1. Kết nối Database
require_once 'config/database.php';

// 2. Kiểm tra người dùng nhấn nút Thêm
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Nhận dữ liệu từ form
    $id = trim($_POST['IDGV']);
    $fullname = trim($_POST['Fullname']);
    $sumClass = $_POST['SumClass'];

    // 4. Kiểm tra dữ liệu
    if ($id == "" || $fullname == "" || $sumClass === "") {
        die("Vui lòng nhập đầy đủ thông tin!");
    }

    if (!ctype_digit((string)$sumClass)) {
        die("Tổng số lớp phải là số nguyên không âm!");
    }

    // 5. Xử lý upload hình ảnh
    if (
        !isset($_FILES['Image']) ||
        $_FILES['Image']['error'] != 0
    ) {
        die("Vui lòng chọn hình ảnh!");
    }

    // Giới hạn dung lượng ảnh 2MB
    if ($_FILES['Image']['size'] > 2 * 1024 * 1024) {
        die("Hình ảnh không được vượt quá 2MB!");
    }

    // Lấy phần mở rộng file
    $extension = strtolower(
        pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION)
    );

    // Các định dạng ảnh được phép
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowed)) {
        die("Định dạng hình ảnh không hợp lệ!");
    }

    // Kiểm tra nội dung file thực sự là ảnh
    $imageInfo = getimagesize($_FILES['Image']['tmp_name']);

    if ($imageInfo === false) {
        die("File tải lên không phải hình ảnh hợp lệ!");
    }

    // 6. Tạo tên file mới
    $image = uniqid('gv_') . '.' . $extension;

    // 7. Xác định nơi lưu ảnh
    $target = __DIR__ . '/images/' . $image;

    // 8. Di chuyển ảnh vào thư mục images
    if (!move_uploaded_file(
        $_FILES['Image']['tmp_name'],
        $target
    )) {
        die("Upload hình ảnh thất bại!");
    }

    // 9. Thêm dữ liệu vào Database
    try {

        $sql = "INSERT INTO GiangVien
                (IDGV, Fullname, Image, SumClass)
                VALUES (:id, :fullname, :image, :sumClass)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':fullname' => $fullname,
            ':image' => $image,
            ':sumClass' => $sumClass
        ]);

        // 10. Chuyển về trang danh sách
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {

        // Nếu lưu Database thất bại thì xóa ảnh vừa upload
        if (file_exists($target)) {
            unlink($target);
        }

        die("Lỗi thêm giảng viên: " . $e->getMessage());
    }

}

?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm giảng viên</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="form-page">

    <div class="form-container">

        <h1>THÊM GIẢNG VIÊN</h1>

        <form method="POST" action="add.php"
              enctype="multipart/form-data">

            <label>Mã giảng viên</label>
            <input type="text" name="IDGV" required
                   placeholder="Ví dụ: GV005">

            <label>Họ và tên</label>
            <input type="text" name="Fullname" required
                   placeholder="Nhập họ và tên">

            <label>Tổng số lớp</label>
            <input type="number" name="SumClass" required
                   min="0" placeholder="Nhập tổng số lớp">

            <label>Hình ảnh giảng viên</label>
            <input type="file" name="Image"
                   accept="image/*" required>

            <button type="submit">
                + Thêm giảng viên
            </button>

        </form>

        <a href="index.php" class="back-link">
            ← Quay lại danh sách
        </a>

    </div>

</div>

</body>
</html>
