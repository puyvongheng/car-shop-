<?php
require '../../config/db.php'; // ភ្ជាបប់ទៅ databese
session_start();//ចាប់ផ្តើម
require '../../config/Check_login.php';//ឆែកមើលថាមានបាន login​​ អត់

$query = "SELECT id, username, email FROM admin WHERE approval_status = 'pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Registrations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/page.css">
   <style>
     
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .btn-approve {
            background-color: #28a745;
            color: #ffffff;
        }
        .btn-approve:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<?php  include('../includes/sidebar.php');?>
<main>


    <div class="container">
        <h2 class="my-4">Pending Registrations</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['username']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['email']); ?></h6>

                        <form method="post" action="approve_admin.php" class="mt-3">
                            <input type="hidden" name="admin_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="approve" class="btn btn-approve">Approve</button>
                        </form>
                        
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                No pending registrations.
            </div>
        <?php endif; ?>
    </div>



    
    </main>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
