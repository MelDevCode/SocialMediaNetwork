<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  include_once 'ValidationFunctions.php';
  // Start session
  session_start();

  extract($_POST);
  $errors = [];
  $messages = [];
  if(isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
  } else {
    header("Location: Login.php");
    exit();
  }

  if (isset($sendFriendRequest)) {
    $friendRequesteeId = filter_input(INPUT_POST, 'friendRequesteeId', FILTER_UNSAFE_RAW);

    try {
      $userIdError = ValidateId($friendRequesteeId);

      if ($userIdError) {
        $errors['userId'] = $userIdError;
      }

      if(!getUserId($friendRequesteeId)) {
        $errors['userId'] = 'User Id: '.$friendRequesteeId.' does not exist!';
      }
      
      if(validateFriendship($userId, $friendRequesteeId)) {
        $errors['friendship'] = 'You are already a friend of user: '.$friendRequesteeId;
      }

      if(validateFriendship($friendRequesteeId, $userId)) {
        $errors['friendship'] = 'You are already a friend of user: '.$friendRequesteeId;
      }

      if(validateFriendshipRequest($userId, $friendRequesteeId)) {
        $errors['friendshipRequest'] = 'You already have sent a friend request to user: '.$friendRequesteeId;
      }

      if(validateFriendshipRequest($friendRequesteeId, $userId)) {
        addFriend($friendRequesteeId, $userId);
        $errors['friendshipRequest'] = 'You already have a friend request from user : '.$friendRequesteeId.' you have become friends';
      }

      if($friendRequesteeId == $userId) {
        $errors['friendshipRequest'] = 'You cannot send a friend request to yourself';
      }

      if(empty($errors)) {
        addFriendRequest($userId, $friendRequesteeId);
        $messages['friendshipRequest'] = 'You have successfully sent a friend request to: '.$friendRequesteeId;
      }

    } catch (Exception $ex) {
        $errors['dbError'] = "Database error: " . $ex->getMessage();
    }
  }

?>

<?php include("./common/header.php"); ?>

<div class="container">
  <h2 class="mt-3">Add Friend</h2>
  <h6 class="mb-4">Welcome <?php echo $userId ?> (You can change user <a href="Login.php">here</a> if this is not you.)</h6>
  
  <form action="AddFriend.php" method="post">
    <div class="card">
      <h6 class="card-header">Send a friend invite</h6>
      <div class="card-body">
      
        <input type="text" class="form-control" name="friendRequesteeId">
        <?php 
        if (!empty($errors)) {
          foreach($errors as $error) {
            echo "<span class='error text-danger'>$error</span>";
          }
        }

        if (!empty($messages)) {
          foreach($messages as $msj) {
            echo "<span class='success text-success'>$msj</span>";
          }
        }
        ?>
        <p class="card-text">Enter the ID of the user you want to be friends with.</p>
        <button type="submit" name="sendFriendRequest" class="btn" style="background-color: DarkSlateBlue; color: white;">Send Friend Request</button>
      </div>
    </div>
  </form>
  
</div>
<?php include('./common/footer.php'); ?>



