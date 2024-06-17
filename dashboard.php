<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// File Uploading (Images)
if (isset($_POST["submit"])) {
    $uploadMessage = "";
    $uploadCount = 0;

    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        $check = getimagesize($_FILES["image"]["tmp_name"][$key]);
        if ($check !== false) {
            $image = $_FILES['image']['tmp_name'][$key];
            $imgContent = addslashes(file_get_contents($image));
            $filename = $_FILES['image']['name'][$key];

            $dataTime = date("Y-m-d H:i:s");
            $insert = $db->query("INSERT into images (image, created, imageName) VALUES ('$imgContent', '$dataTime', '$filename')");
            if ($insert) {
                $uploadCount++;
            } else {
                $uploadMessage .= "Failed to upload $filename: " . $db->error . "<br>";
            }
        } else {
            $uploadMessage .= "File $filename is not a valid image.<br>";
        }
    }

    if ($uploadCount > 0) {
        $uploadMessage = "$uploadCount file(s) uploaded successfully.";
    } else {
        $uploadMessage = "File upload failed, please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .d-none {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="text-center">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Your email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <form action="upload.php" method="post" enctype="multipart/form-data" class="border p-4 rounded">
                    <div class="mb-3">
                        <label for="image" class="form-label">Select images to upload:</label>
                        <input type="file" name="image[]" class="form-control" id="image" multiple required>
                    </div>
                    <div class="d-grid">
                        <input type="submit" name="submit" value="UPLOAD" class="btn btn-primary btn-warning">
                    </div>
                </form>
                <?php if (isset($uploadMessage)) {
                    echo "<div class='alert alert-info mt-3'>$uploadMessage</div>";
                } ?>
            </div>
            <div class="col-md-6">
                <form action="upload-video.php" method="post" enctype="multipart/form-data" class="border p-4 rounded">
                    <div class="mb-3">
                        <label for="video" class="form-label">Paste video link to upload:</label>
                        <input type="text" name="video" class="form-control" id="video" required>
                        <label for="video-name" class="form-label">Video Title</label>
                        <input type="text" name="video-name" class="form-control" id="video-name" required>
                    </div>
                    <div class="d-grid">
                        <input type="submit" name="submit-video" value="UPLOAD" class="btn btn-primary btn-warning">
                    </div>
                </form>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <button id="show-images" class="btn btn-primary btn-warning mb-4">Uploaded Images</button>
                <button id="show-videos" class="btn btn-primary btn-warning mb-4">Uploaded Videos</button>

                <div id="image-container" class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    $sql = "SELECT images.id, images.imageName, images.image, IFNULL(downloads.download_count, 0) as downloadCount 
                            FROM images 
                            LEFT JOIN downloads ON images.id = downloads.image_id";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $imageId = $row["id"];
                            $imageName = $row["imageName"];
                            $imageData = $row["image"];
                            $downloadCount = $row["downloadCount"];
                            $imageDataEncoded = base64_encode($imageData);
                            $imageSrc = 'data:image/jpeg;base64,' . $imageDataEncoded;
                            echo '
                            <div class="col">
                                <div class="card h-100">
                                    <img src="' . $imageSrc . '" class="card-img-top" style="height: 200px; object-fit: cover;" alt="' . $imageName . '">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $imageName . '</h5>
                                        <p class="card-text">Downloads: ' . $downloadCount . '</p>
                                        <div class="d-grid gap-2">
                                            <a href="admin-edit.php?id=' . $imageId . '" class="btn btn-primary">Edit</a>
                                            <a href="admin-delete.php?id=' . $imageId . '" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                        }
                    } else {
                        echo '<p class="text-center">No images uploaded yet.</p>';
                    }
                    ?>
                </div>

                <div id="video-container" class="row row-cols-1 row-cols-md-3 g-3 d-none">
                    <?php
                    $sql = "SELECT * FROM videos";
                    $result = $db->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $videoId = $row["id"];
                            $videoName = $row["videoName"];
                            $videoPath = $row["videoPath"];
                            echo '
                            <div class="col mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $videoName . '</h5>
                                        <div class="embed-responsive embed-responsive-16by9">
                                            <iframe class="embed-responsive-item" src="' . $videoPath . '" allowfullscreen></iframe>
                                        </div>
                                        <div class="d-grid mt-3 gap-2">
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
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('show-images').addEventListener('click', function() {
            document.getElementById('image-container').classList.remove('d-none');
            document.getElementById('video-container').classList.add('d-none');
        });

        document.getElementById('show-videos').addEventListener('click', function() {
            document.getElementById('image-container').classList.add('d-none');
            document.getElementById('video-container').classList.remove('d-none');
        });
    </script>
</body>
</html>
<?php
session_start();
// User Logged in?
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


include("database.php");
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// File Uploading (Images) 
if (isset($_POST["submit"])) {
    $uploadMessage = "";
    $uploadCount = 0;

    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        $check = getimagesize($_FILES["image"]["tmp_name"][$key]);
        if ($check !== false) {
            $image = $_FILES['image']['tmp_name'][$key];
            $imgContent = addslashes(file_get_contents($image));
            $filename = $_FILES['image']['name'][$key];

            $dataTime = date("Y-m-d H:i:s");
            $insert = $db->query("INSERT into images (image, created, imageName) VALUES ('$imgContent', '$dataTime', '$filename')");
            if ($insert) {
                $uploadCount++;
            } else {
                $uploadMessage .= "Failed to upload $filename: " . $db->error . "<br>";
            }
        } else {
            $uploadMessage .= "File $filename is not a valid image.<br>";
        }
    }

    if ($uploadCount > 0) {
        $uploadMessage = "$uploadCount file(s) uploaded successfully.";
    } else {
        $uploadMessage = "File upload failed, please try again.";
    }
}
?>

