<?php
require '../../config/db.php'; // Connect to the database
session_start();
require '../../config/Check_login.php'; // Check if the user is logged in

$id = $_GET['id'] ?? null; // Get the ID of the model to edit
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

// Fetch car makers for select input
$makersQuery = "SELECT id, maker_name FROM car_makers";
$makersResult = $conn->query($makersQuery);

// Fetch car types for select input
$typesQuery = "SELECT DISTINCT car_types FROM model"; // Assuming the car types are stored in the model table
$typesResult = $conn->query($typesQuery);

// Fetch colors for select input
$colorsQuery = "SELECT DISTINCT color FROM model"; // Assuming the colors are stored in the model table
$colorsResult = $conn->query($colorsQuery);

// Fetch fuel types for select input
$fuelTypesQuery = "SELECT DISTINCT fuel_type FROM model"; // Assuming the fuel types are stored in the model table
$fuelTypesResult = $conn->query($fuelTypesQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $full_name = $_POST['full_name'];
    $car_maker_id = $_POST['car_maker_id'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $car_types = $_POST['car_types'];
    $color = $_POST['color'];
    $fuel_type = $_POST['fuel_type'];

    // Handle image upload
    $img = $model['img']; // Preserve the old image if no new image is uploaded
    
    if (!empty($_POST['img_url'])) {
        // If the image is provided via URL
        $img = $_POST['img_url'];
    } elseif (isset($_FILES['img_file']) && $_FILES['img_file']['error'] == 0) {
        // If the image is uploaded via file
        $targetDir = "../uploads/imagesmodel/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create directory if it does not exist
        }
        $targetFile = $targetDir . basename($_FILES["img_file"]["name"]);
        if (move_uploaded_file($_FILES["img_file"]["tmp_name"], $targetFile)) {
            // Remove old image if it was not an external URL
            if ($model['img'] && !filter_var($model['img'], FILTER_VALIDATE_URL)) {
                unlink($targetDir . basename($model['img']));
            }
            $img = basename($_FILES["img_file"]["name"]);
        } else {
            echo "Error uploading file.";
        }
    }

    // Update the model
    $updateQuery = "UPDATE model SET name = ?, full_name = ?, id_car_makers = ?, year = ?, price = ?, description = ?, car_types = ?, color = ?, fuel_type = ?, img = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }

    // Bind parameters: 'ssiissssssi'
    if (!$stmt->bind_param(
        "ssiissssssi", 
        $name, 
        $full_name, 
        $car_maker_id, 
        $year, 
        $price, 
        $description, 
        $car_types, 
        $color, 
        $fuel_type,
        $img, 
        $id
    )) {
        die("Failed to bind parameters: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Failed to execute statement: " . $stmt->error);
    }

    header("Location: view_models_action.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Model</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<?php include('../includes/sidebar.php');?>
<?php include '../../header.php'; ?>
<main>
<div class="container mt-5">
    <h2>Edit Model</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($model['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($model['full_name']); ?>">
        </div>
        <div class="form-group">
            <label for="car_maker_id">Car Maker:</label>
            <select class="form-control" id="car_maker_id" name="car_maker_id" required>
                <?php while ($row = $makersResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo ($row['id'] == $model['id_car_makers']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['maker_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="number" class="form-control" id="year" name="year" value="<?php echo htmlspecialchars($model['year']); ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($model['price']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($model['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="car_types">Car Types:</label>
            <select class="form-control" id="car_types" name="car_types">
                <?php while ($row = $typesResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['car_types']); ?>" <?php echo ($row['car_types'] == $model['car_types']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['car_types']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="color">Color:</label>
            <select class="form-control" id="color" name="color">
                <?php while ($row = $colorsResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['color']); ?>" <?php echo ($row['color'] == $model['color']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['color']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fuel_type">Fuel Type:</label>
            <select class="form-control" id="fuel_type" name="fuel_type">
                <?php while ($row = $fuelTypesResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['fuel_type']); ?>" <?php echo ($row['fuel_type'] == $model['fuel_type']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['fuel_type']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="img_url">Image URL:</label>
            <input type="url" class="form-control" id="img_url" name="img_url" value="<?php echo htmlspecialchars($model['img']); ?>">
        </div>
        <div class="form-group">
            <label for="img_file">Or upload a file:</label>
            <input type="file" class="form-control" id="img_file" name="img_file">

            <?php
            // Determine the image source based on the current image value
            $current_img = htmlspecialchars($model['img']);
            $is_url = filter_var($current_img, FILTER_VALIDATE_URL);

            if ($is_url) {
                // Display image if it's a URL
                echo "<img src='$current_img' alt='Current Image' class='img-thumbnail mt-2' style='max-width: 200px;'>";
            } elseif (!empty($current_img)) {
                // Display image if it's a local file
                $image_path = "../uploads/imagesmodel/" . $current_img;
                if (file_exists($image_path)) {
                    echo "<img src='$image_path' alt='Current Image' class='img-thumbnail mt-2' style='max-width: 200px;'>";
                }
            }
            ?>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</main>

</body>
</html>
