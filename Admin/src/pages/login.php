<?php
require '../../config/db.php'; 
// ភ្ជាបប់ទៅ databese
session_start();//ចាប់ផ្តើម

if (!isset($_SESSION['admin_id'])) {
    // Redirect to index.php if not logged in as admin
   
}else{
    header('Location: dashboard.php');
    exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare and execute query to fetch the admin record
    $stmt = $conn->prepare("SELECT id, username, password_hash, status, approval_status FROM admin WHERE username = ?");
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $db_username, $password_hash, $status, $approval_status);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();

        // Check if the password is correct
        if (!password_verify($password, $password_hash)) {
            $error = "Invalid password.";
        } elseif ($approval_status !== 'approved') {
            // Check if the account is approved
            $error = "Account not approved. Status: $approval_status.";
        } elseif ($status !== 'active') {
            // Check if the account is active
            $error = "Account is inactive.";
        } else {
            // If all checks pass, log in the user
            $_SESSION['admin_id'] = $id;
            $_SESSION['username'] = $db_username;


            header("Location: dashboard.php"); // ប្រសិនបើត្រឹមត្រូវតាមលក្ខណ្ឌហើយ
            exit();
        }
    } else {
        $error = "Invalid username.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            position: relative;
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
            position: relative;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        /* Loader Styles */
        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #007bff;
            width: 40px;
            height: 40px;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -20px;
            margin-left: -20px;
            animation: spin 10s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Hide the loader by default */
        .loader-hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" action="" onsubmit="showLoader()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="register.php">Register</a>

        <!-- Loader -->
        <div id="loader" class="loader loader-hidden"></div>
    </div>

    <script>
        function showLoader() {
            document.getElementById('loader').classList.remove('loader-hidden');
        }
    </script>
</body>
</html>