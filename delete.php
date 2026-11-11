
<?php

// 1. Kết nối Database
require_once 'config/database.php';

// 2. Chỉ xử lý khi nhận yêu cầu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Nhận IDGV
    $id = $_POST['IDGV'] ?? '';

    // 4. Lấy thông tin ảnh trước khi xóa
    $sql = "SELECT Image FROM GiangVien WHERE IDGV = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
        die("Không tìm thấy giảng viên!");
    }

    // 5. Xóa dữ liệu trong Database
    $sql = "DELETE FROM GiangVien WHERE IDGV = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);

    // 6. Xóa ảnh trong thư mục images
    if (!empty($teacher['Image'])) {

        $imagePath = __DIR__ . '/images/' .
                     basename($teacher['Image']);

        if (is_file($imagePath)) {
            unlink($imagePath);
        }
    }

    // 7. Quay về danh sách
    header("Location: index.php");
    exit();

} else {

    header("Location: index.php");
    exit();

}

?>
