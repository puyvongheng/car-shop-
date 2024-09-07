<?php
require '../../config/db.php'; // Adjust the path as needed

if (!$conn) {
    die("Database connection failed.");
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid request.");
}

// Fetch existing data
$query = "SELECT * FROM car_makers WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$car_maker = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maker_name = $_POST['maker_name'];
    $full_name = $_POST['full_name'];
    $logo = $car_maker['logo']; // Preserve old logo if new one is not uploaded
    $countries = $_POST['countries'];

    // Handle file upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $targetDir = "../uploads/logocarmake/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create directory if it does not exist
        }
        $targetFile = $targetDir . basename($_FILES["logo"]["name"]);
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
            $logo = basename($_FILES["logo"]["name"]);
        } else {
            echo "Error uploading file.";
        }
    } elseif (!empty($_POST['logo_url'])) {
        $logo = $_POST['logo_url'];
    }

    // Update the car maker
    $updateQuery = "UPDATE car_makers SET maker_name = ?, full_name = ?, logo = ?, countries = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $maker_name, $full_name, $logo, $countries, $id);

    if ($stmt->execute()) {
        header("Location: view_car_makers_action.php");
        exit;
    } else {
        echo "Error updating data: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car Maker</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<!-- Navigation Bar -->
<?php include('../includes/sidebar.php');?>
<main>
<div class="container mt-5">
    <h2>Edit Car Maker</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="maker_name">Maker Name:</label>
            <input type="text" class="form-control" id="maker_name" name="maker_name" value="<?php echo htmlspecialchars($car_maker['maker_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($car_maker['full_name']); ?>">
        </div>
        <div class="form-group">
            <label for="logo">Logo (Upload File or Enter URL):</label>
            <input type="file" class="form-control-file" id="logo" name="logo">
            <input type="text" class="form-control mt-2" id="logo_url" name="logo_url" placeholder="Or enter logo URL" value="<?php echo htmlspecialchars($car_maker['logo']); ?>">
           
           
            <?php if (filter_var($car_maker['logo'], FILTER_VALIDATE_URL)): ?>
                <img src="<?php echo htmlspecialchars($car_maker['logo']); ?>" alt="Current Logo" style="width: 100px; margin-top: 10px;">
            <?php else: ?>
                <img src="../uploads/logocarmake/<?php echo htmlspecialchars($car_maker['logo']); ?>" alt="Current Logo" style="width: 100px; margin-top: 10px;">
            <?php endif; ?>


        </div>
        <div class="form-group">
            <label for="countries">Countries:</label>
            <input type="text" class="form-control" id="countries" name="countries" value="<?php echo htmlspecialchars($car_maker['countries']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Car Maker</button>
    </form>
</div>

</main>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
