<?php
session_start(); // Start the session

// Include database connection
include 'db.php';

// Initialize error variables
$error = '';
$success = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the entered details
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน!";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "ชื่อผู้ใช้นี้มีอยู่แล้ว!";
        } else {
            // Hash the password using password_hash() function
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the query to insert the new user
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, full_name, phone, address, role)
                                    VALUES (:username, :password_hash, :email, :full_name, :phone, :address, :role)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':role', $role);
            
            $role = 'user';  // Default role is 'user'

            // Execute the query to insert the user
            if ($stmt->execute()) {
                $success = "บัญชีของคุณถูกสร้างแล้ว! คุณสามารถเข้าสู่ระบบได้.";
            } else {
                $error = "เกิดข้อผิดพลาดในการสร้างบัญชี!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - CarManage</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <h2><i class="fas fa-user-plus"></i> สมัครสมาชิก</h2>
                <p>กรอกข้อมูลเพื่อสร้างบัญชีใหม่</p>
            </div>

            <?php if ($success): ?>
                <div class="message success mb-3">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error mb-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-container">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> ชื่อผู้ใช้
                    </label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> รหัสผ่าน
                    </label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> ยืนยันรหัสผ่าน
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> อีเมล
                    </label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-user-circle"></i> ชื่อเต็ม
                    </label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i> เบอร์โทร
                    </label>
                    <input type="text" id="phone" name="phone" class="form-control">
                </div>

                <div class="form-group">
                    <label for="address">
                        <i class="fas fa-home"></i> ที่อยู่
                    </label>
                    <textarea id="address" name="address" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> สมัครสมาชิก
                </button>
            </form>

            <div class="auth-links mt-3">
                <a href="login.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> กลับหน้าล็อกอิน
                </a>
            </div>
        </div>
    </div>
</body>
</html>
