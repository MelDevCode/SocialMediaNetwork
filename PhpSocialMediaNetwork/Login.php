<?php
  // Include the external file with validation functions
  include 'ValidationFunctions.php';
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();

  // Check if the user is already logged in
  if (isset($_SESSION['user'])) {
    // Redirect to logout page
    header("Location: logout.php");
    exit();
  }

  extract($_POST);
  
  // Initialize error array
  $errors = [];

  // Handle form submission
  if (isset($_POST['submit'])) {

    // Collect and validate form inputs
    $userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Validate each field
    $userIdError = ValidateId($userId);
    $passwordError = ValidatePassword($password);
    
    // Store errors in the array
    if ($userIdError) $errors['userId'] = $userIdError;
    if ($passwordError) $errors['password'] = $passwordError;
    
    if (empty($errors)) {
        try {
            // Hash the password (in case it's plain text)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Validate the login and get user data
            $user = validateLogin($userId, $password);

        } catch (Exception $ex) {
            die("The system is currently not available, try again later");
        }

        if ($user == null) {
            $errors['password'] = 'Incorrect User ID and Password Combination!';
        } else {
            // Store the user data in the session
            $_SESSION['user'] = [
              'UserId' => $user['UserId'], // Store the UserId
              'Name' => $user['Name'],     // Store other necessary user details
              // Add more user data if needed
            ];

            // Redirect to the target page if set; otherwise, default to MyPictures
            $redirectTo = $_SESSION['redirect_to'] ?? 'MyPictures.php';
            unset($_SESSION['redirect_to']); // Clear the target page after redirection
            header("Location: $redirectTo");
            exit();
        }
    }
    
  }
  
?>

<?php include("./common/header.php"); ?>
<div class="container">
  <section class="h-100 gradient-form" style="">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-4 mx-md-3">

                <div class="text-center">
                  <h2 class="pb-1">Login to your Account</h2>
                </div>

                <form action="Login.php" method="POST">
                  <div class="form-outline mb-1">
                    <label class="form-label"></label>
                    <input type="text" class="form-control" name="userId" placeholder="User Id"
    value="<?php echo isset($_SESSION['userId']) ? htmlspecialchars($_SESSION['userId']) : (isset($_POST['userId']) ? htmlspecialchars($_POST['userId']) : ''); ?>">
                    <!-- Error Message for Student ID -->
                    <?php if (isset($errors['userId'])): ?>
                    <span class="error text-danger"><?php echo $errors['userId']; ?></span>
                    <?php endif; ?>
                  </div>

                  <div class="form-outline mb-1">
                    <label class="form-label"></label>
                    <input type="password" class="form-control" name="password" placeholder="Password"
      value="<?php echo isset($_SESSION['password']) ? htmlspecialchars($_SESSION['password']) : (isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''); ?>">
                <!-- Error Message for Password -->
                <?php if (isset($errors['password'])): ?>
                    <span class="error text-danger"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
                  </div>
                  
                  <div class="d-grid mt-3">
                    <button class="btn" type="submit" name="submit" style="background-color: DarkSlateBlue; color: white;">Login</button>
                  </div>
                   
                  <div class="d-flex align-items-center justify-content-left mt-1">
                    <p class="mb-0 me-2">You don't have an account?<a href="NewUser.php"> Create one</a></p>
                  </div>
                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center">
              <div class="text-white px-2 py-3 p-md-4 mx-md-3">
                <img src="Common/img/welcome4.jpg" alt="Welcome Image" 
       style="width: 80vw; max-width: 100%; height: auto; margin-top: 20px;"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
  
  
</div>
<?php include('./common/footer.php'); ?>

