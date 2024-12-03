<?php
include 'dbFunctions.php';

//Validate pictureId
$pictureId = isset($_GET['pictureId']) ? intval($_GET['pictureId']) : null;

if ($pictureId) {
    $comments = getCommentsByPictureId($pictureId);
    echo json_encode($comments);
} else {
    echo json_encode([]);
}
?>
