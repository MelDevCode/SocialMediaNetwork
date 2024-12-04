<?php 
    include 'dbFunctions.php';
    $albumId = $_GET['Album_Id'];

    $deletePicture = deletePicture($albumId);
    $deleteAlbum = deleteAlbum($albumId);
    
    
    // Redirect to MyAlbums.php
    header("Location: MyAlbums.php");
    exit();

?>