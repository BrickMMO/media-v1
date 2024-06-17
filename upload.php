<?php

// Function to compress image
function compress($source, $destination, $quality) {
    // Get image information
    $info = getimagesize($source);
    // Create image resource based on MIME type
    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);
    // Save compressed image
    imagejpeg($image, $destination, $quality);
    return $destination;
}

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if (isset($_POST["submit"])) {
    $uploadMessage = "";
    $uploadCount = 0;

    // Database connection details
    include("database.php");
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Iterate through each uploaded file
    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        // Check if file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"][$key]);
        if ($check !== false) {
            // Get the original filename
            $filename = $_FILES['image']['name'][$key];

            // Temporary path of the uploaded file
            $source = $_FILES['image']['tmp_name'][$key];

            // Destination path for the compressed image
            $compressed_image = 'compressed_' . $filename;

            // Compress the uploaded image
            compress($source, $compressed_image, 60);

            // Read compressed image content
            $imgContent = addslashes(file_get_contents($compressed_image));

            // Delete compressed image file
            unlink($compressed_image);

            // Extract tags from image name
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
            
            // Remove numbers from the filename
            $filenameWithoutNumbers = preg_replace('/\d/', '', $filenameWithoutExtension);
            
            // Filename by hyphen and remove empty elements
            $tags = array_filter(explode('-', $filenameWithoutNumbers));
            
            // Empty and numeric tags
            $tags = array_filter($tags, function($tag) {
                return !is_numeric($tag) && !empty($tag);
            });

            error_log("Extracted tags for $filename: " . implode(', ', $tags));

            // Convert tags array to comma-separated string
            $tagsString = implode(',', $tags);
            $dataTime = date("Y-m-d H:i:s");

            // Insert image content into database
            $insert = $db->query("INSERT INTO images (image, created, imageName, tags) VALUES ('$imgContent', '$dataTime', '$filename', '$tagsString')");
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
        $uploadMessage .= "$uploadCount file(s) uploaded successfully.";
        // Storing upload message in session 
        $_SESSION['upload_message'] = $uploadMessage;
        header("Location: upload-success.php");
        exit();
    } else {
        $uploadMessage .= "File upload failed, please try again.";
    }
    $db->close();
}

echo $uploadMessage;
?>
