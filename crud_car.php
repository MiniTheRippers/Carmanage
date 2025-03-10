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

// ฟังก์ชันอัปเดตรถยนต์
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
    <title>จัดการข้อมูลรถยนต์ - CarManage</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-content">
            <a href="dashboard.php" class="nav-brand">
                <i class="fas fa-car"></i> CarManage
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> หน้าหลัก
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card mb-4 fade-in">
            <div class="card-header">
                <h2><i class="fas fa-car"></i> จัดการข้อมูลรถยนต์</h2>
            </div>
            <div class="card-body">
                <!-- Form for adding/updating cars -->
                <form method="POST" class="form-container">
                    <input type="hidden" name="car_id" id="car_id">
                    <div class="form-group">
                        <label for="make">
                            <i class="fas fa-building"></i> ยี่ห้อ
                        </label>
                        <input type="text" id="make" name="make" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="model">
                            <i class="fas fa-car-side"></i> รุ่น
                        </label>
                        <input type="text" id="model" name="model" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="year">
                            <i class="fas fa-calendar"></i> ปี
                        </label>
                        <input type="number" id="year" name="year" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="color">
                            <i class="fas fa-palette"></i> สี
                        </label>
                        <input type="text" id="color" name="color" class="form-control" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="create_car" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มรถยนต์
                        </button>
                        <button type="button" id="reset-btn" onclick="resetForm()" class="btn btn-outline" style="display:none;">
                            <i class="fas fa-plus"></i> เพิ่มใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Car List -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> รายการรถยนต์</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>ยี่ห้อ</th>
                            <th>รุ่น</th>
                            <th>ปี</th>
                            <th>สี</th>
                            <th>จัดการ</th>
                        </tr>
                        <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?= htmlspecialchars($car['id']) ?></td>
                            <td><?= htmlspecialchars($car['make']) ?></td>
                            <td><?= htmlspecialchars($car['model']) ?></td>
                            <td><?= htmlspecialchars($car['year']) ?></td>
                            <td><?= htmlspecialchars($car['color']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning" onclick="editCar(
                                        '<?= $car['id'] ?>',
                                        '<?= htmlspecialchars($car['make']) ?>',
                                        '<?= htmlspecialchars($car['model']) ?>',
                                        '<?= htmlspecialchars($car['year']) ?>',
                                        '<?= htmlspecialchars($car['color']) ?>'
                                    )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                                        <button type="submit" name="delete_car" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-group .btn {
            padding: 0.5rem;
        }
    </style>

    <script>
        function editCar(id, make, model, year, color) {
            document.getElementById('car_id').value = id;
            document.getElementById('make').value = make;
            document.getElementById('model').value = model;
            document.getElementById('year').value = year;
            document.getElementById('color').value = color;
            document.getElementById('submit-btn').name = 'update_car';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-save"></i> อัปเดตข้อมูล';
            document.getElementById('reset-btn').style.display = 'block';
            document.getElementById('make').focus();
        }

        function resetForm() {
            document.getElementById('car_id').value = '';
            document.getElementById('make').value = '';
            document.getElementById('model').value = '';
            document.getElementById('year').value = '';
            document.getElementById('color').value = '';
            document.getElementById('submit-btn').name = 'create_car';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-plus"></i> เพิ่มรถยนต์';
            document.getElementById('reset-btn').style.display = 'none';
        }
    </script>
</body>
</html>
