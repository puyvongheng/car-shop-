<?php
// Include database configuration
require '../../config/db.php'; // Adjust the path as needed

// Check if connection was successful
if (!$conn) {
    die("Database connection failed.");
}

// Initialize pagination and search variables
$results_per_page = 10; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $results_per_page;
$search_term = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';

// Filter conditions
$filter_condition = '';
if ($filter === 'out_of_stock') {
    $filter_condition = "AND m.stock = 0";
} elseif ($filter === 'in_stock') {
    $filter_condition = "AND m.stock > 0";
}

// Query to count total records with search filter
$count_query = "SELECT COUNT(*) AS total FROM model m JOIN car_makers cm ON m.id_car_makers = cm.id WHERE m.name LIKE '%$search_term%' $filter_condition";
$count_result = $conn->query($count_query);
if ($count_result) {
    $total_records = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $results_per_page);
} else {
    die("Query failed: " . $conn->error);
}

// Query to select data from the model table with search and pagination
$query = "SELECT m.id, m.name, m.full_name, cm.maker_name, m.year, m.price, m.description, m.car_types, m.color, m.fuel_type, m.stock, m.img
          FROM model m
          JOIN car_makers cm ON m.id_car_makers = cm.id
          WHERE m.name LIKE '%$search_term%' $filter_condition
          LIMIT $start, $results_per_page";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle export request
if (isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=car_models.csv');

    $output = fopen('php://output', 'w');

    // Output the column headings
    fputcsv($output, array('ID', 'Name', 'Full Name', 'Car Maker', 'Year', 'Price', 'Description', 'Car Types', 'Color', 'Fuel Type', 'Stock', 'Image'));

    // Fetch and output the data with the search filter
    $export_query = "SELECT m.id, m.name, m.full_name, cm.maker_name, m.year, m.price, m.description, m.car_types, m.color, m.fuel_type, m.stock, m.img
                     FROM model m
                     JOIN car_makers cm ON m.id_car_makers = cm.id
                     WHERE m.name LIKE '%$search_term%' $filter_condition";
    $export_result = $conn->query($export_query);

    if (!$export_result) {
        die("Query failed: " . $conn->error);
    }

    while ($row = $export_result->fetch_assoc()) {
        fputcsv($output, array(
            $row['id'],
            $row['name'],
            $row['full_name'],
            $row['maker_name'],
            $row['year'],
            $row['price'],
            $row['description'],
            $row['car_types'],
            $row['color'],
            $row['fuel_type'],
            $row['stock'],
            $row['img']
        ));
    }

    fclose($output);
    exit;
}

// Initialize stock totals
$total_stock = 0;
$out_of_stock_total = 0;
$total_sold_stock = 0;

// Calculate total stock
$stock_query = "SELECT 
                    SUM(stock) AS total_stock, 
                    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS out_of_stock_total, 
                    SUM(stock) AS total_sold_stock 
                FROM model";
$stock_result = $conn->query($stock_query);

if ($stock_result) {
    $stock_data = $stock_result->fetch_assoc();
    $total_stock = $stock_data['total_stock'];
    $out_of_stock_total = $stock_data['out_of_stock_total'];
    $total_sold_stock = $stock_data['total_sold_stock'];
} else {
    die("Query failed: " . $conn->error);
}

// Count total number of models
$model_count_query = "SELECT COUNT(*) AS total FROM model";
$model_count_result = $conn->query($model_count_query);

