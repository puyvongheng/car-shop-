<?php
require '../../config/db.php'; // Adjust the path as needed

// Check if connection was successful
if (!$conn) {
    die("Database connection failed.");
}


$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid request.");
}

// Delete car maker
$query = "DELETE FROM car_makers WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: view_car_makers_action.php");
    exit;
} else {
    echo "Error deleting car maker: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
