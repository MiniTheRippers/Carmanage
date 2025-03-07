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
    <style>
        /* Style adjustments for registration page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
            font-size: 16px;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 100px;
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

        .error,
        .success {
            margin: 20px 0;
            padding: 10px;
            color: white;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }

        .error {
            background-color: #f44336;
        }

        .success {
            background-color: #4CAF50;
        }

        /* Additional responsive styling */
        @media (max-width: 480px) {
            .register-container {
                padding: 20px;
                width: 90%;
            }

            h2 {
                font-size: 20px;
            }

            button {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>สมัครสมาชิก</h2>

        <!-- Show success message -->
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Show error message -->
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Registration form -->
        <form method="POST">
            <label for="username">ชื่อผู้ใช้</label>
            <input type="text" id="username" name="username" required>

            <label for="password">รหัสผ่าน</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">ยืนยันรหัสผ่าน</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <label for="email">อีเมล</label>
            <input type="email" id="email" name="email" required>

            <label for="full_name">ชื่อเต็ม</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="phone">เบอร์โทร</label>
            <input type="text" id="phone" name="phone">

            <label for="address">ที่อยู่</label>
            <textarea id="address" name="address"></textarea>

            <button type="submit">สมัครสมาชิก</button>
        </form>

        <!-- Back to Login button -->
        <div class="back-to-login-container" style="margin-top: 20px;">
            <a href="login.php"><button>กลับหน้าล็อกอิน</button></a>
        </div>
    </div>
</body>
</html>
