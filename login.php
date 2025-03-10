<?php
session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to the main page if already logged in
    exit();
}

// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the entered username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the query to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        // Store user details in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to the main page
        header("Location: dashboard.php");
        exit();
    } else {
        // Invalid login credentials
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - CarManage</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <h2><i class="fas fa-car"></i> CarManage</h2>
                <p>ระบบจัดการข้อมูลรถยนต์</p>
            </div>

            <?php if (isset($error)): ?>
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

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                </button>
            </form>

            <div class="auth-links mt-3">
                <p>ยังไม่มีบัญชี? <a href="register.php" class="text-primary">สมัครสมาชิก</a></p>
            </div>
        </div>
    </div>
</body>
</html>
