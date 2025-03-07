<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';

// ฟังก์ชันดึงข้อมูลเจ้าของรถ
function fetchOwners($conn) {
    $stmt = $conn->query("SELECT * FROM owners");
    return $stmt->fetchAll();
}

// ฟังก์ชันเพิ่มเจ้าของรถใหม่
function createOwner($conn, $name, $email, $phone) {
    $stmt = $conn->prepare("INSERT INTO owners (name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $phone]);
}

// ฟังก์ชันลบเจ้าของรถ
function deleteOwner($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM owners WHERE id = ?");
    $stmt->execute([$id]);
}

// ตรวจสอบการส่งข้อมูล POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_owner'])) {
        createOwner($conn, $_POST['name'], $_POST['email'], $_POST['phone']);
    } elseif (isset($_POST['delete_owner'])) {
        deleteOwner($conn, $_POST['owner_id']);
    }
}

// ดึงข้อมูลเจ้าของรถทั้งหมด
$owners = fetchOwners($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Owners - CarManage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🚗 จัดการข้อมูลเจ้าของรถ</h1>

        <h2>➕ เพิ่มเจ้าของรถ</h2>
        <form method="POST" class="form-container">
            <input type="text" name="name" placeholder="ชื่อ" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="text" name="phone" placeholder="เบอร์โทรศัพท์" required>
            <button type="submit" name="create_owner">เพิ่มเจ้าของรถ</button>
        </form>

        <h2>📋 รายชื่อเจ้าของรถ</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>ชื่อ</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($owners as $owner): ?>
                <tr>
                    <td><?= htmlspecialchars($owner['id']); ?></td>
                    <td><?= htmlspecialchars($owner['name']); ?></td>
                    <td><?= htmlspecialchars($owner['email']); ?></td>
                    <td><?= htmlspecialchars($owner['phone']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="owner_id" value="<?= $owner['id']; ?>">
                            <button type="submit" name="delete_owner" class="delete-btn">ลบ</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
