<?php
session_start(); // Start session to access admin login status
require '../../config/db.php'; // Include database configuration

// Check if the user is logged in and is an admin
// Make sure to add your admin check logic here

// Fetch form data
$order_item_id = isset($_POST['order_item_id']) ? intval($_POST['order_item_id']) : 0;
$status_id = isset($_POST['status_id']) ? intval($_POST['status_id']) : 0;

// Ensure valid input
if ($order_item_id <= 0 || $status_id <= 0) {
    echo "Invalid input.";
    exit();
}

// Update the status of the order item
$update_query = "UPDATE order_items SET status_id = ? WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("ii", $status_id, $order_item_id);
$stmt->execute();

// Optional: Record the status change history
$history_query = "INSERT INTO order_status_history (order_id, status_id, admin_id) 
                  SELECT o.id, ?, ? FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  WHERE oi.id = ?";
$stmt = $conn->prepare($history_query);
$admin_id = 1; // Replace with the actual admin ID
$stmt->bind_param("iii", $status_id, $admin_id, $order_item_id);
$stmt->execute();

// Redirect or show a success message
header('Location: order_itemstotal.php?order_id=' . $order_id);
exit();
?>
