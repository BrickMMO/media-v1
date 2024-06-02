<?php

// compressing image as the size of  image is more than 1MB
function compress($source, $destination, $quality) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);
    imagejpeg($image, $destination, $quality);
    return $destination;
}

session_start();

// User Logged in?
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST["submit"])) {
    $uploadMessage = "";
    $uploadCount = 0;

    // Iterate through each uploaded file
    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        $check = getimagesize($_FILES["image"]["tmp_name"][$key]);
        if ($check !== false) {
            $image = $_FILES['image']['tmp_name'][$key];
            
            // Get the original filename
            $filename = $_FILES['image']['name'][$key];

            // Compress the uploaded image
            $compressed_image = 'compressed_' . $filename;
            compress($image, $compressed_image, 60);
            $imgContent = addslashes(file_get_contents($compressed_image));
            unlink($compressed_image);

            // DB details
            $dbHost = 'localhost';
            $dbUsername = 'root';
            $dbPassword = 'root';
            $dbName = 'brickmmo';

            // Create connection 
            $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

            // Check connection
            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }

            $dataTime = date("Y-m-d H:i:s");

            // Insert image content into database
            $insert = $db->query("INSERT into images (image, created, imageName) VALUES ('$imgContent', '$dataTime','$filename')");
            if ($insert) {
                $uploadCount++;
            }
        }
    }

    if ($uploadCount > 0) {
        $uploadMessage = "$uploadCount file(s) uploaded successfully.";
    } else {
        $uploadMessage = "File upload failed, please try again.";
    }
}

echo $uploadMessage;
?>
