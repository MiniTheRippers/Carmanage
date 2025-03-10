<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';

// ฟังก์ชันดึงข้อมูลบริการ
function fetchServices($conn) {
    $stmt = $conn->query("SELECT * FROM services");
    return $stmt->fetchAll();
}

// ฟังก์ชันเพิ่มบริการใหม่
function createService($conn, $car_id, $service_date, $description, $cost) {
    $stmt = $conn->prepare("INSERT INTO services (car_id, service_date, description, cost) VALUES (?, ?, ?, ?)");
    $stmt->execute([$car_id, $service_date, $description, $cost]);
}

// ฟังก์ชันอัปเดตบริการ
function updateService($conn, $id, $car_id, $service_date, $description, $cost) {
    $stmt = $conn->prepare("UPDATE services SET car_id = ?, service_date = ?, description = ?, cost = ? WHERE id = ?");
    $stmt->execute([$car_id, $service_date, $description, $cost, $id]);
}

// ฟังก์ชันลบบริการ
function deleteService($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
}

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_service'])) {
        createService($conn, $_POST['car_id'], $_POST['service_date'], $_POST['description'], $_POST['cost']);
    } elseif (isset($_POST['update_service'])) {
        updateService($conn, $_POST['service_id'], $_POST['car_id'], $_POST['service_date'], $_POST['description'], $_POST['cost']);
    } elseif (isset($_POST['delete_service'])) {
        deleteService($conn, $_POST['service_id']);
    }
}

// ดึงข้อมูลบริการทั้งหมด
$services = fetchServices($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลบริการ - CarManage</title>
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
                <h2><i class="fas fa-concierge-bell"></i> จัดการข้อมูลบริการ</h2>
            </div>
            <div class="card-body">
                <!-- Form for adding/updating services -->
                <form method="POST" class="form-container">
                    <input type="hidden" name="service_id" id="service_id">
                    <div class="form-group">
                        <label for="service_name">
                            <i class="fas fa-tag"></i> ชื่อบริการ
                        </label>
                        <input type="text" id="service_name" name="service_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            <i class="fas fa-align-left"></i> รายละเอียด
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">
                            <i class="fas fa-money-bill-wave"></i> ราคา
                        </label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="duration">
                            <i class="fas fa-clock"></i> ระยะเวลา (นาที)
                        </label>
                        <input type="number" id="duration" name="duration" class="form-control" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="create_service" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มบริการ
                        </button>
                        <button type="button" id="reset-btn" onclick="resetForm()" class="btn btn-outline" style="display:none;">
                            <i class="fas fa-plus"></i> เพิ่มใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Services List -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> รายการบริการ</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อบริการ</th>
                            <th>รายละเอียด</th>
                            <th>ราคา</th>
                            <th>ระยะเวลา</th>
                            <th>จัดการ</th>
                        </tr>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['id']) ?></td>
                            <td><?= htmlspecialchars($service['service_name']) ?></td>
                            <td><?= htmlspecialchars($service['description']) ?></td>
                            <td><?= number_format($service['price'], 2) ?></td>
                            <td><?= htmlspecialchars($service['duration']) ?> นาที</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning" onclick="editService(
                                        '<?= $service['id'] ?>',
                                        '<?= htmlspecialchars($service['service_name']) ?>',
                                        '<?= htmlspecialchars($service['description']) ?>',
                                        '<?= htmlspecialchars($service['price']) ?>',
                                        '<?= htmlspecialchars($service['duration']) ?>'
                                    )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                        <button type="submit" name="delete_service" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
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
        function editService(id, service_name, description, price, duration) {
            document.getElementById('service_id').value = id;
            document.getElementById('service_name').value = service_name;
            document.getElementById('description').value = description;
            document.getElementById('price').value = price;
            document.getElementById('duration').value = duration;
            document.getElementById('submit-btn').name = 'update_service';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-save"></i> อัปเดตข้อมูล';
            document.getElementById('reset-btn').style.display = 'block';
            document.getElementById('service_name').focus();
        }

        function resetForm() {
            document.getElementById('service_id').value = '';
            document.getElementById('service_name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('duration').value = '';
            document.getElementById('submit-btn').name = 'create_service';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-plus"></i> เพิ่มบริการ';
            document.getElementById('reset-btn').style.display = 'none';
        }
    </script>
</body>
</html>
