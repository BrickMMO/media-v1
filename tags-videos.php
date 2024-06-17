<div class="col-md-3" id="tags-id">
    <h5 class="text-dark mb-3">Tags</h5>
    <div class="d-flex flex-wrap">
        <?php
        include("database.php");

        $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        // Fetch distinct tags from the 'videos' table
        $tagsQuery = "SELECT DISTINCT tags FROM videos";
        $tagsResult = $db->query($tagsQuery);

        if ($tagsResult->num_rows > 0) {
            // Loop through each row to extract and display tags
            while ($row = $tagsResult->fetch_assoc()) {
                // Extract tags from the current row
                $videoTags = explode(',', $row["tags"]);
                
                // Loop through each tag and display as a button
                foreach ($videoTags as $tag) {
                    // Trim tag and check if it's not empty
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        echo '<a href="javascript:void(0)" onclick="loadSection(\'video-section.php?tag=' . urlencode($tag) . '\')" class="btn btn-outline-secondary m-1 text-dark border">' . htmlspecialchars($tag) . '</a>';
                    }
                }
            }
        } else {
            echo "<p>No tags available.</p>";
        }

        // Close database connection
        $db->close();
        ?>
    </div>
</div>
