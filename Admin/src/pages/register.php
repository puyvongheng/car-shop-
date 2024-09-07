<?php
require '../../config/db.php';  // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);

    // Validate inputs
    if (!empty($username) && !empty($password) && !empty($confirm_password) && !empty($email)) {
        // Check if the password length is at least 14 characters
        if (strlen($password) < 14) {
            $error = "Password must be at least 14 characters long.";
        } elseif ($password !== $confirm_password) {
            // Check if passwords match
            $error = "Passwords do not match.";
        } else {
            // Check if the username or email already exists
            $stmt = $conn->prepare("SELECT username, email FROM admin WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($existingUsername, $existingEmail);
                $stmt->fetch();
                if ($existingUsername === $username) {
                    $error = "Username already taken.";
                }
                if ($existingEmail === $email) {
                    $error = "Email already registered.";
                }
            } else {
                // Insert the new user into the database
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO admin (username, password_hash, email, status, approval_status) VALUES (?, ?, ?, 'inactive', 'pending')");
                $stmt->bind_param("sss", $username, $passwordHash, $email);

                if ($stmt->execute()) {
                    $success = "Registration successful! Awaiting approval.";
                } else {
                    error_log("Error: " . $stmt->error, 3, "errors.log");
                    $error = "Error: An error occurred. Please try again later.";
                }
            }
            $stmt->close();
        }
    } else {
        $error = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .login-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .login-container .success {
            color: #28a745;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Registration</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit" name="register">Register</button>
        </form>
        <a href="login.php">Login</a>
    </div>
</body>
</html>
