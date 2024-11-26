<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();

  // Check if the user is logged in
  if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userName = $user['Name'];  // Get the student's name
    } else {
        header("Location: Login.php");
        exit();
    }
?>

<?php include("./common/header.php"); ?>

<div class="container">
    <h2 class="mt-3">My Albums</h2>
    <h6>Welcome <?php echo htmlspecialchars($userName); ?> (You can change user <a href="Login.php">here</a> if this is not you.)</h6>
    
    <!-- Create Album Button -->
    <div class="mt-3 d-flex justify-content-end">
        <a href="AddAlbum.php">
            <button type="submit" class="btn btn-outline-secondary">Create a New Album</button>
        </a>
    </div>
    
    <!-- Album Table -->
    <table class="table align-middle mb-0 bg-white">
        <thead class="bg-light">
            <tr>
                <th>Album Title</th>
                <th>Number of Pictures</th>
                <th>Accessibility</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-link">My Trip to China</button>
                    </div>
                </td>
                <td>
                    <p class="fw-normal mb-1">14</p>
                </td>
                <td>
                    <select class="form-select" id="semesterDropdown" style="padding: 5px;" name="semester">
                        <option value="">Select an option</option>
                        <option value=""></option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger">
                        Delete
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
    
    <!-- Save Changes Button -->
    <div class="mt-3 d-flex justify-content-end">
        <button type="reset" class="btn" style="background-color: DarkSlateBlue; color: white;">Save Changes</button>
    </div>
</div>


<?php include('./common/footer.php'); ?>

