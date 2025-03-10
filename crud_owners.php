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
    <title>จัดการข้อมูลเจ้าของรถ - CarManage</title>
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
                <h2><i class="fas fa-users"></i> จัดการข้อมูลเจ้าของรถ</h2>
            </div>
            <div class="card-body">
                <!-- Form for adding/updating owners -->
                <form method="POST" class="form-container">
                    <input type="hidden" name="owner_id" id="owner_id">
                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user"></i> ชื่อ
                        </label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">
                            <i class="fas fa-user"></i> นามสกุล
                        </label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> อีเมล
                        </label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i> เบอร์โทรศัพท์
                        </label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="address">
                            <i class="fas fa-home"></i> ที่อยู่
                        </label>
                        <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="create_owner" id="submit-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มเจ้าของรถ
                        </button>
                        <button type="button" id="reset-btn" onclick="resetForm()" class="btn btn-outline" style="display:none;">
                            <i class="fas fa-plus"></i> เพิ่มใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Owners List -->
        <div class="card fade-in">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> รายชื่อเจ้าของรถ</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ</th>
                            <th>นามสกุล</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>ที่อยู่</th>
                            <th>จัดการ</th>
                        </tr>
                        <?php foreach ($owners as $owner): ?>
                        <tr>
                            <td><?= htmlspecialchars($owner['id']) ?></td>
                            <td><?= htmlspecialchars($owner['first_name']) ?></td>
                            <td><?= htmlspecialchars($owner['last_name']) ?></td>
                            <td><?= htmlspecialchars($owner['email']) ?></td>
                            <td><?= htmlspecialchars($owner['phone']) ?></td>
                            <td><?= htmlspecialchars($owner['address']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning" onclick="editOwner(
                                        '<?= $owner['id'] ?>',
                                        '<?= htmlspecialchars($owner['first_name']) ?>',
                                        '<?= htmlspecialchars($owner['last_name']) ?>',
                                        '<?= htmlspecialchars($owner['email']) ?>',
                                        '<?= htmlspecialchars($owner['phone']) ?>',
                                        '<?= htmlspecialchars($owner['address']) ?>'
                                    )">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="owner_id" value="<?= $owner['id'] ?>">
                                        <button type="submit" name="delete_owner" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
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
        function editOwner(id, first_name, last_name, email, phone, address) {
            document.getElementById('owner_id').value = id;
            document.getElementById('first_name').value = first_name;
            document.getElementById('last_name').value = last_name;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('address').value = address;
            document.getElementById('submit-btn').name = 'update_owner';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-save"></i> อัปเดตข้อมูล';
            document.getElementById('reset-btn').style.display = 'block';
            document.getElementById('first_name').focus();
        }

        function resetForm() {
            document.getElementById('owner_id').value = '';
            document.getElementById('first_name').value = '';
            document.getElementById('last_name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('address').value = '';
            document.getElementById('submit-btn').name = 'create_owner';
            document.getElementById('submit-btn').innerHTML = '<i class="fas fa-plus"></i> เพิ่มเจ้าของรถ';
            document.getElementById('reset-btn').style.display = 'none';
        }
    </script>
</body>
</html>
