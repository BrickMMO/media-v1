<div class="col-md-3" id="tags-id">
    <h5 class="text-dark mb-3">Tags</h5>
    <div class="d-flex flex-wrap">
        <?php
       include("database.php");
        $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        $tagsResult = $db->query("SELECT tags FROM images");
        $tags = [];

        if ($tagsResult->num_rows > 0) {
            while ($row = $tagsResult->fetch_assoc()) {
                $imageTags = explode(',', $row["tags"]);
                $imageTags = array_filter($imageTags, function ($tag) {
                    return !preg_match('/[0-9]/', $tag);
                });
                $tags = array_merge($tags, $imageTags);
            }
        }

        $tags = array_unique($tags);
        sort($tags);

        foreach ($tags as $tag) {
            if (!empty($tag)) {
                echo '<a href="javascript:void(0)" onclick="loadSection(\'image-section.php?tag=' . urlencode($tag) . '\')" class="btn btn-outline-secondary m-1 text-dark border">' . htmlspecialchars($tag) . '</a>';
            }
        }
        ?>
    </div>
</div>
