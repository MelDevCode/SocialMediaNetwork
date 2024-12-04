<?php 
 include 'dbFunctions.php';
// Get the JSON string from the URL and decode it
    $rowsToSave = json_decode($_GET['rowsToSave'], true); // true converts it to an associative array

    $updateAlbums = updateAlbum($rowsToSave);
    // Redirect to MyAlbums.php
    header("Location: MyAlbums.php");
    exit();
?>