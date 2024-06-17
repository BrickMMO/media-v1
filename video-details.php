<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .details-container {
            overflow-y: auto;
            max-height: calc(100vh - 30px);
        }

        .tag-badge {
            margin-right: 5px;
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>
    <div class="container">
        <form class="d-flex mt-3 mb-3">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];

                    include("database.php");
                    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
                    if ($db->connect_error) {
                        die("Connection failed: " . $db->connect_error);
                    }

                    // Fetching video details
                    $sql = $db->prepare("SELECT videoPath, videoName, tags FROM videos WHERE id = ?");
                    $sql->bind_param("i", $id);
                    $sql->execute();
                    $result = $sql->get_result();

                    
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Video details
                        $videoPath = $row["videoPath"];
                        $videoName = $row["videoName"];
                        $tags = explode(',', $row["tags"]);

                        echo '
                            <div class="video-container">
                                <iframe class="embed-responsive-item" src="' . $videoPath . '" allowfullscreen></iframe>
                            </div>
                        ';

                       
                    } else {
                        echo "<p class='text-center'>No video found.</p>";
                    }

                    $db->close();
                } else {
                    echo "<p class='text-center'>Invalid video ID.</p>";
                }
                ?>
            </div>
            <div class="col-md-4">
                <?php
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];

                    include("database.php");
                    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
                    if ($db->connect_error) {
                        die("Connection failed: " . $db->connect_error);
                    }

                    // Fetching video
                    $sql = $db->prepare("SELECT videoName FROM videos WHERE id = ?");
                    $sql->bind_param("i", $id);
                    $sql->execute();
                    $result = $sql->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $videoName = $row["videoName"];
                        echo '<div>
                                <h2>' . $videoName . '</h2>
                                <a href="' . $videoPath . '" class="btn btn-primary mt-3" target="_blank">Go to the video link</a>
                                <div class="mt-3">
                                    <h4>Tags:</h4>';
                        foreach ($tags as $tag) {
                            echo '<span class="badge bg-warning tag-badge">' . trim($tag) . '</span>';
                        }
                        echo '</div>
                    </div>';
                    } else {
                        echo "<p class='text-center'>No video found.</p>";
                    }

                    $db->close();
                } else {
                    echo "<p class='text-center'>Invalid video ID.</p>";
                }
                ?>
            </div>
        </div>
    </div>

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>