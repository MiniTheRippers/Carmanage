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
    <title>CRUD Maintenance Records - CarManage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔧 จัดการข้อมูลการบำรุงรักษา</h1>

        <h2>➕ เพิ่ม / ✏️ อัปเดตการบำรุงรักษา</h2>
        
        <!-- ฟอร์มเพิ่มการบำรุงรักษา -->
        <form method="POST" class="form-container">
            <input type="number" name="car_id" placeholder="Car ID" required>
            <input type="date" name="maintenance_date" required>
            <input type="text" name="details" placeholder="Details" required>
            <button type="submit" name="create_record">เพิ่มการบำรุงรักษา</button>
        </form>

        <!-- ฟอร์มอัปเดตการบำรุงรักษา -->
        <form method="POST" id="updateForm" class="form-container" style="display:none;">
            <input type="hidden" name="record_id" id="record_id">
            <input type="number" name="car_id" id="car_id" placeholder="Car ID" required>
            <input type="date" name="maintenance_date" id="maintenance_date" required>
            <input type="text" name="details" id="details" placeholder="Details" required>
            <button type="submit" name="update_record">อัปเดตข้อมูล</button>
        </form>

        <h2>📋 ข้อมูลการบำรุงรักษา</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Car ID</th>
                    <th>Maintenance Date</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($maintenance_records as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['id']); ?></td>
                    <td><?= htmlspecialchars($record['car_id']); ?></td>
                    <td><?= htmlspecialchars($record['maintenance_date']); ?></td>
                    <td><?= htmlspecialchars($record['details']); ?></td>
                    <td>
                        <!-- ปุ่มลบ -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="record_id" value="<?= $record['id']; ?>">
                            <button type="submit" name="delete_record" class="delete-btn">ลบ</button>
                        </form>
                        <!-- ปุ่มอัปเดต -->
                        <button class="update-btn" onclick="editRecord(<?= $record['id']; ?>, '<?= htmlspecialchars($record['car_id']); ?>', '<?= htmlspecialchars($record['maintenance_date']); ?>', '<?= htmlspecialchars($record['details']); ?>')">อัปเดต</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // ฟังก์ชันเปิดฟอร์มอัปเดตและกรอกข้อมูลในฟอร์ม
        function editRecord(id, car_id, maintenance_date, details) {
            document.getElementById('record_id').value = id;
            document.getElementById('car_id').value = car_id;
            document.getElementById('maintenance_date').value = maintenance_date;
            document.getElementById('details').value = details;
            document.getElementById('updateForm').style.display = 'block';
        }
    </script>
</body>
</html>
