<?php
require '../../config/db.php'; // ភ្ជាបប់ទៅ databese
session_start();//ចាប់ផ្តើម
require '../../config/Check_login.php';//ឆែកមើលថាមានបាន login​​ អត់


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_approval_status'])) {
    $user_id = intval($_POST['user_id']);
    $new_approval_status = $_POST['approval_status'];

    // Validate approval status
    if ($new_approval_status != 'approved' && $new_approval_status != 'rejected' && $new_approval_status != 'pending') {
        echo "Invalid approval status.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE admin SET approval_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_approval_status, $user_id);

    if ($stmt->execute()) {
        echo "User approval status updated to $new_approval_status!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();

    // Redirect back to manage users page
    header("Location: manage_users.php");
    exit();
}
?>
<?php 
    include('../includes/sidebar.php');
?>