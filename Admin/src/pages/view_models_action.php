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

// Query to count total records with search filter
$count_query = "SELECT COUNT(*) AS total FROM model m JOIN car_makers cm ON m.id_car_makers = cm.id WHERE m.name LIKE '%$search_term%'";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $results_per_page);

// Query to select data from the model table with search and pagination
$query = "SELECT m.id, m.name, m.full_name, cm.maker_name, m.year, m.price, m.description, m.car_types, m.color, m.fuel_type,m.stock, m.img
          FROM model m
          JOIN car_makers cm ON m.id_car_makers = cm.id
          WHERE m.name LIKE '%$search_term%'
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
    fputcsv($output, array('ID', 'Name', 'Full Name', 'Car Maker', 'Year', 'Price', 'Description', 'Car Types', 'Color', 'Fuel Type','stock', 'Image'));

    // Fetch and output the data with the search filter
    $export_query = "SELECT m.id, m.name, m.full_name, cm.maker_name, m.year, m.price, m.description, m.car_types, m.color, m.fuel_type,m.stock, m.img
                     FROM model m
                     JOIN car_makers cm ON m.id_car_makers = cm.id
                     WHERE m.name LIKE '%$search_term%'";
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
<?php include('../includes/sidebar.php');?>
<main>
    
<form method="get" class="mb-3">
    <div class="form-row">
        <div class="col">
            <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?php echo htmlspecialchars($search_term); ?>">
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

<form method="post" class="mb-3">
    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
    <button type="submit" name="export" class="btn btn-success">Export to CSV</button>
</form>


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
                <th> stock	</th>
                <th>Image</th>
          
                <th>Actions</th>
                <th>មើល</th>

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
                    <a href="auction_update_stock.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm ">Edit</a>
                </td>
                    <td>
                        <?php if ($row['img']): ?>
                            <?php if (filter_var($row['img'], FILTER_VALIDATE_URL)): ?>
                                <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Image" style="width: 100px;">
                            <?php else: ?>
                                <img src="../uploads/imagesmodel/<?php echo htmlspecialchars($row['img']); ?>" alt="Image" style="width: 100px;">
                            <?php endif; ?>
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
              
                
           


                        <a href="edit_model.php?id=<?php echo $row['id']; ?>" class=" btn btn-danger btn-sm">Edit</a>

                      
                        <button class="openDeleteModalBtn btn btn-danger btn-sm" data-id="<?php echo $row['id']; ?>">hide</button>
                    
                        <button class="openDeleteModalBtn btn btn-danger btn-sm" data-id="<?php echo $row['id']; ?>">Delete</button>

                        <!-- Modal for delete confirmation -->
                        <div id="deleteModal" class="modal" style="display:none;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Deletion</h5>

                                        <button type="button" class="close closeModalBtn">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this model?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="" id="confirmDeleteBtn" class="btn btn-danger btn-sm">Delete</a>
                                        <button type="button" class="btn btn-secondary closeModalBtn">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td>  <a href="http://www/user/details.php?model_id=<?php echo $row['id']; ?>" >មើលជាuser</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>






    <!-- Pagination -->
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li><a href="?search=<?php echo urlencode($search_term); ?>&page=1" class="btn btn-light">1</a></li>
            <li><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page - 1; ?>" class="btn btn-light">Previous</a></li>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);

        if ($start > 1) echo '<li>...</li>';
        
        for ($i = $start; $i <= $end; $i++): ?>
            <li><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $i; ?>" class="btn btn-light <?php if ($page == $i) echo 'active'; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>

        <?php if ($end < $total_pages): ?>
            <li>...</li>
            <li><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $total_pages; ?>" class="btn btn-light"><?php echo $total_pages; ?></a></li>
            <li><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page + 1; ?>" class="btn btn-light">Next</a></li>
        <?php endif; ?>
    </ul>



</main>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current page from localStorage or default to 1
    const currentPage = localStorage.getItem('currentPage') || 1;
    
    // Set the page parameter in the URL
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('page', currentPage);
    window.history.replaceState({}, '', `${window.location.pathname}?${urlParams}`);

    // Save the page number to localStorage on page change
    document.querySelectorAll('.pagination a').forEach(anchor => {
        anchor.addEventListener('click', function() {
            const pageNumber = new URL(this.href).searchParams.get('page');
            localStorage.setItem('currentPage', pageNumber);
        });
    });
});
</script>


<script src="../assets/js/Popups.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
