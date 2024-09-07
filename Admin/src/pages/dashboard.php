<?php
require '../../config/db.php'; // Connect to the database
session_start();
require '../../config/Check_login.php'; // Check if logged in

// Fetch the logged-in admin's details
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username, status, approval_status FROM admin WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($username, $status, $approval_status);
$stmt->fetch();
$stmt->close();

// Fetch the total counts
$totalCounts = [
    'admins' => $conn->query("SELECT COUNT(*) AS total FROM admin")->fetch_assoc()['total'],
    'users' => $conn->query("SELECT COUNT(*) AS total FROM user")->fetch_assoc()['total'],
    'models' => $conn->query("SELECT COUNT(*) AS total FROM model")->fetch_assoc()['total'],
    'carMakers' => $conn->query("SELECT COUNT(*) AS total FROM car_makers")->fetch_assoc()['total'],
    'orderItems' => $conn->query("SELECT COUNT(*) AS total FROM order_items")->fetch_assoc()['total'],
    'orders' => $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'],
    'totalPrice' => $conn->query("SELECT SUM(total_price) AS total_price FROM orders")->fetch_assoc()['total_price'],
    'totalModelPrice' => $conn->query("SELECT SUM(price) AS total_price_model FROM model")->fetch_assoc()['total_price_model']
];

// Check for new orders today
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) AS new_count FROM orders WHERE DATE(order_date) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$newOrdersCount = $stmt->get_result()->fetch_assoc()['new_count'];

// Calculate total orders sold this month
$firstDayOfMonth = date('Y-m-01');
$lastDayOfMonth = date('Y-m-t');
$stmt = $conn->prepare("SELECT COUNT(*) AS monthly_sold_count FROM orders WHERE order_date BETWEEN ? AND ?");
$stmt->bind_param("ss", $firstDayOfMonth, $lastDayOfMonth);
$stmt->execute();
$monthlySoldCount = $stmt->get_result()->fetch_assoc()['monthly_sold_count'];

// Calculate total revenue for this month
$stmt = $conn->prepare("SELECT SUM(total_price) AS monthly_revenue FROM orders WHERE order_date BETWEEN ? AND ?");
$stmt->bind_param("ss", $firstDayOfMonth, $lastDayOfMonth);
$stmt->execute();
$monthlyRevenue = $stmt->get_result()->fetch_assoc()['monthly_revenue'];

// Query to get the totals for each status
$statusQuery = "SELECT status, COUNT(*) AS total FROM order_items GROUP BY status";
$statusResult = $conn->query($statusQuery);

// Initialize an array to hold the counts
$statusTotals = array();
while ($row = $statusResult->fetch_assoc()) {
    $statusTotals[$row['status']] = $row['total'];
}

