<?php
    include 'dbFunctions.php';

    // Validate albumId
    $albumId = isset($_GET['albumId']) ? intval($_GET['albumId']) : null;

    if ($albumId) {
        $photos = getPhotosByAlbumId($albumId);
        echo json_encode($photos);
    } else {
        echo json_encode([]);
    }
?>