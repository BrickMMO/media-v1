<?php
// Check if image ID is provided
if (isset($_GET['id'])) {
    // Database connection details
    $dbHost = 'localhost';
    $dbUsername = 'root';
    $dbPassword = 'root';
    $dbName = 'brickmmo';

    // Create a new database connection
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check if the connection is successful
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Get the image ID from the URL and sanitize it as an integer
    $id = intval($_GET['id']);

    // Prepare and execute a query to fetch the image data from the database based on the provided ID
    $sql = $db->prepare("SELECT image, imageName FROM images WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $result = $sql->get_result();

    // If the query returns at least one row
    if ($result->num_rows > 0) {
        // Fetch the row
        $row = $result->fetch_assoc();

        // Get the image data and name
        $imageData = $row['image'];
        $imageName = $row['imageName'];

        // Set headers for file transfer
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($imageName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($imageData));

        // Output the image data to the browser
        echo $imageData;

        // Update download count in the downloads table
        $updateDownload = $db->prepare("INSERT INTO downloads (image_id, download_count) VALUES (?, 1)
                                        ON DUPLICATE KEY UPDATE download_count = download_count + 1");
        $updateDownload->bind_param("i", $id);
        $updateDownload->execute();

        // Exit to prevent further output
        exit;
    } else {
        // If no image is found for the provided ID, display an error message
        echo "Image not found.";
    }

    // Close the database connection
    $db->close();
} else {
    // If no image ID is provided in the URL, display a message
    echo "No image ID provided.";
}
