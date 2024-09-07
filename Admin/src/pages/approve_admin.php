<?php
require '../../config/db.php'; 

session_start();

require '../../config/Check_login.php';//ឆែកមើលថាមានបាន login​​ អត់

// Ensure the current user is authorized to approve other admins
// You might want to implement additional checks based on your application logic

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'])) {
    $adminId = intval($_POST['admin_id']);
    
    // Check if the new admin exists and is pending approval
    $stmt = $conn->prepare("SELECT * FROM admin WHERE id = ? AND approval_status = 'pending'");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE admin SET approval_status = 'approved', status = 'active' WHERE id = ?");
        $updateStmt->bind_param("i", $adminId);

        if ($updateStmt->execute()) {
            header("Location: pending_registrations.php");
            echo "Admin approved successfully!";
        } else {
            echo "Error: " . $updateStmt->error;
        }
        $updateStmt->close();
    } else {
        echo "No pending admin found with the given ID.";
    }
    $stmt->close();
}
?>
<?php 
    include('../includes/sidebar.php');
?>



<form method="post" action="">
    <input type="hidden" name="admin_id" value="1"> <!-- Example ID, replace with dynamic value -->
    <button type="submit" name="approve">Approve Admin</button>
</form>
