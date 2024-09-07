<?php
require '../../config/db.php'; // ភ្ជាបប់ទៅ databese
session_start();//ចាប់ផ្តើម
require '../../config/Check_login.php';//ឆែកមើលថាមានបាន login​​ អត់

$admin_id = $_SESSION['admin_id'];
$query = "SELECT username FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_name);
$stmt->fetch();
$stmt->close();

function updateUser(mysqli $conn, int $user_id, ?string $status, ?string $approval_status): string {
    $query = "UPDATE admin SET status = ?, approval_status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return "<div class='alert alert-danger'>Prepare statement failed: " . $conn->error . "</div>";
    }

    $stmt->bind_param("ssi", $status, $approval_status, $user_id);

    if ($stmt->execute()) {
        return "<div class='alert alert-success'>User updated: Status is $status and Approval Status is $approval_status!</div>";
    } else {
        return "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_change'])) {
    $user_id = intval($_POST['user_id']);
    $status = $_POST['status'] ?? null;
    $approval_status = $_POST['approval_status'] ?? null;

    if ($status && !in_array($status, ['active', 'inactive'])) {
        echo "<div class='alert alert-danger'>Invalid status.</div>";
        exit();
    }

    if ($approval_status && !in_array($approval_status, ['approved', 'rejected', 'pending'])) {
        echo "<div class='alert alert-danger'>Invalid approval status.</div>";
        exit();
    }

    $message = updateUser($conn, $user_id, $status, $approval_status);

    header("refresh:2;url=manage_users.php");
    exit();
}

$query = "SELECT id, username, email, status, approval_status FROM admin";
$result = $conn->query($query);

if (!$result) {
    die("<div class='alert alert-danger'>Query failed: " . $conn->error . "</div>");
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/page.css">
    <style>
    
        .manage-users-container {
            margin: 20px;
        }
        table {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            text-align: center;
        }
        .btn-change {
            background-color: #007bff;
            color: #fff;
        }
        .btn-change:hover {
            background-color: #0056b3;
        }
        .status-active {
            color: green;
        }
        .status-inactive {
            color: red;
        }
        .approval-pending {
            background-color: yellow;
        }
        .approval-approved {
            background-color: green;
            color: white;
        }
        .approval-rejected {
            background-color: red;
            color: white;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
  
    <?php include('../includes/sidebar.php');?>
    <!-- Navigation Bar -->
<main>
 <!-- Page Content -->
 <div class="container manage-users-container">
        <h1 class="my-4">Manage Users</h1>

        <?php if (isset($message)) echo $message; ?>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Approval Status</th>
                        <th>Actions</th>
                     
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="<?php echo ($row['status'] == 'active') ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </td>
                            <td class="<?php echo ($row['approval_status'] == 'pending') 
                                    ? 'approval-pending' 
                                    : (($row['approval_status'] == 'approved') 
                                       ? 'approval-approved' 
                                       : 'approval-rejected'); ?>">
                                <?php echo htmlspecialchars($row['approval_status']); ?>
                            </td>
                            <td>
                                <button class="btn btn-change btn-sm" data-toggle="modal" data-target="#changeModal" data-user-id="<?php echo $row['id']; ?>" data-current-status="<?php echo htmlspecialchars($row['status']); ?>" data-current-approval-status="<?php echo htmlspecialchars($row['approval_status']); ?>">Change</button>
                            </td>
                         
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No users found.</div>
        <?php endif; ?>
    </div>



    <!-- Change Modal -->
    <div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="changeModalLabel" aria-hidden="true">
        
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeModalLabel">Change User Status and Approval Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="changeUserId">
                        <div class="form-group">
                            <label for="statusSelect">Select Status:</label>
                            <select name="status" id="statusSelect" class="form-control">
                                <option value="">-- No Change --</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="approvalSelect">Select Approval Status:</label>
                            <select name="approval_status" id="approvalSelect" class="form-control">
                                <option value="">-- No Change --</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="confirm_change" class="btn btn-primary">Confirm Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>
   
<?php 
    include('../includes/footer.php');
?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#changeModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var userId = button.data('user-id');
            var currentStatus = button.data('current-status');
            var currentApprovalStatus = button.data('current-approval-status');

            var modal = $(this);
            modal.find('#changeUserId').val(userId);
            modal.find('#statusSelect').val(currentStatus);
            modal.find('#approvalSelect').val(currentApprovalStatus);
        });

       
    </script>
</body>
</html>
