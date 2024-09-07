<?php
// Include database configuration
require '../../config/db.php'; // Adjust the path as needed

// Check if connection was successful
if (!$conn) {
    die("Database connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $maker_name = $_POST['maker_name'];
    $full_name = $_POST['full_name'];
    $logo = $_POST['logo'];
    $countries = $_POST['countries'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO car_makers (maker_name, full_name, logo, countries) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $maker_name, $full_name, $logo, $countries);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the same page to clear POST data (optional)
        header("Location: add_car_maker.php");
        exit;
    } else {
        echo "Error inserting data: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car Maker</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<!-- Navigation Bar -->
<?php include('../includes/sidebar.php');?>

<main>


<div class="container mt-5">
    <h2>Add Car Maker</h2>
    <form method="post">
        <div class="form-group">
            <label for="maker_name">Maker Name:</label>
            <input type="text" class="form-control" id="maker_name" name="maker_name" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" class="form-control" id="full_name" name="full_name">
        </div>
        <div class="form-group">
            <label for="logo">Logo URL:</label>
            <input type="text" class="form-control" id="logo" name="logo">
        </div>
        <div class="form-group">
            <label for="countries">Countries:</label>
            <input type="text" class="form-control" id="countries" name="countries">
        </div>
        <button type="submit" class="btn btn-primary">Add Car Maker</button>
    </form>
</div>
</main>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