// Handle missing statuses
$statuses = ['Order Placement', 'Picking', 'Sorting', 'Packing', 'Shipping', 'Complete'];
foreach ($statuses as $status) {
    if (!isset($statusTotals[$status])) {
        $statusTotals[$status] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/page.css">
    <style>
        <?php include('../assets/css/dashboard.css'); ?>
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
<?php include '../../header.php'; ?>

<main>
    <div class="container dashboard-container">
        <h2 class="my-4"><span style="color: black;">Welcome,</span> <?php echo htmlspecialchars($username); ?>!</h2>
    </div>

    <div class="container-fluid">
        <div class="row mt-4">
            <!-- Box for Admin and User counts -->
            <div class="col-md-3">
                <a href="../pages/manage_users.php" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">Admin</h5>
                            <hr>


                          

                            <div style="display: flex;" class="box">
                            <div style="width: 50%;" class="box-tex">
                            <h1><?php echo $totalCounts['admins']; ?></h1>
                            </div>
                            <div style="width: 50%; "  class="box-icon">
                            <h1>    <img src="../icon/programmer.png" style="width: 50px;" alt=""></h1>
                            </div>
                            </div>




                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">costomer</h5>
                            <hr>
                           


                            

                            <div style="display: flex;" class="box">
                            <div style="width: 50%;" class="box-tex">
                            <h1><?php echo $totalCounts['users']; ?></h1>
                            </div>
                            <div style="width: 50%; "  class="box-icon">
                            <h1>    <img src="../icon/group.png" style="width: 50px;" alt=""></h1>
                            </div>
                            </div>







                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="../pages/view_car_makers_action.php" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">Car Makers</h5>
                            <hr>
                            <h1><?php echo $totalCounts['carMakers']; ?></h1>
                        </div>
                    </div>
                </a>
            </div>

            
            <div class="col-md-3" >
                <a href="../pages/view_models_action.php" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">Models</h5>
                            <hr>



                            
    

                            <div style="display: flex;" class="box">
                            <div style="width: 50%;" class="box-tex">
                            <h1><?php echo $totalCounts['models']; ?></h1>
                            </div>
                            <div style="width: 50%; "  class="box-icon">
                            <h1><i class="fa-solid fa-box"></i></h1>
                            </div>
                            </div>





                        </div>
                    </div>
                </a>
            </div>


                <?php 
                require '../../config/db.php'; // Connect to the database

                // Fetch the total models count
                $totalModelsResult = $conn->query("SELECT COUNT(*) AS total FROM model");
                $totalModels = $totalModelsResult->fetch_assoc()['total'];

                // Fetch the total stock count
                $totalStockResult = $conn->query("SELECT SUM(stock) AS total_stock FROM model");
                $totalStockRow = $totalStockResult->fetch_assoc();
                $total_stock = $totalStockRow['total_stock'];

                // Fetch out of stock models count
                $outOfStockResult = $conn->query("SELECT COUNT(*) AS out_of_stock_total FROM model WHERE stock = 0");
                $outOfStockRow = $outOfStockResult->fetch_assoc();
                $out_of_stock_total = $outOfStockRow['out_of_stock_total'];

                // Calculate models in stock
                $modelsInStock = $totalModels - $out_of_stock_total;
                ?>



            <div class="col-md-3">
                <a href="../pages/view_models_action.php" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">sold</h5>
                            <hr>

                            
                            <div style="display: flex;" class="box">
                            <div style="width: 50%;" class="box-tex">
                            <h1> <?php echo number_format($total_stock); ?></h1>
                            </div>
                            <div style="width: 50%; "  class="box-icon">
                            <h1><i class="fa-solid fa-box"></i></h1>
                            </div>
                            </div>


                        </div>
                    </div>
                </a>
            </div>

            
            <div class="col-md-3">
                <a href="http://www/Admin/src/pages/tesing.php?search=&filter=out_of_stock" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">Out of Stock Models:</h5>
                            <hr>





                            <div style="display: flex;" class="box">
                            <div style="width: 50%;" class="box-tex">
                            <h1>  <?php echo number_format($out_of_stock_total); ?></h1>
                            </div>
                            <div style="width: 50%; "  class="box-icon">
                            <h1>    <img src="../icon/out-of-stock.png" style="width: 50px;" alt=""></h1>
                        
                            </div>
                            </div>




                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="http://www/Admin/src/pages/tesing.php?search=&filter=in_stock" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">Total Models in Stock: </h5>
               
                            <hr>

                            <div style="display: flex;" class="box">
                            <div style="width: 50%;" class="box-tex">
                            <h1>  <?php echo number_format($totalCounts['models'] - $out_of_stock_total); ?>  </h1>
                            </div>
                            <div style="width: 50%; "  class="box-icon">
                            <h1>    <img src="../icon/in-stock.png" style="width: 50px;" alt=""></h1>
                            </div>
                            </div>

                      


                        </div>
                    </div>
                </a>
            </div>





        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <a href="ordertotal.php" class="link">
                    <div class="card mb-3 info-box">
                        <div class="card-body">
                            <h5 class="card-title">Orders</h5>
                            <hr>
                            <h1 style="display: flex;">
                                <?php echo $totalCounts['orders']; ?>
                                <?php if ($newOrdersCount > 0): ?>
                                    <div class="notification" style="font-size: small; margin-left: 20px; margin-top: 10px;">
                                        <strong>New Orders Alert!</strong> There are <?php echo $newOrdersCount; ?> new order(s) today.
                                    </div>
                                <?php endif; ?>
                            </h1>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <div class="card mb-3 info-box">
                    <div class="card-body">
                        <h5 class="card-title">Order Items</h5>
                        <hr>
                        <h1><?php echo $totalCounts['orderItems']; ?></h1>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-3 info-box">
                    <div class="card-body">
                        <h5 class="card-title">Order Sold This Month</h5>
                        <hr>
                        <h1><?php echo $monthlySoldCount; ?></h1>
                    </div>
                </div>
            </div>


            <?php
// Fetch the count of unique models sold this month
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT order_items.model_id) AS model_sold_count
    FROM order_items
    JOIN orders ON order_items.order_id = orders.id
    WHERE orders.order_date BETWEEN ? AND ?
");
$stmt->bind_param("ss", $firstDayOfMonth, $lastDayOfMonth);
$stmt->execute();
$modelSoldCount = $stmt->get_result()->fetch_assoc()['model_sold_count'];
$stmt->close();
?>

    <div class="col-md-3">
    <div class="card mb-3 info-box">
        <div class="card-body">
            <h5 class="card-title">Model Sold This Month</h5>
            <hr>
            <h1><?php echo $modelSoldCount; ?></h1>
        </div>
    </div>
</div>



<?php
// Fetch the total count of cars sold this month
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_cars_sold
    FROM order_items
    JOIN orders ON order_items.order_id = orders.id
    WHERE orders.order_date BETWEEN ? AND ?
");
$stmt->bind_param("ss", $firstDayOfMonth, $lastDayOfMonth);
$stmt->execute();
$totalCarsSold = $stmt->get_result()->fetch_assoc()['total_cars_sold'];
$stmt->close();
?>
<div class="col-md-3">
    <div class="card mb-3 info-box">
        <div class="card-body">
            <h5 class="card-title">Total Cars Sold This Month</h5>
            <hr>
            <h1><?php echo $totalCarsSold; ?></h1>
        </div>
    </div>
</div>




            <div class="col-md-3">
                <div class="card mb-3 info-box">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Revenue</h5>
                        <hr>
                        <h2>
                            <?php echo number_format($monthlyRevenue, 2); ?> 
                            <i style="font-size: 1.8rem;" class="fa-solid fa-dollar-sign"></i>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h1 class="my-4">Order Items Status Totals</h1>
    <table class="table table-striped">
        <thead class="thead-dark" >
            <tr >
                <th>Status</th>
                <th>Total</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statuses as $status): ?>
                <tr>
                    <td><?php echo htmlspecialchars($status); ?></td>
                    <td><?php echo $statusTotals[$status]; ?></td>
                    <td>
                        <a href="ordertotal.php?status=<?php echo urlencode($status); ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
