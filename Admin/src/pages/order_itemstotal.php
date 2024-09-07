<?php
session_start(); // Start session to access admin login status
require '../../config/db.php'; // Include database configuration

// Check if the user is logged in and is an admin
// Add your admin check code here

// Get the order_id from the query string
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo "Invalid Order ID.";
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details including user address and email
$order_query = "SELECT o.id, o.order_date, o.total_price, u.first_name, u.last_name, u.email, u.address
                FROM orders o
                JOIN user u ON o.user_id = u.id
                WHERE o.id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch order items
$order_items_query = "SELECT oi.*, m.name, m.img FROM order_items oi
                      JOIN model m ON oi.model_id = m.id
                      WHERE oi.order_id = ?";
$stmt = $conn->prepare($order_items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items_result = $stmt->get_result();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status']) && isset($_POST['item_id'])) {
        $status = $_POST['status'];
        $item_id = intval($_POST['item_id']);
        
        $update_query = "UPDATE order_items SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $status, $item_id);
        $stmt->execute();
        $stmt->close();
        
        // Refresh the page to show updated status
        header("Location: order_itemstotal.php?order_id=" . $order_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
    <style>
        .order-item img {
            max-width: 100px;
            height: auto;
        }
        .order-summary {
            background: #f8f9fa; /* Light grey background */
            border-radius: 8px; /* Rounded corners */
            padding: 20px; /* Padding inside the box */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Subtle shadow */
            margin: 20px 0; /* Margin around the box */
        }
        .order-summary h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333; /* Darker text for heading */
        }
        .order-summary p {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #555; /* Slightly lighter text color */
        }
        .order-summary a {
            display: inline-block;
            margin-top: 10px;
            font-size: 1rem;
            color: #007bff; /* Bootstrap primary color */
            text-decoration: none;
        }
        .order-summary a:hover {
            text-decoration: underline; /* Underline on hover */
        }

    </style>
</head>
<body>
<!-- Navigation Bar -->
<?php include('../includes/sidebar.php');?>

<main>

        <h2>Order Details</h2>
        <div class="order-summary" id="printable">
            <h3>Order ID: <?php echo htmlspecialchars($order['id']); ?></h3>
            <p>Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
            <p>Total Price: $<?php echo number_format($order['total_price'], 2); ?></p>
            <p>User: <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
            <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
            <p>Address: <?php echo htmlspecialchars($order['address']); ?></p>

            <a href="#">detail user</a>



        </div>

        <button class="btn btn-primary btn-print" onclick="printOrder()">Print</button>

        <script>
    function printOrder() {
        var printable = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printable;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

        <h4>Order Items</h4>

        
        <?php if ($order_items_result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td></td>
                        <th>Image</th>
                        <th>Model Name</th>
                        <th>Quantity</th>
                        <th>Price Each</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $order_items_result->fetch_assoc()): ?>
                        <tr>
                            <td>    <a href="http://www/user/details.php?model_id=<?php echo $item['model_id']; ?>" >មើលជាuser</a> 
                            <br>
                            <hr>
                        
                            <a href="  model.php?search=<?php echo htmlspecialchars($item['model_id']); ?>" class="car-link">id </a> 

                     
                          
                       
                        
                        </td>
                            <td class="order-item">

                        

                            <a href="edit_model.php?id=<?php echo htmlspecialchars($item['model_id']); ?>" class="car-link">

                                <?php if ($item['img']): ?>
                                    <?php if (filter_var($item['img'], FILTER_VALIDATE_URL)): ?>
                                        <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100px;">
                                    <?php else: ?>
                                        <img src="../uploads/imagesmodel/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php endif; ?>
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </a>

                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['status']); ?></td>
                            <td>

                       
                                <form action="" method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">

                                    <select name="status" class="form-control">
                                        <option value="Order Placement" <?php if ($item['status'] == 'Order Placement') echo 'selected'; ?>>Order Placement</option>
                                        <option value="Picking" <?php if ($item['status'] == 'Picking') echo 'selected'; ?>>Picking</option>
                                        <option value="Sorting" <?php if ($item['status'] == 'Sorting') echo 'selected'; ?>>Sorting</option>
                                        <option value="Packing" <?php if ($item['status'] == 'Packing') echo 'selected'; ?>>Packing</option>
                                        <option value="Shipping" <?php if ($item['status'] == 'Shipping') echo 'selected'; ?>>Shipping</option>
                                        <option value="Complete" <?php if ($item['status'] == 'Complete') echo 'selected'; ?>>Complete</option>
                                    </select>

                        
                                    <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                </form>





                                
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No items found for this order.</p>
        <?php endif; ?>
   
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
