<?php
require '../../config/db.php'; // Connect to the database
session_start();
require '../../config/Check_login.php'; // Check if the user is logged in

$id = $_GET['id'] ?? null; // Get the ID of the model to update
if (!$id) {
    die("Invalid request."); // If ID is not provided
}

// Fetch existing data
$query = "SELECT * FROM model WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$model = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stock = (int)$_POST['stock']; // Convert to integer

    // Update the stock
    $updateQuery = "UPDATE model SET stock = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }

    if (!$stmt->bind_param("ii", $stock, $id)) {
        die("Failed to bind parameters: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Failed to execute statement: " . $stmt->error);
    }
/*
    header("Location: view_models_action.php"); // Redirect to the page showing models
    exit;
*/
    echo "<script>
    if (window.history.length > 2) {
        window.history.go(-2);
    } else {
        window.history.back();
    }
  </script>";
exit;
  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Stock</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap Icons -->
    <style>
        .input-group-button {
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('../includes/sidebar.php'); ?>
<?php include '../../header.php'; ?>

<main>
<div class="container mt-5">
    <h2>Update Stock for Model: <?php echo htmlspecialchars($model['name']); ?></h2>
    <form method="post">
        <div class="form-group">
            <label for="stock">Current Stock:</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="btn btn-outline-secondary input-group-button" id="decrement">
                        <i class="bi bi-dash"></i> <!-- Bootstrap icon for minus -->
                    </button>
                </div>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($model['stock']); ?>" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary input-group-button" id="increment">
                        <i class="bi bi-plus"></i> <!-- Bootstrap icon for plus -->
                    </button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Stock</button>
    </form>
</div>
</main>

<script>
    // JavaScript to handle increment and decrement
    document.getElementById('decrement').addEventListener('click', function() {
        let stockInput = document.getElementById('stock');
        let currentValue = parseInt(stockInput.value);
        if (currentValue > 0) {
            stockInput.value = currentValue - 1;
        }
    });

    document.getElementById('increment').addEventListener('click', function() {
        let stockInput = document.getElementById('stock');
        let currentValue = parseInt(stockInput.value);
        stockInput.value = currentValue + 1;
    });
</script>

</body>
</html>
