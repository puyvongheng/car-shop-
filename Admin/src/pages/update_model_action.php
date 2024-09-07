<?php
require '../../config/db.php'; // Connect to the database
session_start();
require '../../config/Check_login.php'; // Check if logged in

$id = $_POST['id'] ?? null; // Use POST method to get the ID
if (!$id) {
    die("Invalid request.");
}

// Fetch existing data
$query = "SELECT * FROM model WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$model = $result->fetch_assoc();
$stmt->close();

$name = $_POST['name'];
$full_name = $_POST['full_name'];
$car_maker_id = $_POST['car_maker_id'];
$year = $_POST['year'];
$price = $_POST['price'];
$description = $_POST['description'];
$car_types = $_POST['car_types'];
$color = $_POST['color'];
$fuel_type = $_POST['fuel_type'];

// Handle image upload
$img = $model['img']; // Preserve the old image if no new image is uploaded

if (!empty($_POST['img_url'])) {
    // If the image is provided via URL
    $img = $_POST['img_url'];
} elseif (isset($_FILES['img_file']) && $_FILES['img_file']['error'] == 0) {
    // If the image is uploaded via file
    $targetDir = "uploads/imagesmodel/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true); // Create directory if it does not exist
    }
    $targetFile = $targetDir . basename($_FILES["img_file"]["name"]);
    if (move_uploaded_file($_FILES["img_file"]["tmp_name"], $targetFile)) {
        // Remove old image if it was not an external URL
        if ($model['img'] && !filter_var($model['img'], FILTER_VALIDATE_URL)) {
            unlink($targetDir . basename($model['img']));
        }
        $img = basename($_FILES["img_file"]["name"]);
    } else {
        echo "Error uploading file.";
    }
}

// Update the model
$updateQuery = "UPDATE model SET name = ?, full_name = ?, id_car_makers = ?, year = ?, price = ?, description = ?, car_types = ?, color = ?, fuel_type = ?, img = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);
if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
}

if (!$stmt->bind_param(
    "ssiissssssi", 
    $name, 
    $full_name, 
    $car_maker_id, 
    $year, 
    $price, 
    $description, 
    $car_types, 
    $color, 
    $fuel_type, 
    $img, 
    $id
)) {
    die("Failed to bind parameters: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Failed to execute statement: " . $stmt->error);
}

header("Location: view_models_action.php");
exit;

// Close the connection
$conn->close();
?>
