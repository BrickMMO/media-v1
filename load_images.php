<?php
$query = isset($_GET['query']) ? $_GET['query'] : '';

include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
// SQL query based on search or tag
if ($query) {
    $sql = $db->prepare("SELECT id, imageName, image FROM images WHERE imageName LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
    $sql->bind_param("s", $query);
} else {
    $sql = $db->prepare("SELECT id, imageName, image FROM images ORDER BY RAND() LIMIT 12");
}

// Execute query and display results
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imageId = $row["id"];
        $imageName = $row["imageName"];
        $imageName = pathinfo($imageName, PATHINFO_FILENAME);
        $imageData = $row["image"];
        $imageDataEncoded = base64_encode($imageData);
        $imageSrc = 'data:image/jpeg;base64,' . $imageDataEncoded;
        echo '
            <div class="col-md-4 mb-3">
                <div class="card" style="max-width: 18rem;">
                    <a href="javascript:void(0)" class="image-link" onclick="showImageDetails(' . $imageId . ', \'' . addslashes($imageName) . '\')">
                        <img src="' . $imageSrc . '" class="card-img-top img-fluid" style="height: 200px; object-fit: cover;" alt="...">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">' . $imageName . '</h5>
                    </div>
                </div>
            </div>
        ';
    }
} else {
    echo "<p class='text-center'>No results found.</p>";
}

$sql->close();
$db->close();
?>
