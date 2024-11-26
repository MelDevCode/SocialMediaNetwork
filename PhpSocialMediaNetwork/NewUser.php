<?php
  // Include the external file with validation functions
  include 'ValidationFunctions.php';
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();
  extract($_POST);

  // Initialize error array
  $errors = [];

  // Handle form submission
  if (isset($_POST['submit'])) {
    // Collect and validate form inputs
    $userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $passwordReenter = filter_input(INPUT_POST, 'passwordReenter', FILTER_SANITIZE_STRING);

    // Validate each field
    $userIdError = ValidateId($userId);
    $nameError = ValidateName($name);
    $phoneError = ValidatePhone($phone);
    $passwordError = ValidatePassword($password);
    $passwordReenterError = ValidatePasswordReenter($password, $passwordReenter);
    
    // Store errors in the array
    if ($userIdError) $errors['userId'] = $userIdError;
    if ($nameError) $errors['name'] = $nameError;
    if ($phoneError) $errors['phone'] = $phoneError;
    if ($passwordError) $errors['password'] = $passwordError;
    if ($passwordReenterError) $errors['passwordReenter'] = $passwordReenterError;

    if (empty($errors)) {
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Attempt to add the new user with the hashed password
            addNewUser($userId, $name, $phone, $hashedPassword);

            // Redirect upon successful registration
            header("Location: MyPictures.php");
            exit();
        } catch (Exception $ex) {
            $errors['userId'] = 'A student with this ID already signed up.';
        }
    }
  }

?>

<?php include("./common/header.php"); ?>

<div class="container d-flex flex-column align-items-center justify-content-center" >
  <div class="card mt-3 mb-3 px-5" style="width: 35rem;">
    <h2 class="text-center mt-3">Create an Account</h2>
    <form action="NewUser.php" method="POST">
      <!-- User ID -->
      <div class="row mb-3 align-items-center">
        <label class="col-4 text-start">User ID</label>
        <div class="col-8">
          <input type="text" class="form-control" name="userId" placeholder="Enter your user ID"
  value="<?php echo isset($_SESSION['userId']) ? htmlspecialchars($_SESSION['userId']) : (isset($_POST['userId']) ? htmlspecialchars($_POST['userId']) : ''); ?>">
            <!-- Error Message for Student ID -->
                <?php if (isset($errors['userId'])): ?>
                <span class="error text-danger"><?php echo $errors['userId']; ?></span>
                <?php endif; ?>
        </div>
      </div>

      <!-- Username -->
      <div class="row mb-3 align-items-center">
        <label class="col-4 text-start">Username</label>
        <div class="col-8">
          <input type="text" class="form-control" name="name" placeholder="Enter your username"
  value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>">
            <!-- Error Message for Name -->
                <?php if (isset($errors['name'])): ?>
                    <span class="error text-danger"><?php echo $errors['name']; ?></span>
                <?php endif; ?>
        </div>
      </div>

      <!-- Phone Number -->
      <div class="row mb-3 align-items-center">
        <label class="col-4 text-start">Phone Number</label>
        <div class="col-8">
          <input type="text" class="form-control" name="phone" placeholder="Enter your phone number"
  value="<?php echo isset($_SESSION['phone']) ? htmlspecialchars($_SESSION['phone']) : (isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''); ?>">
            <!-- Error Message for Phone -->
                <?php if (isset($errors['phone'])): ?>
                    <span class="error text-danger"><?php echo $errors['phone']; ?></span>
                <?php endif; ?>
        </div>
      </div>

      <!-- Password -->
      <div class="row mb-3 align-items-center">
        <label class="col-4 text-start">Password</label>
        <div class="col-8">
          <input type="password" class="form-control" name="password" placeholder="Enter your password"
  value="<?php echo isset($_SESSION['password']) ? htmlspecialchars($_SESSION['password']) : (isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''); ?>">
            <!-- Error Message for Password -->
                <?php if (isset($errors['password'])): ?>
                    <span class="error text-danger"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
        </div>
      </div>

      <!-- Repeat Password -->
      <div class="row mb-3 align-items-center">
        <label class="col-4 text-start">Repeat Password</label>
        <div class="col-8">
          <input type="password" class="form-control" name="passwordReenter" placeholder="Repeat your password"
  value="<?php echo isset($_SESSION['passwordReenter']) ? htmlspecialchars($_SESSION['passwordReenter']) : (isset($_POST['passwordReenter']) ? htmlspecialchars($_POST['passwordReenter']) : ''); ?>">
            <!-- Error Message for Password Reenter -->
                <?php if (isset($errors['passwordReenter'])): ?>
                    <span class="error text-danger"><?php echo $errors['passwordReenter']; ?></span>
                <?php endif; ?>
        </div>
      </div>

      <!-- Register Button -->
      <div class="d-grid mt-4">
        <button class="btn" type="submit" name="submit" style="background-color: DarkSlateBlue; color: white;">Register</button>
      </div>

      <!-- Already Have an Account -->
      <div class="text-center mt-2 mb-2">
        <p class="mb-0">You already have an account? <a href="Login.php">Login here</a></p>
      </div>
    </form>
  </div>
</div>



<?php include('./common/footer.php'); ?>

