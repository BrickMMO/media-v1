<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if image ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}


include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$id = $_GET['id'];
$newName = '';
$newTags = '';

// Fetching existing image name and image data from the database
$sql = "SELECT imageName, image, tags FROM images WHERE id=?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($imageName, $imageData, $tags);
$stmt->fetch();
$stmt->close();

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = $_POST['new_name'];
    $newTags = $_POST['new_tags'];

    // Updating image name and tags in the database
    $sql = "UPDATE images SET imageName=?, tags=? WHERE id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssi", $newName, $newTags, $id);
    $stmt->execute();
    $stmt->close();
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
    <title>Edit Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Edit Image</h2>
        <div class="row">
            <div class="col-md-6">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($imageData); ?>" alt="<?php echo $imageName; ?>" class="img-fluid">
            </div>
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>">
                    <div class="mb-3">
                        <label for="new_name" class="form-label">New Name:</label>
                        <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo $imageName; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="new_tags" class="form-label">New Tags:</label>
                        <input type="text" class="form-control" id="new_tags" name="new_tags" value="<?php echo $tags; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
