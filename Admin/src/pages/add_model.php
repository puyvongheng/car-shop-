<?php
// Include database configuration
require '../../config/db.php'; // Adjust the path as needed

if (!$conn) {
    die("Database connection failed.");
}

// Fetch car makers for select input
$makersQuery = "SELECT id, maker_name FROM car_makers";
$makersResult = $conn->query($makersQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $full_name = $_POST['full_name'];
    $car_maker_id = $_POST['car_maker_id'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $car_types = $_POST['car_types'] === 'Othercar_types' ? $_POST['car_types_input'] : $_POST['car_types'];
    $color = $_POST['color'] === 'Othercolor' ? $_POST['color_input'] : $_POST['color'];
    $fuel_type = $_POST['fuel_type'] === 'Otherfuel_type' ? $_POST['fuel_type_input'] : $_POST['fuel_type'];

    // Handle file upload
    $img = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $targetDir = "../uploads/imagesmodel/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create directory if it does not exist
        }
        $targetFile = $targetDir . basename($_FILES["img"]["name"]);
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $targetFile)) {
            $img = basename($_FILES["img"]["name"]);
        } else {
            echo "Error uploading file.";
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO model (name, full_name, id_car_makers, year, price, description, car_types, color, fuel_type, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiissssss", $name, $full_name, $car_maker_id, $year, $price, $description, $car_types, $color, $fuel_type, $img);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: add_model.php");
        exit;
    } else {
        echo "Error inserting data: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>
+
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Model</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('../includes/sidebar.php'); ?>
<main>
<div class="container mt-5">
    <h2>Add Model</h2>
    <form method="post" enctype="multipart/form-data">
        <!-- Name -->
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <!-- Full Name -->
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" class="form-control" id="full_name" name="full_name">
        </div>
        <!-- Car Maker -->

  
        <div class="form-group">
            <label for="car_maker_id">Car Maker:</label>
            <select class="form-control" id="car_maker_id" name="car_maker_id" required>
                <?php while ($row = $makersResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                        <?php echo htmlspecialchars($row['maker_name']); ?>

                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <!-- Year -->
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="number" class="form-control" id="year" name="year" required>
        </div>
        <!-- Price -->
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <!-- Description -->
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <!-- Car Types -->
        <div class="form-group">
            <label for="car_types">Car Types:</label>
            <select class="form-control" id="car_types" name="car_types" onchange="toggleInputFieldCarTypes()">
                <?php
                $car_types_sql = "SELECT DISTINCT car_types FROM model ORDER BY car_types";
                $car_types_result = $conn->query($car_types_sql);
                while ($car_types_row = $car_types_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($car_types_row['car_types']); ?>">
                        <?php echo htmlspecialchars($car_types_row['car_types']); ?>
                    </option>
                <?php endwhile; ?>
                <option value="Othercar_types">Other (Please specify)</option>
            </select>
            <input type="text" class="form-control" id="car_types_input" name="car_types_input" placeholder="Car Types" style="display: none; margin-top: 10px;">
        </div>
        <!-- Color -->
        <div class="form-group">
            <label for="color">Color:</label>
            <select class="form-control" id="color" name="color" onchange="toggleInputFieldColor()">
                <?php
                


                $color_sql = "SELECT DISTINCT color FROM model ORDER BY color";
                $color_result = $conn->query($color_sql);
                while ($color_row = $color_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($color_row['color']); ?>">
                        <?php echo htmlspecialchars($color_row['color']); ?>
                    </option>
                <?php endwhile; ?>
                <option value="Othercolor">Other (Please specify)</option>
            </select>
            <input type="text" class="form-control" id="color_input" name="color_input" placeholder="Enter custom color" style="display: none; margin-top: 10px;">
        </div>
        <!-- Fuel Type -->
        <div class="form-group">
            <label for="fuel_type">Fuel Type:</label>
            <select class="form-control" id="fuel_type" name="fuel_type" onchange="toggleInputFieldFuelType()">
                <?php
                $fuel_type_sql = "SELECT DISTINCT fuel_type FROM model ORDER BY fuel_type";
                $fuel_type_result = $conn->query($fuel_type_sql);
                while ($fuel_type_row = $fuel_type_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($fuel_type_row['fuel_type']); ?>">
                        <?php echo htmlspecialchars($fuel_type_row['fuel_type']); ?>
                    </option>
                <?php endwhile; ?>
                <option value="Otherfuel_type">Other (Please specify)</option>
            </select>
            <input type="text" class="form-control" id="fuel_type_input" name="fuel_type_input" placeholder="Enter fuel type" style="display: none; margin-top: 10px;">
        </div>
        <!-- Image -->
        <div class="form-group">
            <label for="img">Image:</label>
            <input type="file" class="form-control-file" id="img" name="img">
        </div>
        <button type="submit" class="btn btn-primary">Add Model</button>

    </form>
</div>
</main>

<script>
function toggleInputFieldColor() {
    var selectElement = document.getElementById("color");
    var inputElement = document.getElementById("color_input");

    if (selectElement.value === "Othercolor") {
        inputElement.style.display = "block";
    } else {
        inputElement.style.display = "none";
    }
}

function toggleInputFieldCarTypes() {
    var selectElement = document.getElementById("car_types");
    var inputElement = document.getElementById("car_types_input");

    if (selectElement.value === "Othercar_types") {
        inputElement.style.display = "block";
    } else {
        inputElement.style.display = "none";
    }
}

function toggleInputFieldFuelType() {
    var selectElement = document.getElementById("fuel_type");
    var inputElement = document.getElementById("fuel_type_input");

    if (selectElement.value === "Otherfuel_type") {
        inputElement.style.display = "block";
    } else {
        inputElement.style.display = "none";
    }
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
