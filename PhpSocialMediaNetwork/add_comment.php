<?php
include 'dbFunctions.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in.']);
    exit();
}

// Validate input
$authorId = $_SESSION['user']['UserId']; // Ensure you are using the correct key for UserId
$pictureId = isset($_POST['pictureId']) ? intval($_POST['pictureId']) : null;
$commentText = isset($_POST['commentText']) ? trim($_POST['commentText']) : '';

if ($pictureId && !empty($commentText)) {
    // Save the comment
    saveComment($authorId, $pictureId, $commentText);
    echo json_encode(['success' => true, 'pictureId' => $pictureId]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
}
?>