<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if image ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

// Get image ID from URL parameter
$imageId = $_GET['id'];

// Include database connection
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = 'root';
$dbName = 'brickmmo';

// Create database connection
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch image details based on ID
$sql = $db->prepare("SELECT imageName, image FROM images WHERE id = ?");
$sql->bind_param("i", $imageId);
$sql->execute();
$result = $sql->get_result();

$imageName = "";
$imagePath = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imageName = $row["imageName"];
    $imageData = $row["image"];
    $imagePath = 'data:image/jpeg;base64,' . base64_encode($imageData);
}

$sql->close();

// Handle image deletion
if (isset($_POST['delete'])) {
    // Delete image from database
    $deleteSql = $db->prepare("DELETE FROM images WHERE id = ?");
    $deleteSql->bind_param("i", $imageId);
    $deleteSql->execute();
    $deleteSql->close();

    // Redirect to dashboard after deletion
    header("Location: dashboard.php");
    exit();
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Delete Image</h5>
                        <p class="card-text">Are you sure you want to delete this image?</p>
                        <img src="<?php echo $imagePath; ?>" class="img-fluid mb-3" alt="<?php echo $imageName; ?>">
                        <!-- Form to handle image deletion -->
                        <form method="post" action="">
                            <div class="d-grid gap-2">
                                <!-- Button to confirm deletion -->
                                <button type="submit" name="delete" class="btn btn-danger">Yes, delete it</button>
                                <!-- Button to cancel deletion and return to dashboard -->
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
