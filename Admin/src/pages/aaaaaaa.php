<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Revenue</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>

<main>
<div class="container mt-4">
    <h2>Monthly Revenue</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Month</th>
                <th>Total Revenue (USD)</th>
            </tr>
        </thead>
        <tbody>
            <?php
           require '../../config/db.php'; // Connect to the database

            // SQL query to get total revenue per month
            $sql = "SELECT DATE_FORMAT(o.order_date, '%Y-%m') AS sale_month, SUM(o.total_price) AS total_revenue
                    FROM orders o
                    GROUP BY YEAR(o.order_date), MONTH(o.order_date)
                    ORDER BY sale_month DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["sale_month"] . "</td>";
                    echo "<td>$" . number_format($row["total_revenue"], 2) . "</td>";
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
