<?php
require '../../config/db.php'; // Adjust the path as needed

// Check if connection was successful
if (!$conn) {
    die("Database connection failed.");
}


$id = $_GET['id'];
if (!$id) {
    die("Invalid request.");
}

// Delete the model
$query = "DELETE FROM model WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: view_models_action.php");
    exit;
} else {
    echo "Error deleting record: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
