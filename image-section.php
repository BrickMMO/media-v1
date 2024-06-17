<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Image Section</title>
    <style>
        .fullscreen-image {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .fullscreen-image img {
            max-width: 90%;
            max-height: 90%;
            margin: auto;
            display: block;
        }

        #tags-id.hidden {
            display: none;
        }

        .col-md-3 {
            flex: 0 0 45%;
        }

        .col-md-9 {
            flex: 0 0 55%;
        }

        .search-form {
            width: 100%;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="row">
                <div class="col-12 search-bar">
                    <form class="d-flex mb-3" onsubmit="performSearch(event)">
                        <input class="form-control me-2" type="search" id="query" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
            <?php include 'tags.php'; ?>
            <div class="col-md-9">

                <div class="row justify-content-center" id="image-container">
                    <?php
                    $query = isset($_GET['query']) ? $_GET['query'] : '';
                    $tag = isset($_GET['tag']) ? $_GET['tag'] : '';

                   include("database.php");
                    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
                    if ($db->connect_error) {
                        die("Connection failed: " . $db->connect_error);
                    }

                    // SQL query based on search or tag
                    if ($query) {
                        $sql = $db->prepare("SELECT id, imageName, image FROM images WHERE imageName LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
                        $sql->bind_param("s", $query);
                    } elseif ($tag) {
                        $sql = $db->prepare("SELECT id, imageName, image FROM images WHERE tags LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
                        $sql->bind_param("s", $tag);
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
                </div>
                <div class="row justify-content-center" id="image-details"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function performSearch(event) {
            event.preventDefault();
            var query = document.getElementById('query').value;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('image-container').innerHTML = xhr.responseText;
                }
            };
            xhr.open('GET', 'load_images.php?query=' + encodeURIComponent(query), true);
            xhr.send();
        }

        function showImageDetails(imageId, imageName) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Hide the tags section
                    document.getElementById('tags-id').classList.add('hidden');

                    // Display the image details
                    document.getElementById('image-details').innerHTML = xhr.responseText;
                    window.scrollTo(0, document.getElementById('image-details').offsetTop);
                    document.getElementById('image-container').style.display = 'none';
                }
            };
            xhr.open('GET', 'image-details.php?id=' + imageId + '&name=' + encodeURIComponent(imageName), true);
            xhr.send();
        }
    </script>

</body>

</html>
