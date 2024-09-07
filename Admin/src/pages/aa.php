<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Buyers</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>

<main>

<div class="container mt-4">
    <h2>Top Buyers</h2>
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Total Orders(ដង)</th>
                <th>link</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require '../../config/db.php'; // Connect to the database
            // SQL query to get top buyers
            $sql = "SELECT u.id, u.first_name, u.last_name, COUNT(o.id) AS total_orders
                    FROM user u
                    JOIN orders o ON u.id = o.user_id
                    GROUP BY u.id
                    ORDER BY total_orders DESC
                  /*  LIMIT 10   */";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";


                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["total_orders"] . "</td>";

 echo "<td>" . "<a href='#'>aa</a>"  ."</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No results found.</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>
