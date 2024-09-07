<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top-Selling Car Models</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php'); ?>

<main>
<div class="container mt-4">
    <h2>Top-Selling Car Models</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Full Name</th>
                <th>Total Sold</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../../config/db.php'; // Connect to the database

            // SQL query to get top-selling models
            $sql = "SELECT m.id, m.name, m.full_name, m.img, SUM(oi.quantity) AS total_quantity
                    FROM model m
                    JOIN order_items oi ON m.id = oi.model_id
                    JOIN orders o ON oi.order_id = o.id
                    GROUP BY m.id
                    ORDER BY total_quantity DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($row["img"]) . "' alt='" . htmlspecialchars($row["name"]) . "' style='width:100px; height:auto;'></td>";
                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["full_name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["total_quantity"]) . "</td>";
                    
                    // Provide correct URLs for the action links
                    $view_link = "model.php?search=" . urlencode($row["full_name"]);
                    echo "<td><a href='" . $view_link . "'>View Details</a></td>";

                    $view_link = "http://www/user/details.php?model_id=" . urlencode($row["id"]);
                    echo "<td><a href='" . $view_link . "'>View Details (user)</a></td>";

                

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No results found.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>
