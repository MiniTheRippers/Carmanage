<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';

// ฟังก์ชันดึงข้อมูลประกันภัย
function fetchInsurance($conn) {
    $stmt = $conn->query("SELECT * FROM insurance");
    return $stmt->fetchAll();
}

// ฟังก์ชันเพิ่มประกันภัยใหม่
function createInsurance($conn, $car_id, $provider, $policy_number, $expiration_date) {
    $stmt = $conn->prepare("INSERT INTO insurance (car_id, provider, policy_number, expiration_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$car_id, $provider, $policy_number, $expiration_date]);
}

// ฟังก์ชันลบประกันภัย
function deleteInsurance($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM insurance WHERE id = ?");
    $stmt->execute([$id]);
}

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_insurance'])) {
        createInsurance($conn, $_POST['car_id'], $_POST['provider'], $_POST['policy_number'], $_POST['expiration_date']);
    } elseif (isset($_POST['delete_insurance'])) {
        deleteInsurance($conn, $_POST['insurance_id']);
    }
}

// ดึงข้อมูลประกันภัยทั้งหมด
$insurance_records = fetchInsurance($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Insurance - CarManage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🛡️ จัดการข้อมูลประกันภัย</h1>

        <h2>➕ เพิ่มประกันภัย</h2>
        <form method="POST" class="form-container">
            <input type="number" name="car_id" placeholder="Car ID" required>
            <input type="text" name="provider" placeholder="Provider" required>
            <input type="text" name="policy_number" placeholder="Policy Number" required>
            <input type="date" name="expiration_date" placeholder="Expiration Date" required>
            <button type="submit" name="create_insurance">เพิ่มประกันภัย</button>
        </form>

        <h2>📋 ข้อมูลประกันภัย</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Car ID</th>
                    <th>Provider</th>
                    <th>Policy Number</th>
                    <th>Expiration Date</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($insurance_records as $insurance): ?>
                <tr>
                    <td><?= htmlspecialchars($insurance['id']); ?></td>
                    <td><?= htmlspecialchars($insurance['car_id']); ?></td>
                    <td><?= htmlspecialchars($insurance['provider']); ?></td>
                    <td><?= htmlspecialchars($insurance['policy_number']); ?></td>
                    <td><?= htmlspecialchars($insurance['expiration_date']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="insurance_id" value="<?= $insurance['id']; ?>">
                            <button type="submit" name="delete_insurance" class="delete-btn">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
