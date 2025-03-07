<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';

// ฟังก์ชันดึงข้อมูลรถยนต์
function fetchCars($conn) {
    $stmt = $conn->query("SELECT * FROM cars");
    return $stmt->fetchAll();
}

// ฟังก์ชันเพิ่มรถยนต์ใหม่
function createCar($conn, $make, $model, $year, $color) {
    $stmt = $conn->prepare("INSERT INTO cars (make, model, year, color) VALUES (?, ?, ?, ?)");
    $stmt->execute([$make, $model, $year, $color]);
}

// ฟังก์ชันอัปเดตข้อมูลรถยนต์
function updateCar($conn, $id, $make, $model, $year, $color) {
    $stmt = $conn->prepare("UPDATE cars SET make = ?, model = ?, year = ?, color = ? WHERE id = ?");
    $stmt->execute([$make, $model, $year, $color, $id]);
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
    } elseif (isset($_POST['update_car'])) {
        updateCar($conn, $_POST['car_id'], $_POST['make'], $_POST['model'], $_POST['year'], $_POST['color']);
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

        <h2>➕ เพิ่ม / ✏️ อัปเดตรถยนต์</h2>
        
        <!-- ฟอร์มเพิ่มรถยนต์ -->
        <form method="POST" class="form-container">
            <input type="text" name="make" placeholder="Make" required>
            <input type="text" name="model" placeholder="Model" required>
            <input type="number" name="year" placeholder="Year" required>
            <input type="text" name="color" placeholder="Color" required>
            <button type="submit" name="create_car">เพิ่มรถยนต์</button>
        </form>

        <!-- ฟอร์มอัปเดตรถยนต์ -->
        <form method="POST" id="updateForm" class="form-container" style="display:none;">
            <input type="hidden" name="car_id" id="car_id">
            <input type="text" name="make" id="make" placeholder="Make" required>
            <input type="text" name="model" id="model" placeholder="Model" required>
            <input type="number" name="year" id="year" placeholder="Year" required>
            <input type="text" name="color" id="color" placeholder="Color" required>
            <button type="submit" name="update_car">อัปเดตข้อมูล</button>
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
                        <!-- ปุ่มลบ -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="car_id" value="<?= $car['id']; ?>">
                            <button type="submit" name="delete_car" class="delete-btn">ลบ</button>
                        </form>
                        <!-- ปุ่มอัปเดต -->
                        <button class="update-btn" onclick="editCar(<?= $car['id']; ?>, '<?= htmlspecialchars($car['make']); ?>', '<?= htmlspecialchars($car['model']); ?>', '<?= htmlspecialchars($car['year']); ?>', '<?= htmlspecialchars($car['color']); ?>')">อัปเดต</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // ฟังก์ชันเปิดฟอร์มอัปเดตและกรอกข้อมูลในฟอร์ม
        function editCar(id, make, model, year, color) {
            document.getElementById('car_id').value = id;
            document.getElementById('make').value = make;
            document.getElementById('model').value = model;
            document.getElementById('year').value = year;
            document.getElementById('color').value = color;
            document.getElementById('updateForm').style.display = 'block';
        }
    </script>
</body>
</html>
