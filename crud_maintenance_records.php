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

// ฟังก์ชันลบการบำรุงรักษา
function deleteMaintenanceRecord($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM maintenance_records WHERE id = ?");
    $stmt->execute([$id]);
}

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_record'])) {
        createMaintenanceRecord($conn, $_POST['car_id'], $_POST['maintenance_date'], $_POST['details']);
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

        <h2>➕ เพิ่มการบำรุงรักษา</h2>
        <form method="POST" class="form-container">
            <input type="number" name="car_id" placeholder="Car ID" required>
            <input type="date" name="maintenance_date" required>
            <input type="text" name="details" placeholder="Details" required>
            <button type="submit" name="create_record">เพิ่มการบำรุงรักษา</button>
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
                        <form method="POST">
                            <input type="hidden" name="record_id" value="<?= $record['id']; ?>">
                            <button type="submit" name="delete_record" class="delete-btn">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
