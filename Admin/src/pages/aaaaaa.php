<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Car Sales</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>

<main>
<div class="container mt-4">
    <h2>Monthly Car Sales</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Month</th>
                <th>Total Cars Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../../config/db.php'; // Connect to the database
            // SQL query to get total cars sold per month
            $sql = "SELECT DATE_FORMAT(o.order_date, '%Y-%m') AS sale_month, SUM(oi.quantity) AS total_sold
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    GROUP BY YEAR(o.order_date), MONTH(o.order_date)
                    ORDER BY sale_month DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["sale_month"] . "</td>";
                    echo "<td>" . $row["total_sold"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No results found.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>
