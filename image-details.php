<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <div class="container-fluid">
        <div class="row no-gutters">
            <?php
            // Database connection details
            $dbHost = 'localhost';
            $dbUsername = 'root';
            $dbPassword = 'root';
            $dbName = 'brickmmo';

            // Create a new database connection
            $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

            // Check if the connection is successful
            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }

            // Fetch image details based on ID
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = $db->prepare("SELECT imageName, image, tags FROM images WHERE id = ?");
                $sql->bind_param("i", $id);
                $sql->execute();
                $result = $sql->get_result();

                // If there are results
                if ($result->num_rows > 0) {
                    // Fetch the row
                    $row = $result->fetch_assoc();

                    // Get image details
                    $imageName = $row["imageName"];
                    $imageData = $row["image"];
                    $imagePath = 'data:image/jpeg;base64,' . base64_encode($imageData);
                    $tags = $row["tags"];

                    // Get image dimensions and type
                    $tempImagePath = tempnam(sys_get_temp_dir(), 'img');
                    file_put_contents($tempImagePath, $imageData);
                    $imageSize = getimagesize($tempImagePath);
                    unlink($tempImagePath);

                    $width = $imageSize[0];
                    $height = $imageSize[1];
                    $mimeType = $imageSize['mime'];

                    // Display image details
                    echo '
                    <div class="col-md-8 d-flex justify-content-center align-items-center" style="height: 100vh; overflow: hidden;">
                        <img src="' . $imagePath . '" class="img-fluid" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="...">
                    </div>
                    <div class="col-md-4 p-4" style="height: 100vh; overflow-y: auto;">
                        <h2>' . $imageName . '</h2>
                        <p>Tags: ' . htmlspecialchars($tags) . '</p>
                        <p>Dimensions: ' . $width . ' x ' . $height . '</p>
                        <p>File Type: ' . $mimeType . '</p>
                        <a href="download.php?id=' . $id . '" class="btn btn-primary">Download</a>
                    </div>
                    ';
                } else {
                    echo "<p class='text-center'>No image found.</p>";
                }
            } else {
                echo "<p class='text-center'>Invalid image ID.</p>";
            }
            $db->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
