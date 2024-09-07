<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Selling Car Makers</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>

<main>
<div class="container mt-4">
    <h2>Top Selling Car Makers</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Car Maker</th>
                <th>Total Cars Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php
         require '../../config/db.php'; // Connect to the database

            // SQL query to get top selling car makers
            $sql = "SELECT cm.maker_name, SUM(oi.quantity) AS total_sold
                    FROM car_makers cm
                    JOIN model m ON cm.id = m.id_car_makers
                    JOIN order_items oi ON m.id = oi.model_id
                    GROUP BY cm.id
                    ORDER BY total_sold DESC
                   /* LIMIT 10   */" ;

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["maker_name"] . "</td>";
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
