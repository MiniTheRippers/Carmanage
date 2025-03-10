<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';

// ฟังก์ชันดึงข้อมูลการบำรุงรักษา
function fetchMaintenanceRecords($conn) {
    $stmt = $conn->query("SELECT * FROM maintenance_records");
    return $stmt->fetchAll();
}

// ฟังก์ชันเพิ่มการบำรุงรักษาใหม่
function createMaintenanceRecord($conn, $car_id, $maintenance_date, $details) {
    $stmt = $conn->prepare("INSERT INTO maintenance_records (car_id, maintenance_date, details) VALUES (?, ?, ?)");
    $stmt->execute([$car_id, $maintenance_date, $details]);
}

// ฟังก์ชันอัปเดตการบำรุงรักษา
function updateMaintenanceRecord($conn, $id, $car_id, $maintenance_date, $details) {
    $stmt = $conn->prepare("UPDATE maintenance_records SET car_id = ?, maintenance_date = ?, details = ? WHERE id = ?");
    $stmt->execute([$car_id, $maintenance_date, $details, $id]);
}

// ฟังก์ชันลบการบำรุงรักษา
function deleteMaintenanceRecord($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM maintenance_records WHERE id = ?");
    $stmt->execute([$id]);
}

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_record'])) {
        createMaintenanceRecord($conn, $_POST['car_id'], $_POST['maintenance_date'], $_POST['details']);
    } elseif (isset($_POST['update_record'])) {
        updateMaintenanceRecord($conn, $_POST['record_id'], $_POST['car_id'], $_POST['maintenance_date'], $_POST['details']);
    } elseif (isset($_POST['delete_record'])) {
        deleteMaintenanceRecord($conn, $_POST['record_id']);
    }
}

// ดึงข้อมูลการบำรุงรักษาทั้งหมด
$maintenance_records = fetchMaintenanceRecords($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลการบำรุงรักษา - CarManage</title>
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
                <h2><i class="fas fa-tools"></i> จัดการข้อมูลการบำรุงรักษา</h2>
            </div>
            <div class="card-body">
                <!-- Form for adding/updating maintenance records -->
                <form method="POST" class="form-container">
                    <input type="hidden" name="record_id" id="record_id">
                    <div class="form-group">
                        <label for="car_id">
                            <i class="fas fa-car"></i> รหัสรถ
                        </label>
                        <input type="number" id="car_id" name="car_id" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="maintenance_date">
                            <i class="fas fa-calendar"></i> วันที่บำรุงรักษา
                        </label>
                        <input type="date" id="maintenance_date" name="maintenance_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="details">
                            <i class="fas fa-clipboard-list"></i> รายละเอียด
                        </label>
                        <textarea id="details" name="details" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="create_record" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มรายการ
                        </button>
                        <button type="button" id="reset-btn" onclick="resetForm()" class="btn btn-outline" style="display:none;">
                            <i class="fas fa-plus"></i> เพิ่มใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Maintenance Records List -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> รายการบำรุงรักษา</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>รหัสรถ</th>
                            <th>วันที่บำรุงรักษา</th>
                            <th>รายละเอียด</th>
                            <th>จัดการ</th>
                        </tr>
                        <?php foreach ($maintenance_records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['id']) ?></td>
                            <td><?= htmlspecialchars($record['car_id']) ?></td>
                            <td><?= htmlspecialchars($record['maintenance_date']) ?></td>
                            <td><?= htmlspecialchars($record['details']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning" onclick="editRecord(
                                        '<?= $record['id'] ?>',
                                        '<?= htmlspecialchars($record['car_id']) ?>',
                                        '<?= htmlspecialchars($record['maintenance_date']) ?>',
                                        '<?= htmlspecialchars($record['details']) ?>'
                                    )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="record_id" value="<?= $record['id'] ?>">
                                        <button type="submit" name="delete_record" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
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
        function editRecord(id, car_id, maintenance_date, details) {
            document.getElementById('record_id').value = id;
            document.getElementById('car_id').value = car_id;
            document.getElementById('maintenance_date').value = maintenance_date;
            document.getElementById('details').value = details;
            document.getElementById('submit-btn').name = 'update_record';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-save"></i> อัปเดตข้อมูล';
            document.getElementById('reset-btn').style.display = 'block';
            document.getElementById('car_id').focus();
        }

        function resetForm() {
            document.getElementById('record_id').value = '';
            document.getElementById('car_id').value = '';
            document.getElementById('maintenance_date').value = '';
            document.getElementById('details').value = '';
            document.getElementById('submit-btn').name = 'create_record';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-plus"></i> เพิ่มรายการ';
            document.getElementById('reset-btn').style.display = 'none';
        }
    </script>
</body>
</html>
