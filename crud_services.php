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
    <title>CRUD Services - CarManage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🛠️ จัดการข้อมูลบริการ</h1>

        <h2>➕ เพิ่ม / ✏️ อัปเดตบริการ</h2>
        <!-- ฟอร์มเพิ่มบริการใหม่ -->
        <form method="POST" class="form-container">
            <input type="number" name="car_id" placeholder="รหัสรถ" required>
            <input type="date" name="service_date" required>
            <input type="text" name="description" placeholder="รายละเอียดบริการ" required>
            <input type="number" step="0.01" name="cost" placeholder="ค่าใช้จ่าย (บาท)" required>
            <button type="submit" name="create_service">เพิ่มบริการ</button>
        </form>

        <!-- ฟอร์มอัปเดตข้อมูลบริการ -->
        <form method="POST" id="updateForm" class="form-container" style="display:none;">
            <input type="hidden" name="service_id" id="service_id">
            <input type="number" name="car_id" id="car_id" placeholder="รหัสรถ" required>
            <input type="date" name="service_date" id="service_date" required>
            <input type="text" name="description" id="description" placeholder="รายละเอียดบริการ" required>
            <input type="number" step="0.01" name="cost" id="cost" placeholder="ค่าใช้จ่าย (บาท)" required>
            <button type="submit" name="update_service">อัปเดตบริการ</button>
        </form>

        <h2>📋 ข้อมูลบริการ</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>รหัสรถ</th>
                    <th>วันที่ให้บริการ</th>
                    <th>รายละเอียด</th>
                    <th>ค่าใช้จ่าย (บาท)</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service['id']); ?></td>
                    <td><?= htmlspecialchars($service['car_id']); ?></td>
                    <td><?= htmlspecialchars($service['service_date']); ?></td>
                    <td><?= htmlspecialchars($service['description']); ?></td>
                    <td><?= number_format($service['cost'], 2); ?></td>
                    <td>
                        <!-- ปุ่มลบ -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="service_id" value="<?= $service['id']; ?>">
                            <button type="submit" name="delete_service" class="delete-btn">ลบ</button>
                        </form>
                        <!-- ปุ่มอัปเดต -->
                        <button class="update-btn" onclick="editService(<?= $service['id']; ?>, '<?= htmlspecialchars($service['car_id']); ?>', '<?= htmlspecialchars($service['service_date']); ?>', '<?= htmlspecialchars($service['description']); ?>', '<?= htmlspecialchars($service['cost']); ?>')">อัปเดต</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // ฟังก์ชันเปิดฟอร์มอัปเดตและกรอกข้อมูลในฟอร์ม
        function editService(id, car_id, service_date, description, cost) {
            document.getElementById('service_id').value = id;
            document.getElementById('car_id').value = car_id;
            document.getElementById('service_date').value = service_date;
            document.getElementById('description').value = description;
            document.getElementById('cost').value = cost;
            document.getElementById('updateForm').style.display = 'block';
        }
    </script>
</body>
</html>
