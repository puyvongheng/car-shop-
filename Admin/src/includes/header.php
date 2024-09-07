<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation</title>
    <style>
        /* Internal CSS for the navigation bar */
        .navbar {
            background-color: #f8f9fa; /* Background color for the navbar */
        }

        .navbar-brand img {
            height: 40px; /* Set the height of the logo image */
        }

        .nav-link {
            color: #000; /* Default color for nav links */
            font-weight: normal; /* Default font weight */
            transition: color 0.3s; /* Smooth color transition */
        }

        .nav-link:hover {
            color: #0056b3; /* Color when hovered */
        }

        .nav-link.active {
            color: #0d6efd; /* Color for the active nav link */
            font-weight: bold; /* Make the active link bold */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/public/index.php">
                <img src="/images/logo.png" alt="Car Shop">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="../pages/dashboard.php">dashboard.php</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'register.php') ? 'active' : ''; ?>" href="/public/register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="/public/login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
