<?php
// รวมไฟล์ db.php เพื่อเชื่อมต่อฐานข้อมูล
include 'db.php';
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// ฟังก์ชันเพื่อดึงข้อมูลจากตาราง
function fetchData($conn, $table) {
    $stmt = $conn->query("SELECT * FROM $table");
    return $stmt->fetchAll();
}

// ดึงข้อมูลจากทุกตาราง
$cars = fetchData($conn, 'cars');
$maintenance_records = fetchData($conn, 'maintenance_records');
$insurance = fetchData($conn, 'insurance');
$services = fetchData($conn, 'services');
$owners = fetchData($conn, 'owners');

// Handle logout logic
if (isset($_GET['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page after logging out
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Database Data - CarManage</title>
    <link rel="stylesheet" href="style.css"> <!-- ลิงก์ไปยังไฟล์ CSS -->
</head>
<body>
    <div class="container">
        <h1>ข้อมูลจากฐานข้อมูล CarManage</h1>

        <!-- Logout button -->
        <div class="logout-btn-container">
            <a href="?logout=true"><button>ออกจากระบบ</button></a>
        </div>

        <!-- ข้อมูลรถยนต์ -->
        <section>
            <h2>ข้อมูลรถยนต์</h2>
            <div class="btn-container">
                <a href="crud_car.php"><button>จัดการรถยนต์</button></a>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Color</th>
                    </tr>
                    <?php foreach ($cars as $car): ?>
                    <tr>
                        <td><?= htmlspecialchars($car['id']); ?></td>
                        <td><?= htmlspecialchars($car['make']); ?></td>
                        <td><?= htmlspecialchars($car['model']); ?></td>
                        <td><?= htmlspecialchars($car['year']); ?></td>
                        <td><?= htmlspecialchars($car['color']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>

        <!-- ข้อมูลการบำรุงรักษา -->
        <section>
            <h2>ข้อมูลการบำรุงรักษา</h2>
            <div class="btn-container">
                <a href="crud_maintenance_records.php"><button>จัดการการบำรุงรักษา</button></a>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Car ID</th>
                        <th>Maintenance Date</th>
                        <th>Details</th>
                    </tr>
                    <?php foreach ($maintenance_records as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['id']); ?></td>
                        <td><?= htmlspecialchars($record['car_id']); ?></td>
                        <td><?= htmlspecialchars($record['maintenance_date']); ?></td>
                        <td><?= htmlspecialchars($record['details']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>

        <!-- ข้อมูลประกันภัย -->
        <section>
            <h2>ข้อมูลประกันภัย</h2>
            <div class="btn-container">
                <a href="crud_insurance.php"><button>จัดการประกันภัย</button></a>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Car ID</th>
                        <th>Provider</th>
                        <th>Policy Number</th>
                        <th>Expiration Date</th>
                    </tr>
                    <?php foreach ($insurance as $ins): ?>
                    <tr>
                        <td><?= htmlspecialchars($ins['id']); ?></td>
                        <td><?= htmlspecialchars($ins['car_id']); ?></td>
                        <td><?= htmlspecialchars($ins['provider']); ?></td>
                        <td><?= htmlspecialchars($ins['policy_number']); ?></td>
                        <td><?= htmlspecialchars($ins['expiration_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>

        <!-- ข้อมูลบริการ -->
        <section>
            <h2>ข้อมูลบริการ</h2>
            <div class="btn-container">
                <a href="crud_services.php"><button>จัดการบริการ</button></a>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Car ID</th>
                        <th>Service Date</th>
                        <th>Description</th>
                        <th>Cost</th>
                    </tr>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= htmlspecialchars($service['id']); ?></td>
                        <td><?= htmlspecialchars($service['car_id']); ?></td>
                        <td><?= htmlspecialchars($service['service_date']); ?></td>
                        <td><?= htmlspecialchars($service['description']); ?></td>
                        <td><?= htmlspecialchars($service['cost']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>

        <!-- ข้อมูลเจ้าของรถ -->
        <section>
            <h2>ข้อมูลเจ้าของรถ</h2>
            <div class="btn-container">
                <a href="crud_owners.php"><button>จัดการเจ้าของรถ</button></a>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                    <?php foreach ($owners as $owner): ?>
                    <tr>
                        <td><?= htmlspecialchars($owner['id']); ?></td>
                        <td><?= htmlspecialchars($owner['name']); ?></td>
                        <td><?= htmlspecialchars($owner['email']); ?></td>
                        <td><?= htmlspecialchars($owner['phone']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
