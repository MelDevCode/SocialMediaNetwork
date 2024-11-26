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

<div class="container text-center">
    <!-- Page Heading -->
    <h2 class="mt-3">My Friends</h2>
    <h6>Welcome <?php echo htmlspecialchars($userName); ?> (You can change the user <a href="Login.php">here</a> if this is not you.)</h6>
    
    <!-- List of Friends Card -->
    <div class="card mt-3 mx-auto" style="width: 55rem;">
        <h5 class="card-header text-center">List of Friends</h5> 
        <div class="card-body">
            <table class="table align-middle mb-0 bg-white">
                <thead class="bg-light text-start">
                    <tr>
                        <th>Name</th>
                        <th>Shared Albums</th>
                        <th>Defriend</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-link">Ryomen Sukuna</button>
                            </div>
                        </td>
                        <td class="text-start">
                            <p class="fw-normal mb-1">0</p>
                        </td>
                        <td class="text-start">
                            <input type="checkbox">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <button type="submit" value="Submit" name="clear" class="btn mt-3" style="background-color: DarkSlateBlue; color: white;">Deny Selected</button>
            </div>
        </div>
    </div>
    
    <!-- Friend Requests Card -->
    <div class="card mt-3 mx-auto" style="width: 55rem;">
        <h5 class="card-header text-center">Friend Requests</h5> 
        <div class="card-body">
            <table class="table align-middle mb-0 bg-white">
                <thead class="bg-light text-start">
                    <tr>
                        <th>Name</th>
                        <th>Accept or Deny</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-link">Megumi Fushiguro</button>
                            </div>
                        </td>
                        <td class="text-start">
                            <input type="checkbox">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" value="Submit" name="submit" class="btn me-2" style="background-color: DarkSlateBlue; color: white;">Accept Selected</button>
                <button type="submit" value="Submit" name="clear" class="btn btn-outline-secondary">Deny Selected</button>
            </div>
        </div>
    </div>
</div>

<?php include('./common/footer.php'); ?>

