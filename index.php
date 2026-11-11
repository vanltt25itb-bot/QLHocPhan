
<?php

// 1. Kết nối Database
require_once 'config/database.php';

// 2. Truy vấn tất cả giảng viên
$sql = "SELECT * FROM GiangVien";

$stmt = $conn->prepare($sql);

// 3. Thực thi câu lệnh
$stmt->execute();

// 4. Lấy toàn bộ dữ liệu
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý giảng viên</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">

        <h1>QUẢN LÝ GIẢNG VIÊN</h1>

        <!-- Nút thêm -->
        <div class="add-button">
            <a href="add.php">+ Thêm giảng viên</a>
        </div>

        <!-- Bảng dữ liệu -->
        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>Mã giảng viên</th>
                        <th>Họ và tên</th>
                        <th>Hình ảnh</th>
                        <th>Tổng số lớp</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($teachers) > 0): ?>

                        <?php foreach ($teachers as $teacher): ?>

                            <tr>

                                <!-- Mã giảng viên -->
                                <td>
                                    <?= htmlspecialchars($teacher['IDGV']) ?>
                                </td>

                                <!-- Họ tên -->
                                <td>
                                    <?= htmlspecialchars($teacher['Fullname']) ?>
                                </td>

                                <!-- Hình ảnh -->
                                <td>
                                    <img
                                        src="images/<?= htmlspecialchars($teacher['Image'] ?? '') ?>"
                                        alt="Ảnh giảng viên"
                                        class="teacher-image"
                                    >
                                </td>

                                <!-- Tổng số lớp -->
                                <td>
                                    <?= htmlspecialchars((string)$teacher['SumClass']) ?>
                                </td>

                                <!-- Thao tác -->
                                <td>

                                    <div class="actions">

                                        <a href="edit.php?id=<?= urlencode($teacher['IDGV']) ?>">
                                            Sửa
                                        </a>

                                        <form method="POST" action="delete.php"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa giảng viên này không?')">

                                            <input
                                                type="hidden"
                                                name="IDGV"
                                                value="<?= htmlspecialchars($teacher['IDGV']) ?>"
                                            >

                                            <button type="submit">Xóa</button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5">
                                Chưa có dữ liệu giảng viên!
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>

