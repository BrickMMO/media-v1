<?php

include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';

// SQL query based on search query
if ($query) {
    $stmt = $db->prepare("SELECT id, videoName, videoPath FROM videos WHERE videoName LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
    $stmt->bind_param("s", $query);
} else {
    $stmt = $db->prepare("SELECT id, videoName, videoPath FROM videos ORDER BY RAND() LIMIT 12");
}

$stmt->execute();
$result = $stmt->get_result();

// Display video cards
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videoId = $row["id"];
        $videoName = $row["videoName"];
        $videoPath = $row["videoPath"];
        echo '
            <div class="col-md-4 mb-3">
                <div class="card video-card" onclick="window.location.href=\'video-details.php?id=' . $videoId . '&name=' . urlencode($videoName) . '\'">
                    <iframe src="' . $videoPath . '" class="card-img-top" allowfullscreen></iframe>
                    <div class="card-body">
                        <h5 class="card-title">' . $videoName . '</h5>
                    </div>
                </div>
            </div>
        ';
    }
} else {
    echo "<p class='text-center'>No results found.</p>";
}

$stmt->close();
$db->close();
?>
