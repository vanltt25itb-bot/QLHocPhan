
<?php

// 1. Kết nối Database
require_once 'config/database.php';

// 2. Lấy IDGV từ URL
$id = $_GET['id'] ?? '';

// 3. Truy vấn thông tin giảng viên
$sql = "SELECT * FROM GiangVien WHERE IDGV = :id";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);

$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// 4. Kiểm tra giảng viên có tồn tại không
if (!$teacher) {
    die("Không tìm thấy giảng viên!");
}

// 5. Xử lý khi nhấn nút Cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['Fullname'] ?? '');
    $sumClass = $_POST['SumClass'] ?? '';

    if ($fullname == "" || $sumClass === "" ||
        !ctype_digit((string)$sumClass)) {
        die("Thông tin không hợp lệ!");
    }

    // Giữ ảnh cũ nếu không chọn ảnh mới
    $image = $teacher['Image'];
    $newImagePath = null;

    // 6. Kiểm tra ảnh mới
    if (
        isset($_FILES['Image']) &&
        $_FILES['Image']['error'] != UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['Image']['error'] != UPLOAD_ERR_OK) {
            die("Lỗi upload hình ảnh!");
        }

        if ($_FILES['Image']['size'] > 2 * 1024 * 1024) {
            die("Ảnh không được vượt quá 2MB!");
        }

        $extension = strtolower(
            pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION)
        );

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowed)) {
            die("Định dạng ảnh không hợp lệ!");
        }

        if (getimagesize($_FILES['Image']['tmp_name']) === false) {
            die("File không phải hình ảnh hợp lệ!");
        }

        $image = uniqid('gv_') . '.' . $extension;

        $newImagePath = __DIR__ . '/images/' . $image;

        if (!move_uploaded_file(
            $_FILES['Image']['tmp_name'],
            $newImagePath
        )) {
            die("Upload ảnh thất bại!");
        }
    }

    // 7. Cập nhật Database
    try {

        $sql = "UPDATE GiangVien SET
                Fullname = :fullname,
                Image = :image,
                SumClass = :sumClass
                WHERE IDGV = :id";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':fullname' => $fullname,
            ':image' => $image,
            ':sumClass' => $sumClass,
            ':id' => $id
        ]);

        // Xóa ảnh cũ nếu đã thay ảnh mới
        if ($newImagePath !== null && !empty($teacher['Image'])) {

            $oldImagePath = __DIR__ . '/images/' .
                            basename($teacher['Image']);

            if (is_file($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        header("Location: index.php");
        exit();

    } catch (PDOException $e) {

        if ($newImagePath !== null && is_file($newImagePath)) {
            unlink($newImagePath);
        }

        die("Lỗi cập nhật: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sửa giảng viên</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="form-page">

    <div class="form-container">

        <h1>SỬA THÔNG TIN GIẢNG VIÊN</h1>

        <form method="POST" enctype="multipart/form-data">

            <!-- Mã giảng viên -->
            <label>Mã giảng viên</label>

            <input type="text"
                   value="<?= htmlspecialchars($teacher['IDGV']) ?>"
                   disabled>

            <!-- Họ và tên -->
            <label>Họ và tên</label>

            <input type="text"
                   name="Fullname"
                   value="<?= htmlspecialchars($teacher['Fullname']) ?>"
                   placeholder="Nhập họ và tên"
                   required>

            <!-- Tổng số lớp -->
            <label>Tổng số lớp</label>

            <input type="number"
                   name="SumClass"
                   min="0"
                   value="<?= htmlspecialchars((string)$teacher['SumClass']) ?>"
                   required>

            <!-- Ảnh hiện tại -->
            <label>Ảnh hiện tại</label>

            <?php if (!empty($teacher['Image'])): ?>

                <img
                    src="images/<?= htmlspecialchars($teacher['Image']) ?>"
                    alt="Ảnh giảng viên"
                    class="current-image"
                >

            <?php else: ?>

                <p>Chưa có hình ảnh.</p>

            <?php endif; ?>

            <!-- Chọn ảnh mới -->
            <label>Chọn ảnh mới (nếu muốn thay đổi)</label>

            <input type="file"
                   name="Image"
                   accept="image/jpeg,image/png,image/gif,image/webp">

            <!-- Nút cập nhật -->
            <button type="submit">
                Cập nhật thông tin
            </button>

        </form>

        <a href="index.php" class="back-link">
            ← Quay lại danh sách
        </a>

    </div>

</div>

</body>
</html>

