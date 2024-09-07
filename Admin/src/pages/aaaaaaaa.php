<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Revenue Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>

<main>
<div class="container mt-4">
    <h2>Daily Revenue Chart</h2>
    <canvas id="revenueChart"></canvas>
</div>
</main>
<script>
    // Fetch data from PHP
    const labels = <?php
        // PHP code to fetch dates
        require '../../config/db.php'; // Connect to the database


        // SQL query to get daily revenue
        $sql = "SELECT DATE(o.order_date) AS sale_date, SUM(o.total_price) AS total_revenue
                FROM orders o
                GROUP BY DATE(o.order_date)
                ORDER BY sale_date ASC";

        $result = $conn->query($sql);

        $dates = [];
        $revenues = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $dates[] = $row['sale_date'];
                $revenues[] = $row['total_revenue'];
            }
        }

        $conn->close();

        // Convert PHP arrays to JavaScript
        echo json_encode($dates);
    ?>;

    const revenueData = <?php echo json_encode($revenues); ?>;

    // Chart.js configuration
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line', // Use line chart
        data: {
            labels: labels, // X-axis labels (dates)
            datasets: [{
                label: 'Total Revenue (USD)',
                data: revenueData, // Y-axis data (revenues)
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4 // Curve the line
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Revenue (USD)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
