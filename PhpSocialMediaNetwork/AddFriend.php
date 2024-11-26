<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();

?>

<?php include("./common/header.php"); ?>

<div class="container">
  <h2 class="mt-3">Add Friend</h2>
  <h6 class="mb-4">Welcome  (You can change user <a href="Login.php">here</a> if this is not you.)</h6>
  
      <div class="card">
  <h6 class="card-header">Send a friend invite</h6>
  <div class="card-body">
    
    <input type="text" class="form-control">
    <p class="card-text">Enter the ID of the user you want to be friends with.</p>
    <a href="#" class="btn" style="background-color: DarkSlateBlue; color: white;">Send Friend Request</a>
  </div>
  
</div>
<?php include('./common/footer.php'); ?>



