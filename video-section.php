<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Video Section</title>
    <style>
        .fullscreen-video {
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

        .fullscreen-video iframe {
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

        .video-container {
            margin-top: 20px;
        }

        .video-container .video-card {
            margin-bottom: 20px;
            cursor: pointer;
        }

        .video-container .video-card iframe {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="row">
                <div class="col-12 search-bar">
                    <div class="d-flex mb-3">
                        <input class="form-control me-2" type="search" id="query" placeholder="Search"
                            aria-label="Search">
                            <button class="btn btn-outline-success" type="button" id="searchButton">Search</button>
                    </div>
                </div>
            </div>
            <?php include 'tags-videos.php'; ?>
            <div class="col-md-9 video-container">
                <div class="row justify-content-center" id="video-container">
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
                        $sql = $db->prepare("SELECT id, videoName, videoPath FROM videos WHERE videoName LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
                        $sql->bind_param("s", $query);
                    } elseif ($tag) {
                        $sql = $db->prepare("SELECT id, videoName, videoPath FROM videos WHERE tags LIKE CONCAT('%', ?, '%') ORDER BY RAND() LIMIT 12");
                        $sql->bind_param("s", $tag);
                    } else {
                        $sql = $db->prepare("SELECT id, videoName, videoPath FROM videos ORDER BY RAND() LIMIT 12");
                    }
                    $sql->execute();
                    $result = $sql->get_result();

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

                    $sql->close();
                    $db->close();
                    ?>
                </div>
                <div class="row justify-content-center" id="video-details"></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('searchButton').addEventListener('click', performSearch);

        // Function to perform the search
        function performSearch(event) {
            var query = document.getElementById('query').value.trim(); 

            // AJAX request to load_videos.php
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('video-container').innerHTML = xhr.responseText; 
                }
            };
            xhr.open('GET', 'load_videos.php?query=' + encodeURIComponent(query), true);
            xhr.send();
        }
    });
</script>


</body>

</html>
