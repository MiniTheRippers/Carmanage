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
    <title>จัดการข้อมูลประกันภัย - CarManage</title>
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
                <h2><i class="fas fa-shield-alt"></i> จัดการข้อมูลประกันภัย</h2>
            </div>
            <div class="card-body">
                <!-- Form for adding/updating insurance -->
                <form method="POST" class="form-container">
                    <input type="hidden" name="insurance_id" id="insurance_id">
                    <div class="form-group">
                        <label for="car_id">
                            <i class="fas fa-car"></i> รหัสรถ
                        </label>
                        <input type="number" id="car_id" name="car_id" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="provider">
                            <i class="fas fa-building"></i> บริษัทประกัน
                        </label>
                        <input type="text" id="provider" name="provider" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="policy_number">
                            <i class="fas fa-file-contract"></i> เลขกรมธรรม์
                        </label>
                        <input type="text" id="policy_number" name="policy_number" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="start_date">
                            <i class="fas fa-calendar-plus"></i> วันที่เริ่มคุ้มครอง
                        </label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">
                            <i class="fas fa-calendar-minus"></i> วันที่สิ้นสุดคุ้มครอง
                        </label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="create_insurance" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มประกันภัย
                        </button>
                        <button type="button" id="reset-btn" onclick="resetForm()" class="btn btn-outline" style="display:none;">
                            <i class="fas fa-plus"></i> เพิ่มใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Insurance List -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> รายการประกันภัย</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>รหัสรถ</th>
                            <th>บริษัทประกัน</th>
                            <th>เลขกรมธรรม์</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>จัดการ</th>
                        </tr>
                        <?php foreach ($insurance_records as $insurance): ?>
                        <tr>
                            <td><?= htmlspecialchars($insurance['id']) ?></td>
                            <td><?= htmlspecialchars($insurance['car_id']) ?></td>
                            <td><?= htmlspecialchars($insurance['provider']) ?></td>
                            <td><?= htmlspecialchars($insurance['policy_number']) ?></td>
                            <td><?= htmlspecialchars($insurance['start_date']) ?></td>
                            <td><?= htmlspecialchars($insurance['end_date']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning" onclick="editInsurance(
                                        '<?= $insurance['id'] ?>',
                                        '<?= htmlspecialchars($insurance['car_id']) ?>',
                                        '<?= htmlspecialchars($insurance['provider']) ?>',
                                        '<?= htmlspecialchars($insurance['policy_number']) ?>',
                                        '<?= htmlspecialchars($insurance['start_date']) ?>',
                                        '<?= htmlspecialchars($insurance['end_date']) ?>'
                                    )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="insurance_id" value="<?= $insurance['id'] ?>">
                                        <button type="submit" name="delete_insurance" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
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
        function editInsurance(id, car_id, provider, policy_number, start_date, end_date) {
            document.getElementById('insurance_id').value = id;
            document.getElementById('car_id').value = car_id;
            document.getElementById('provider').value = provider;
            document.getElementById('policy_number').value = policy_number;
            document.getElementById('start_date').value = start_date;
            document.getElementById('end_date').value = end_date;
            document.getElementById('submit-btn').name = 'update_insurance';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-save"></i> อัปเดตข้อมูล';
            document.getElementById('reset-btn').style.display = 'block';
            document.getElementById('car_id').focus();
        }

        function resetForm() {
            document.getElementById('insurance_id').value = '';
            document.getElementById('car_id').value = '';
            document.getElementById('provider').value = '';
            document.getElementById('policy_number').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('submit-btn').name = 'create_insurance';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-plus"></i> เพิ่มประกันภัย';
            document.getElementById('reset-btn').style.display = 'none';
        }
    </script>
</body>
</html>
