<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted
if (isset($_POST["submit-video"])) {
    $uploadMessage = "";
    $uploadCount = 0;

   include("database.php");
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Get the video name and path from the form
    $filename = $_POST["video-name"];
    $filepath = $_POST["video"];

    // Extract tags from video name
    $tags = explode('-', $filename);

    // Remove numeric tags and empty elements
    $tags = array_filter($tags, function($tag) {
        return !is_numeric($tag) && !empty($tag);
    });

    // Convert tags array to comma-separated string
    $tagsString = implode(',', $tags);
    $uploadTime = date("Y-m-d H:i:s");

    // Insert video details into database, including tags
    $insert = $db->query("INSERT INTO videos (videoName, uploadTime, videoPath, tags) VALUES ('$filename', '$uploadTime', '$filepath', '$tagsString')");

    if ($insert) {
        $uploadCount++;
        $_SESSION['upload_message'] = "Video uploaded successfully.";
        header("Location: upload-success.php");
        exit();
    } else {
        $uploadMessage = "Failed to upload video: " . $db->error;
    }

    $db->close();
}

echo $uploadMessage;
?>