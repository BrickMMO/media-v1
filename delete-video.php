<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$videoId = $_GET['id'];


include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch video details based on ID
$sql = $db->prepare("SELECT videoName, videoPath FROM videos WHERE id = ?");
$sql->bind_param("i", $videoId);
$sql->execute();
$result = $sql->get_result();

$videoName = "";
$videoPath = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $videoName = $row["videoName"];
    $videoPath = $row["videoPath"];
}

$sql->close();

// Video Deletion
if (isset($_POST['delete'])) {
    // Delete video from database
    $deleteSql = $db->prepare("DELETE FROM videos WHERE id = ?");
    $deleteSql->bind_param("i", $videoId);
    $deleteSql->execute();
    $deleteSql->close();
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
    <title>Delete Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Delete Video</h5>
                        <p class="card-text">Are you sure you want to delete this video?</p>
                        <h6><?php echo htmlspecialchars($videoName); ?></h6>
                        <div class="embed-responsive embed-responsive-16by9 mb-3">
                            <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($videoPath); ?>" allowfullscreen></iframe>
                        </div>
                        <form method="post" action="">
                            <div class="d-grid gap-2">
                                <button type="submit" name="delete" class="btn btn-danger">Yes, delete it</button>
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
