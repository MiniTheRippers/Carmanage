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

// ฟังก์ชันอัปเดตข้อมูลเจ้าของรถ
function updateOwner($conn, $id, $name, $email, $phone) {
    $stmt = $conn->prepare("UPDATE owners SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $id]);
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
    } elseif (isset($_POST['update_owner'])) {
        updateOwner($conn, $_POST['owner_id'], $_POST['name'], $_POST['email'], $_POST['phone']);
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

        <h2>➕ เพิ่ม / ✏️ อัปเดตเจ้าของรถ</h2>
        
        <!-- ฟอร์มเพิ่มข้อมูลเจ้าของรถ -->
        <form method="POST" class="form-container">
            <input type="text" name="name" placeholder="ชื่อ" required>
            <input type="email" name="email" placeholder="อีเมล" required>
            <input type="text" name="phone" placeholder="เบอร์โทรศัพท์" required>
            <button type="submit" name="create_owner">เพิ่มเจ้าของรถ</button>
        </form>

        <!-- ฟอร์มอัปเดตข้อมูลเจ้าของรถ -->
        <form method="POST" id="updateForm" class="form-container" style="display:none;">
            <input type="hidden" name="owner_id" id="owner_id">
            <input type="text" name="name" id="name" placeholder="ชื่อ" required>
            <input type="email" name="email" id="email" placeholder="อีเมล" required>
            <input type="text" name="phone" id="phone" placeholder="เบอร์โทรศัพท์" required>
            <button type="submit" name="update_owner">อัปเดตข้อมูล</button>
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
                        <!-- ปุ่มลบ -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="owner_id" value="<?= $owner['id']; ?>">
                            <button type="submit" name="delete_owner" class="delete-btn">ลบ</button>
                        </form>
                        <!-- ปุ่มอัปเดต -->
                        <button class="update-btn" onclick="editOwner(<?= $owner['id']; ?>, '<?= htmlspecialchars($owner['name']); ?>', '<?= htmlspecialchars($owner['email']); ?>', '<?= htmlspecialchars($owner['phone']); ?>')">อัปเดต</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // ฟังก์ชันเปิดฟอร์มอัปเดตและกรอกข้อมูลในฟอร์ม
        function editOwner(id, name, email, phone) {
            document.getElementById('owner_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('updateForm').style.display = 'block';
        }
    </script>
</body>
</html>
