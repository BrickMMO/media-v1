<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container-fluid">
        <div class="bg-light p-4 rounded-3 w-100">
            <div class="row g-0">
                <?php
                $db = new mysqli('sql204.infinityfree.com', 'if0_36738867', 'gINbX7lzWbwiMw', 'if0_36738867_brickmmo');
                if ($db->connect_error) { die("Connection failed: " . $db->connect_error); }

                $id = isset($_GET['id']) ? $_GET['id'] : null;
                if ($id) {
                    $sql = $db->prepare("SELECT imageName, image, tags FROM images WHERE id = ?");
                    $sql->bind_param("i", $id);
                    $sql->execute();
                    $result = $sql->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $imageName = htmlspecialchars($row["imageName"]);
                        $imagePath = 'data:image/jpeg;base64,' . base64_encode($row["image"]);
                        $tags = explode(',', $row["tags"]);
                        $imageSize = getimagesizefromstring($row["image"]);

                        echo '
                        <div class="col-md-8">
                            <img src="' . $imagePath . '" class="img-fluid" alt="' . $imageName . '">
                        </div>
                        <div class="col-md-4 details-section p-3">
                            <h2>' . $imageName . '</h2>
                            <p><strong>Tags:</strong> ';

                        foreach ($tags as $tag) {
                            echo '<span class="badge bg-warning text-dark me-1 mb-1">' . htmlspecialchars(trim($tag)) . '</span>';
                        }

                        echo '</p>
                            <p>Dimensions: ' . $imageSize[0] . ' x ' . $imageSize[1] . '</p>
                            <p>File Type: ' . $imageSize['mime'] . '</p>
                            <a href="download.php?id=' . $id . '" class="btn btn-primary">Download</a>
                        </div>';
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
