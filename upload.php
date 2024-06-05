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
    // Return the destination path
    return $destination;
}

// Start session to maintain session state
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if (isset($_POST["submit"])) {
    // Initialize variables
    $uploadMessage = "";
    $uploadCount = 0;

    // Database connection details
    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = 'root';
    $dbName = 'brickmmo';

    // Create database connection 
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check database connection
    if ($db->connect_error) {
        // If connection fails, display error message and terminate script
        die("Connection failed: " . $db->connect_error);
    }

    // Iterate through each uploaded file
    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
        // Check if file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"][$key]);
        if ($check !== false) {
            // Get the original filename
            $filename = $_FILES['image']['name'][$key];

            // Compress the uploaded image
            $compressed_image = 'compressed_' . $filename;
            compress($image, $compressed_image, 60);
            // Read compressed image content
            $imgContent = addslashes(file_get_contents($compressed_image));
            // Delete compressed image file
            unlink($compressed_image);

            // Extract tags from image name
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
            // Remove numbers from the filename
            $filenameWithoutNumbers = preg_replace('/\d/', '', $filenameWithoutExtension);
            // Split the filename by hyphen and remove empty elements
            $tags = array_filter(explode('-', $filenameWithoutNumbers));
            // Filter out empty and numeric tags
            $tags = array_filter($tags, function($tag) {
                return !is_numeric($tag) && !empty($tag);
            });

            // Debug statement to check extracted tags
            error_log("Extracted tags for $filename: " . implode(', ', $tags));

            // Convert tags array to comma-separated string
            $tagsString = implode(',', $tags);

            // Get current date and time
            $dataTime = date("Y-m-d H:i:s");

            // Insert image content into database
            $insert = $db->query("INSERT INTO images (image, created, imageName, tags) VALUES ('$imgContent', '$dataTime', '$filename', '$tagsString')");
            // Check if insertion was successful
            if ($insert) {
                $uploadCount++;
            } else {
                // If insertion fails, append error message to upload message
                $uploadMessage .= "Failed to upload $filename: " . $db->error . "<br>";
            }
        } else {
            // If file is not a valid image, append error message to upload message
            $uploadMessage .= "File $filename is not a valid image.<br>";
        }
    }

    // Construct final upload message
    if ($uploadCount > 0) {
        $uploadMessage .= "$uploadCount file(s) uploaded successfully.";
    } else {
        $uploadMessage .= "File upload failed, please try again.";
    }

    // Close database connection
    $db->close();
}

// Echo upload message
echo $uploadMessage;
?>
