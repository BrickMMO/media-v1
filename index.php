<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>BrickMMO - Media</title>
</head>
 <style>
        #showImages:hover,
        #showVideos:hover {
            background-color: #dc3545; 
            border-color: #dc3545;
            color: #fff; 
        }
    </style>
<body>
    <?php include('./navbar.php'); ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-center mb-4">
            <button class="btn btn-secondary mx-2 btn-warning" id="showImages">Images</button>
            <button class="btn btn-primary mx-2 btn-warning" id="showVideos">Videos</button>
        </div>

        <div id="contentSection">
            <?php
            if (isset($_GET['tag']) || isset($_GET['query'])) {
                include('./image-section.php');
            } else {
                include('./image-section.php');
            }
            ?>
        </div>
    </div>

    <?php include('./loading.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    // Default is image section
        document.addEventListener('DOMContentLoaded', function() {
            loadSection('image-section.php'); 
            setActiveButton('showImages'); 
        });

        document.getElementById('showImages').addEventListener('click', function() {
            loadSection('image-section.php');
            setActiveButton('showImages');
        });

        document.getElementById('showVideos').addEventListener('click', function() {
            loadSection('video-section.php');
            setActiveButton('showVideos');
        });

        function loadSection(section) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', section, true);

            // Show loading modal
            var loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'), {
                backdrop: 'static',
                keyboard: false
            });
            loadingModal.show();

            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('contentSection').innerHTML = xhr.responseText;
                } else {
                    console.error('Error loading section:', xhr.status, xhr.statusText);
                }
                loadingModal.hide();
            };

            xhr.onerror = function() {
                console.error('Request error.');
                loadingModal.hide();
            };

            xhr.send();
        }

        function setActiveButton(activeButtonId) {
            document.getElementById('showImages').classList.remove('btn-primary');
            document.getElementById('showImages').classList.add('btn-secondary');
            document.getElementById('showVideos').classList.remove('btn-primary');
            document.getElementById('showVideos').classList.add('btn-secondary');

            document.getElementById(activeButtonId).classList.remove('btn-secondary');
            document.getElementById(activeButtonId).classList.add('btn-primary');
        }
    </script>
</body>

</html>