if ($model_count_result) {
    $total_models = $model_count_result->fetch_assoc()['total'];
} else {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Models</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
    <link rel="stylesheet" href="../assets/css/Popups.css">
</head>
<body>
<!-- Navigation Bar -->
<?php include('../includes/sidebar.php'); ?>
<main>
    <!-- Search and Filter Form -->
    <form method="get" class="mb-3">
        <div class="form-row">
            <div class="col">
                <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            <div class="col">
                <select name="filter" class="form-control">
                    <option value="" <?php if ($filter === '') echo 'selected'; ?>>Show All Models</option>
                    <option value="out_of_stock" <?php if ($filter === 'out_of_stock') echo 'selected'; ?>>Out of Stock Models</option>
                    <option value="in_stock" <?php if ($filter === 'in_stock') echo 'selected'; ?>>Models in Stock</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>

    <!-- Export Form -->
    <form method="post" class="mb-3">
        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
        <button type="submit" name="export" class="btn btn-success">Export to CSV</button>
    </form>

    <!-- Statistics -->
    <div class="mb-4">
        <p>Total Stock: <?php echo number_format($total_stock); ?></p>
        <p>Total Models: <?php echo number_format($total_models); ?></p>
        <p>Out of Stock Models Total: <?php echo number_format($out_of_stock_total); ?></p>
        <p>Total Models in Stock: <?php echo number_format($total_models - $out_of_stock_total); ?></p>
        <p>Stock Sold: <?php echo number_format($total_sold_stock); ?></p>
    </div>

    <!-- Models Table -->
    <h2>View Models</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Full Name</th>
                <th>Car Maker</th>
                <th>Year</th>
                <th>Price</th>
                <th>Description</th>
                <th>Car Types</th>
                <th>Color</th>
                <th>Fuel Type</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['maker_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['car_types']); ?></td>
                    <td><?php echo htmlspecialchars($row['color']); ?></td>
                    <td><?php echo htmlspecialchars($row['fuel_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['stock']); ?>
                        <a href="auction_update_stock.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                    <td>
                        <?php if ($row['img']): ?>
                            <?php if (filter_var($row['img'], FILTER_VALIDATE_URL)): ?>
                                <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Image" style="width: 100%;">
                            <?php else: ?>
                                <img src="../uploads/imagesmodel/<?php echo htmlspecialchars($row['img']); ?>" alt="Image" style="width: 100%;">
                            <?php endif; ?>
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm openDeleteModalBtn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>">Delete</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modelModal-<?php echo $row['id']; ?>">View</button>
                        <!-- Modal -->
                        <div class="modal fade" id="modelModal-<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modelModalLabel-<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modelModalLabel-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID:</strong> <?php echo htmlspecialchars($row['id']); ?></p>
                                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($row['full_name']); ?></p>
                                        <p><strong>Car Maker:</strong> <?php echo htmlspecialchars($row['maker_name']); ?></p>
                                        <p><strong>Year:</strong> <?php echo htmlspecialchars($row['year']); ?></p>
                                        <p><strong>Price:</strong> <?php echo htmlspecialchars($row['price']); ?></p>
                                        <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                        <p><strong>Car Types:</strong> <?php echo htmlspecialchars($row['car_types']); ?></p>
                                        <p><strong>Color:</strong> <?php echo htmlspecialchars($row['color']); ?></p>
                                        <p><strong>Fuel Type:</strong> <?php echo htmlspecialchars($row['fuel_type']); ?></p>
                                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($row['stock']); ?></p>
                                        <?php if ($row['img']): ?>
                                            <?php if (filter_var($row['img'], FILTER_VALIDATE_URL)): ?>
                                                <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Image" style="width: 100%;">
                                            <?php else: ?>
                                                <img src="../uploads/imagesmodel/<?php echo htmlspecialchars($row['img']); ?>" alt="Image" style="width: 100%;">
                                            <?php endif; ?>
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_term); ?>&filter=<?php echo urlencode($filter); ?>">Previous</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>&filter=<?php echo urlencode($filter); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_term); ?>&filter=<?php echo urlencode($filter); ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</main>

<!-- Bootstrap and jQuery JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.querySelectorAll('.openDeleteModalBtn').forEach(button => {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.querySelector('.modal-body').innerText = 'Are you sure you want to delete ' + name + '?';
            deleteModal.querySelector('form').action = 'delete_model.php?id=' + id;
            $('#deleteModal').modal('show');
        });
    });
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Message will be dynamically inserted here -->
            </div>
            <div class="modal-footer">
                <form method="post">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
