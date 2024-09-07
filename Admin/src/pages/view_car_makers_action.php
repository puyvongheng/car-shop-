<?php
require '../../config/db.php'; // Adjust the path as needed

if (!$conn) {
    die("Database connection failed.");
}









// Fetch car makers from the database
$sql = "SELECT * FROM car_makers";
$result = $conn->query($sql);

// Handle form submission for adding a new car maker
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $maker_name = $_POST['maker_name'];
    $full_name = $_POST['full_name'];
    $logo = '';
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

    // Insert new car maker into the database
    $insertQuery = "INSERT INTO car_makers (maker_name, full_name, logo, countries) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $maker_name, $full_name, $logo, $countries);
    if ($stmt->execute()) {
        header("Location: view_car_makers_action.php");
        exit;
    } else {
        echo "Error adding car maker: " . $stmt->error;
    }
    $stmt->close();
}








// Handle export request
if (isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=car_makers.csv');

    $output = fopen('php://output', 'w');

    // Output the column headings
    fputcsv($output, array('ID', 'Maker Name', 'Full Name', 'Logo', 'Countries'));

    // Fetch and output the data
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Car Makers</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/page.css">
</head>
<body>
<!-- Navigation Bar -->
<?php include('../includes/sidebar.php');?>
<main>

<form method="post">
        <button type="submit" name="export" class="btn btn-secondary mb-3">Export to CSV</button>
    </form>


    <h2>Car Makers List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Maker Name</th>
                <th>Full Name</th>
                <th>Logo</th>
                <th>Countries</th>
                <th>Actions</th> <!-- Added column for actions -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['maker_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td>
                        <?php if (filter_var($row['logo'], FILTER_VALIDATE_URL)): ?>
                            <img src="<?php echo htmlspecialchars($row['logo']); ?>" alt="Logo" style="width: 100px;">
                        <?php else: ?>
                            <img src="../uploads/logocarmake/<?php echo htmlspecialchars($row['logo']); ?>" alt="Logo" style="width: 100px;">
                        <?php endif; ?>

                    </td>
                    <td><?php echo htmlspecialchars($row['countries']); ?></td>
                    <td>
                        <a href="edit_car_maker.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="edit_car_maker.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm">Hide</a>
                        <a href="delete_car_maker.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this car maker?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>


    

    
    <!-- Add New Car Maker Form 
    <h2>Add New Car Maker</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="maker_name">Maker Name:</label>
            <input type="text" class="form-control" id="maker_name" name="maker_name" required>
        </div>
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" class="form-control" id="full_name" name="full_name">
        </div>
        <div class="form-group">
            <label for="logo">Logo (Upload File or Enter URL):</label>
            <input type="file" class="form-control-file" id="logo" name="logo">
            <input type="text" class="form-control mt-2" id="logo_url" name="logo_url" placeholder="Or enter logo URL">
        </div>
        <div class="form-group">
            <label for="countries">Countries:</label>
            <input type="text" class="form-control" id="countries" name="countries">
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add Car Maker</button>
    </form>

    -->

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
