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
    <title>Login - CarManage</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style adjustments for login page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }

        .register-link {
            margin-top: 10px;
            display: block;
            font-size: 14px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>เข้าสู่ระบบ</h2>

        <!-- Display error message if any -->
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" id="username" name="username" required>

            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">เข้าสู่ระบบ</button>
        </form>

        <!-- Link to the registration page -->
        <div class="register-link">
            <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
        </div>
    </div>
</body>
</html>
