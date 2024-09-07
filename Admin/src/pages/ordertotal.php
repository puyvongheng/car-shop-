<?php
session_start(); // Start session to access admin login status
require '../../config/db.php';

// Check if database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search status from the form if available
$search_status = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch all orders with optional status filter
$orders_query = "
    SELECT o.id, o.order_date, o.total_price, u.first_name, u.last_name
    FROM orders o
    JOIN user u ON o.user_id = u.id
    WHERE EXISTS (
        SELECT 1
        FROM order_items oi
        WHERE oi.order_id = o.id
        AND (? = '' OR oi.status = ?)
    )
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($orders_query);
$stmt->bind_param("ss", $search_status, $search_status);
$stmt->execute();
$result = $stmt->get_result();

// Check for new orders today
$today = date('Y-m-d');
$new_orders_query = "SELECT id FROM orders WHERE DATE(order_date) = ?";
$stmt = $conn->prepare($new_orders_query);
$stmt->bind_param("s", $today);
$stmt->execute();
$new_orders_result = $stmt->get_result();
$new_orders_today = [];
while ($row = $new_orders_result->fetch_assoc()) {
    $new_orders_today[] = $row['id'];
}

// Fetch order items for each order
$order_items_query = "SELECT oi.order_id, oi.model_id, oi.quantity, oi.price, oi.status, m.name AS model_name
                      FROM order_items oi
                      JOIN model m ON oi.model_id = m.id";
$order_items_result = $conn->query($order_items_query);
$order_items = [];
while ($item = $order_items_result->fetch_assoc()) {
    $order_items[$item['order_id']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
    <style>
        .order-summary {
            margin-bottom: 30px;
        }
        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #d4edda;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
        }
        .item-status {
            font-weight: bold;
        }
        .new-order {
            background-color: #fff3cd;
        }
        .status-order-placement {
            background-color: #d1ecf1;
        }
        .status-picking {
            background-color: #cce5ff;
        }
        .status-sorting {
            background-color: #e2e3e5;
        }
        .status-packing {
            background-color: #d4edda;
        }
        .status-shipping {
            background-color: #fff3cd;
        }
        .status-complete {
            background-color: #d4edda;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<?php include('../includes/sidebar.php');?>

<main>
    <h2>All Orders</h2>

    <?php if (count($new_orders_today) > 0): ?>
        <div class="notification">
            <strong>New Orders Alert!</strong> There are <?php echo count($new_orders_today); ?> new order(s) today.
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="GET" class="mb-4">
        <div class="form-group">
            <label for="status">Filter by Status:</label>
            <select id="status" name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="Order Placement" <?php if ($search_status === 'Order Placement') echo 'selected'; ?>>Order Placement</option>
                <option value="Picking" <?php if ($search_status === 'Picking') echo 'selected'; ?>>Picking</option>
                <option value="Sorting" <?php if ($search_status === 'Sorting') echo 'selected'; ?>>Sorting</option>
                <option value="Packing" <?php if ($search_status === 'Packing') echo 'selected'; ?>>Packing</option>
                <option value="Shipping" <?php if ($search_status === 'Shipping') echo 'selected'; ?>>Shipping</option>
                <option value="Complete" <?php if ($search_status === 'Complete') echo 'selected'; ?>>Complete</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Search <i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Order Date</th>
                    <th>Total Price</th>
                    <th>Details</th>
                    <th>Statuses</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr class="<?php echo in_array($order['id'], $new_orders_today) ? 'new-order' : ''; ?>">
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td><a href="order_itemstotal.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-info btn-sm">View<i class="fa-sharp fa-solid fa-eye"></i> Items</a></td>
                        <td>
                            <?php
                            // Display statuses for each item in the order with color coding
                            if (isset($order_items[$order['id']])) {
                                $statuses = array_map(function($item) {
                                    $status_class = 'status-' . strtolower(str_replace(' ', '-', $item['status']));
                                    return '<span class="' . $status_class . '">' . htmlspecialchars($item['model_name']) . ': ' . htmlspecialchars($item['status']) . '</span>';
                                }, $order_items[$order['id']]);
                                echo implode('<br>', $statuses);
                            } else {
                                echo 'No items';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
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
