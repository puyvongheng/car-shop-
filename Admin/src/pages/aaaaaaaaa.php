<?php
require '../../config/db.php'; // Include your database connection file

if (isset($_GET['filter'])) {
    header('Content-Type: application/json');

    $filter = $_GET['filter'];

    switch ($filter) {
        case 'daily':
            $sql = "SELECT DATE(order_date) AS sale_date, SUM(total_price) AS total_revenue
                    FROM orders
                    GROUP BY DATE(order_date)
                    ORDER BY sale_date ASC";
            break;
        case 'weekly':
            $sql = "SELECT YEAR(order_date) AS year, WEEK(order_date) AS week, SUM(total_price) AS total_revenue
                    FROM orders
                    GROUP BY YEAR(order_date), WEEK(order_date)
                    ORDER BY year ASC, week ASC";
            break;
        case 'monthly':
            $sql = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS sale_month, SUM(total_price) AS total_revenue
                    FROM orders
                    GROUP BY YEAR(order_date), MONTH(order_date)
                    ORDER BY sale_month ASC";
            break;
        case 'yearly':
            $sql = "SELECT YEAR(order_date) AS sale_year, SUM(total_price) AS total_revenue
                    FROM orders
                    GROUP BY YEAR(order_date)
                    ORDER BY sale_year ASC";
            break;
        default:
            $sql = "SELECT DATE(order_date) AS sale_date, SUM(total_price) AS total_revenue
                    FROM orders
                    GROUP BY DATE(order_date)
                    ORDER BY sale_date ASC";
    }

    $result = $conn->query($sql);

    $labels = [];
    $revenue = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($filter == 'weekly') {
                $labels[] = $row['year'] . ' Week ' . $row['week'];
            } elseif ($filter == 'monthly') {
                $labels[] = $row['sale_month'];
            } elseif ($filter == 'yearly') {
                $labels[] = $row['sale_year'];
            } else {
                $labels[] = $row['sale_date'];
            }
            $revenue[] = $row['total_revenue'];
        }
    }

    echo json_encode(['labels' => $labels, 'revenue' => $revenue]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Chart with Filters</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>

<?php include('../includes/sidebar.php');?>

<main>

<div class="container mt-4">
    <h2>Revenue Chart with Filters</h2>
    <div class="form-group">
        <label for="filter">Select Time Period:</label>
        <select id="filter" class="form-control" onchange="updateChart()">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>
    <canvas id="revenueChart"></canvas>
</div>

</main>

<script>
    function updateChart() {
        const filter = document.getElementById('filter').value;

        fetch(`?filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                revenueChart.data.labels = data.labels;
                revenueChart.data.datasets[0].data = data.revenue;
                revenueChart.update();
            });
    }

    // Initial chart setup
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Empty initial labels
            datasets: [{
                label: 'Total Revenue (USD)',
                data: [], // Empty initial data
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4
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

    // Initial load
    updateChart();
</script>

</body>
</html>
