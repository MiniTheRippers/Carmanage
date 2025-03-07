<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';

// ฟังก์ชันดึงข้อมูลรถยนต์
function fetchCars($conn) {
    $stmt = $conn->query("SELECT * FROM cars");
    return $stmt->fetchAll();
}

// ฟังก์ชันเพิ่มรถใหม่
function createCar($conn, $make, $model, $year, $color) {
    $stmt = $conn->prepare("INSERT INTO cars (make, model, year, color) VALUES (?, ?, ?, ?)");
    $stmt->execute([$make, $model, $year, $color]);
}

// ฟังก์ชันลบรถยนต์
function deleteCar($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$id]);
}

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_car'])) {
        createCar($conn, $_POST['make'], $_POST['model'], $_POST['year'], $_POST['color']);
    } elseif (isset($_POST['delete_car'])) {
        deleteCar($conn, $_POST['car_id']);
    }
}

// ดึงข้อมูลรถทั้งหมด
$cars = fetchCars($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Car - CarManage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🚗 จัดการข้อมูลรถยนต์</h1>

        <h2>➕ เพิ่มรถยนต์</h2>
        <form method="POST" class="form-container">
            <input type="text" name="make" placeholder="Make" required>
            <input type="text" name="model" placeholder="Model" required>
            <input type="number" name="year" placeholder="Year" required>
            <input type="text" name="color" placeholder="Color" required>
            <button type="submit" name="create_car">เพิ่มรถยนต์</button>
        </form>

        <h2>📋 ข้อมูลรถยนต์</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Color</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($cars as $car): ?>
                <tr>
                    <td><?= htmlspecialchars($car['id']); ?></td>
                    <td><?= htmlspecialchars($car['make']); ?></td>
                    <td><?= htmlspecialchars($car['model']); ?></td>
                    <td><?= htmlspecialchars($car['year']); ?></td>
                    <td><?= htmlspecialchars($car['color']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="car_id" value="<?= $car['id']; ?>">
                            <button type="submit" name="delete_car" class="delete-btn">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
