<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>
<main>
<div class="container mt-5">
    <h2 class="text-center">Customer Reviews</h2>

    <!-- Filtering Form -->
    <form method="GET" class="mb-4">
        <div class="form-row">
            <div class="col-md-2">
                <input type="text" class="form-control" name="id" placeholder="Review ID" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="user_id" placeholder="User ID" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="customer_name" placeholder="Customer Name" value="<?php echo htmlspecialchars($_GET['customer_name'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="model" placeholder="Model" value="<?php echo htmlspecialchars($_GET['model'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="rating" placeholder="Rating" min="1" max="5" value="<?php echo htmlspecialchars($_GET['rating'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="review_date" placeholder="Review Date" value="<?php echo htmlspecialchars($_GET['review_date'] ?? ''); ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <?php
    require '../../config/db.php'; // Database connection

    // Get filter values from the request
    $id = $_GET['id'] ?? '';
    $user_id = $_GET['user_id'] ?? '';
    $customer_name = $_GET['customer_name'] ?? '';
    $model = $_GET['model'] ?? '';
    $rating = $_GET['rating'] ?? '';
    $review_date = $_GET['review_date'] ?? '';

    // Get sort column and direction
    $sort_column = $_GET['sort'] ?? 'r.review_date';
    $sort_direction = $_GET['direction'] ?? 'DESC';

    // Construct the SQL query with filters and sorting
    $sql = "SELECT r.id, r.user_id, r.model_id, u.first_name, u.last_name, m.name AS model_name, r.rating, r.review_text, r.review_date
            FROM reviews r
            JOIN user u ON r.user_id = u.id
            JOIN model m ON r.model_id = m.id
            WHERE 1=1";

    if ($id) {
        $sql .= " AND r.id = '" . $conn->real_escape_string($id) . "'";
    }
    if ($user_id) {
        $sql .= " AND r.user_id = '" . $conn->real_escape_string($user_id) . "'";
    }
    if ($customer_name) {
        $sql .= " AND (u.first_name LIKE '%" . $conn->real_escape_string($customer_name) . "%' OR u.last_name LIKE '%" . $conn->real_escape_string($customer_name) . "%')";
    }
    if ($model) {
        $sql .= " AND m.name LIKE '%" . $conn->real_escape_string($model) . "%'";
    }
    if ($rating) {
        $sql .= " AND r.rating = '" . $conn->real_escape_string($rating) . "'";
    }
    if ($review_date) {
        $sql .= " AND DATE(r.review_date) = '" . $conn->real_escape_string($review_date) . "'";
    }

    $sql .= " ORDER BY $sort_column $sort_direction";

    $result = $conn->query($sql);
    ?>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'r.id', 'direction' => $sort_column == 'r.id' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">Review ID</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'r.user_id', 'direction' => $sort_column == 'r.user_id' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">User ID</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'r.model_id', 'direction' => $sort_column == 'r.model_id' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">Model ID</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'u.first_name', 'direction' => $sort_column == 'u.first_name' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">Customer Name</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'm.name', 'direction' => $sort_column == 'm.name' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">Model</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'r.rating', 'direction' => $sort_column == 'r.rating' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">Rating</a></th>
                <th>Review Text</th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'r.review_date', 'direction' => $sort_column == 'r.review_date' && $sort_direction == 'ASC' ? 'DESC' : 'ASC'])); ?>">Review Date</a></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["model_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["first_name"] . " " . $row["last_name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["model_name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["rating"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["review_text"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["review_date"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>No reviews found.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
