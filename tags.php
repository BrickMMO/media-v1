<!-- Tags Section -->
<div class="col-md-3">
    <h5 class="text-dark mb-3">Tags</h5>
    <div class="d-flex flex-wrap">
        <?php
        // Database connection details
        $dbHost = 'localhost';
        $dbUsername = 'root';
        $dbPassword = 'root';
        $dbName = 'brickmmo';

        // Create connection
        $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

        // Check connection
        if ($db->connect_error) {
            // If connection fails, display error message and terminate script
            die("Connection failed: " . $db->connect_error);
        }

        // Fetch all tags from the database
        $tagsResult = $db->query("SELECT tags FROM images");
        $tags = []; // Initialize an empty array to store tags

        // If there are tags available
        if ($tagsResult->num_rows > 0) {
            // Loop through each row to extract tags
            while ($row = $tagsResult->fetch_assoc()) {
                // Extract tags from the database
                $imageTags = explode(',', $row["tags"]);
                // Filter out tags containing numeric characters
                $imageTags = array_filter($imageTags, function ($tag) {
                    return !preg_match('/[0-9]/', $tag);
                });
                // Merge extracted tags with existing tags array
                $tags = array_merge($tags, $imageTags);

                // Debug output
                error_log("Tags from database: " . print_r($imageTags, true));
            }
        }

        // Filter out duplicate tags and sort alphabetically
        $tags = array_unique($tags);
        sort($tags);

        // Loop through each tag and display as a button
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                // Generate link with tag as a parameter
                echo '<a href="?tag=' . urlencode($tag) . '" class="btn btn-outline-secondary m-1 text-dark border">' . htmlspecialchars($tag) . '</a>';
            }
        }
        ?>
    </div>
</div>
