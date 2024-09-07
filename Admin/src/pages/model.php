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
$count_query = "SELECT COUNT(*) AS total FROM model m 
                JOIN car_makers cm ON m.id_car_makers = cm.id 
                WHERE m.id LIKE '%$search_term%' 
                OR m.name LIKE '%$search_term%' 
                OR m.full_name LIKE '%$search_term%' 
                OR cm.maker_name LIKE '%$search_term%' 
                OR m.year LIKE '%$search_term%' 
                OR m.price LIKE '%$search_term%' 
                OR m.description LIKE '%$search_term%' 
                OR m.car_types LIKE '%$search_term%' 
                OR m.color LIKE '%$search_term%' 
                OR m.fuel_type LIKE '%$search_term%'";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $results_per_page);

// Query to select data from the model table with search and pagination
$query = "SELECT m.id, m.name, m.full_name, cm.maker_name, m.year, m.price, m.description, m.car_types, m.color, m.fuel_type, m.stock, m.img
          FROM model m
          JOIN car_makers cm ON m.id_car_makers = cm.id
          WHERE m.id LIKE '%$search_term%' 
          OR m.name LIKE '%$search_term%' 
          OR m.full_name LIKE '%$search_term%' 
          OR cm.maker_name LIKE '%$search_term%' 
          OR m.year LIKE '%$search_term%' 
          OR m.price LIKE '%$search_term%' 
          OR m.description LIKE '%$search_term%' 
          OR m.car_types LIKE '%$search_term%' 
          OR m.color LIKE '%$search_term%' 
          OR m.fuel_type LIKE '%$search_term%'
          LIMIT $start, $results_per_page";
$result = $conn->query($query);

// Check if the query was successful
if (!$result) {
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


<main >
   <br>
   <br>
    <form method="get" class="mb-3">
        <div class="form-row">
            <div class="col">
                <input type="text" name="search" class="form-control" placeholder="Search by ID, Name, Full Name, Car Maker, Year, Price, Description, Car Types, Color, Fuel Type" value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>


    <h2>View Models</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
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
                        <a href="auction_update_stock.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">add<i class="fa-solid fa-plus"></i></a>
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
                        <a href="edit_model.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit<i class="fa-solid fa-pen"></i></a>
                        <!-- No Delete Button -->
                    </td>
                    <td>
                        <a href="http://www/user/details.php?model_id=<?php echo $row['id']; ?>">View as User <i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item"><a href="?search=<?php echo urlencode($search_term); ?>&page=1" class="page-link">1</a></li>
            <li class="page-item"><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page - 1; ?>" class="page-link">Previous</a></li>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);

        if ($start > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?php if ($page == $i) echo 'active'; ?>"><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a></li>
        <?php endfor; ?>

        <?php if ($end < $total_pages): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <li class="page-item"><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $total_pages; ?>" class="page-link"><?php echo $total_pages; ?></a></li>
            <li class="page-item"><a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page + 1; ?>" class="page-link">Next</a></li>
        <?php endif; ?>
    </ul>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
