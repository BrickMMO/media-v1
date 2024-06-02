<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <!-- Search Form -->
        <form class="d-flex my-4" role="search" method="get" action="">
            <input class="form-control me-2" type="search" name="query" id="search-query" placeholder="Search" aria-label="Search" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
            <button class="btn btn-outline-success" type="button" onclick="searchImages()">Search</button>
        </form>
        <div class="row justify-content-center" id="image-container">
            <?php
            // Database connection
            $dbHost = 'localhost';
            $dbUsername = 'root';
            $dbPassword = 'root';
            $dbName = 'brickmmo';

            $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }

            // Check if a search query is set
            $query = isset($_GET['query']) ? $_GET['query'] : '';

            // Fetch images and names from the database based on the search query
            if ($query) {
                $sql = $db->prepare("SELECT id, imageName, image FROM images WHERE imageName LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
                $sql->bind_param("s", $query);
            } else {
                $sql = $db->prepare("SELECT id, imageName, image FROM images ORDER BY RAND() LIMIT 12");
            }
            $sql->execute();
            $result = $sql->get_result();

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    $imageId = $row["id"];
                    $imageName = $row["imageName"];
                    // Remove file extension from image name
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
            $db->close();
            ?>
        </div>
        <div class="row justify-content-center" id="image-details"></div>
    </div>

    <script>
        function searchImages() {
            var query = document.getElementById('search-query').value;
            // Redirect to the same page with the search query
            window.location.href = 'image-section.php?query=' + encodeURIComponent(query);
        }

        function showImageDetails(imageId, imageName) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('image-details').innerHTML = xhr.responseText;
                    // Scroll to the image details section
                    window.scrollTo(0, document.getElementById('image-details').offsetTop);
                    // Hide the image container
                    document.getElementById('image-container').style.display = 'none';
                }
            };
            xhr.open('GET', 'image-details.php?id=' + imageId + '&name=' + encodeURIComponent(imageName), true);
            xhr.send();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
