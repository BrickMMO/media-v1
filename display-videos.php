<?php
// Fetch uploaded videos from the database
include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// SQL query to get videos
$sql = "SELECT id, videoName, videoPath FROM videos";
$result = $db->query($sql);

// Check if the query returned any results
if ($result->num_rows > 0) {
    // Fetch each video data
    while ($row = $result->fetch_assoc()) {
        $videoId = $row["id"];
        $videoName = $row["videoName"];
        $videoPath = $row["videoPath"];

        // Output the formatted video card with edit and delete options
        echo '
            <div class="col-md-4 mb-3">
                <div class="card video-card">
                    <iframe src="' . htmlspecialchars($videoPath) . '" class="card-img-top" allowfullscreen></iframe>
                    <div class="card-body">
                        <h5 class="card-title">' . htmlspecialchars($videoName) . '</h5>
                        <div class="btn-group" role="group" aria-label="Video Actions">
                            <a href="edit-video.php?id=' . $videoId . '" class="btn btn-primary">Edit</a>
                            <a href="delete-video.php?id=' . $videoId . '" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
} else {
    echo '<p class="text-center">No videos uploaded yet.</p>';
}
$db->close();
?>
