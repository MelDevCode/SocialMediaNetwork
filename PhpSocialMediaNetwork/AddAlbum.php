<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();

?>

<?php include("./common/header.php"); ?>

<div class="container" style="width: 60rem;">
    <h2 class="mt-3">Create New Album</h2>
    <h6>Welcome Back Melissa (You can change user <a href="Login.php">here</a> if this is not you.)</h6>
    
    <table class="table align-middle">
        <tbody>
            <tr>
                <td class="fw-bold">Title</td>
                <td>
                    <input type="text" class="form-control" placeholder="Enter album name">
                </td>
            </tr>
            <tr>
                <td class="fw-bold">Accesibility</td>
                <td>
                    <select class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                  </select>
                </td>
            </tr>
            <tr>
                <td class="fw-bold">Description</td>
                <td>
                    <textarea class="form-control" placeholder="Enter description" rows="3"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
  <!-- Buttons -->
    <div class="mt-3">
      <button type="submit" name="submit" class="btn" style="background-color: DarkSlateBlue; color: white;">Submit</button> 
      <button type="submit" name="clear" class="btn" style="background-color: DarkSlateBlue; color: white;">Clear</button> 
    </div>
  
</div>

<?php include('./common/footer.php'); ?>

