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
    <title>Dashboard - CarManage</title>
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
                <a href="?logout=true" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard fade-in">
            <!-- Stats Cards -->
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($cars) ?></h3>
                    <p>รถยนต์ทั้งหมด</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($maintenance_records) ?></h3>
                    <p>รายการซ่อมบำรุง</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($insurance) ?></h3>
                    <p>ประกันภัย</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($owners) ?></h3>
                    <p>เจ้าของรถ</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h2><i class="fas fa-bolt"></i> จัดการข้อมูล</h2>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="crud_car.php" class="btn btn-primary">
                        <i class="fas fa-car"></i> จัดการรถยนต์
                    </a>
                    <a href="crud_maintenance_records.php" class="btn btn-primary">
                        <i class="fas fa-tools"></i> จัดการซ่อมบำรุง
                    </a>
                    <a href="crud_insurance.php" class="btn btn-primary">
                        <i class="fas fa-shield-alt"></i> จัดการประกันภัย
                    </a>
                    <a href="crud_services.php" class="btn btn-primary">
                        <i class="fas fa-concierge-bell"></i> จัดการบริการ
                    </a>
                    <a href="crud_owners.php" class="btn btn-primary">
                        <i class="fas fa-users"></i> จัดการเจ้าของรถ
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Cars -->
        <div class="card mb-4">
            <div class="card-header">
                <h2><i class="fas fa-car"></i> รถยนต์ล่าสุด</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>ยี่ห้อ</th>
                            <th>รุ่น</th>
                            <th>ปี</th>
                            <th>สี</th>
                        </tr>
                        <?php foreach (array_slice($cars, 0, 5) as $car): ?>
                        <tr>
                            <td><?= htmlspecialchars($car['id']) ?></td>
                            <td><?= htmlspecialchars($car['make']) ?></td>
                            <td><?= htmlspecialchars($car['model']) ?></td>
                            <td><?= htmlspecialchars($car['year']) ?></td>
                            <td><?= htmlspecialchars($car['color']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Maintenance -->
        <div class="card mb-4">
            <div class="card-header">
                <h2><i class="fas fa-tools"></i> การซ่อมบำรุงล่าสุด</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>รถยนต์</th>
                            <th>วันที่</th>
                            <th>รายละเอียด</th>
                        </tr>
                        <?php foreach (array_slice($maintenance_records, 0, 5) as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['id']) ?></td>
                            <td><?= htmlspecialchars($record['car_id']) ?></td>
                            <td><?= htmlspecialchars($record['maintenance_date']) ?></td>
                            <td><?= htmlspecialchars($record['details']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .quick-actions .btn {
            width: 100%;
        }
    </style>
</body>
</html>
