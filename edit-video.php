<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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
$newPath = '';
$newTags = '';

// Fetch existing video details 
$sql = "SELECT videoName, videoPath, tags FROM videos WHERE id=?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($videoName, $videoPath, $tags);
$stmt->fetch();
$stmt->close();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = $_POST['new_name'];
    $newPath = $_POST['new_path'];
    $newTags = $_POST['new_tags'];

    // Update video details in the database
    $sql = "UPDATE videos SET videoName=?, videoPath=?, tags=? WHERE id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssi", $newName, $newPath, $newTags, $id);
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
    <title>Edit Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Edit Video</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-16by9 mb-3">
                    <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($videoPath); ?>" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>">
                    <div class="mb-3">
                        <label for="new_name" class="form-label">New Name:</label>
                        <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo htmlspecialchars($videoName); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_path" class="form-label">New Video Link:</label>
                        <input type="text" class="form-control" id="new_path" name="new_path" value="<?php echo htmlspecialchars($videoPath); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_tags" class="form-label">New Tags:</label>
                        <input type="text" class="form-control" id="new_tags" name="new_tags" value="<?php echo htmlspecialchars($tags); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
