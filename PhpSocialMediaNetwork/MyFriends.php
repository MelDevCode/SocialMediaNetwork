<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();
  extract($_POST);

  // Check if the user is logged in
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        $userName = $user['Name'];
        $userId = $user['UserId'];  // Get the student's name
    } else {
        header("Location: Login.php");
        exit();
    }

    $errors = array();

    if(isset($defriend)) {
        if(!isset($friends)) {
            $errors["noFriendsSelected"] = "You must select at least one friend!";
        } else {
            if(empty($errors)) {
                foreach($friends as $friend) {
                    deleteFriend($userId, $friend);
                }
            }
        }
    }

    if(isset($acceptRequest)) {
        if(!isset($friends)) {
            $errors["noFriendsSelected"] = "You must select at least one friend!";
        } else {
            if(empty($errors)) {
                foreach($friends as $friend) {
                    deleteFriend($userId, $friend);
                }
            }
        }
    }
?>

<?php include("./common/header.php"); ?>

<div class="container text-center">
    <!-- Page Heading -->
    <h2 class="mt-3">My Friends</h2>
    <h6>Welcome <?php echo htmlspecialchars($userName); ?> (You can change the user <a href="Login.php">here</a> if this is not you.)</h6>
    <?php 
        if (!empty($errors)) {
          foreach($errors as $error) {
            echo "<span class='error text-danger'>$error</span>";
          }
        }
    ?>
    <!-- Create Album Button -->
    <div class="mt-3 mx-auto">
        <a href="AddFriend.php">
            <button type="submit" class="btn btn-outline-secondary">Add a New Friend</button>
        </a>
    </div>
    <!-- List of Friends Card -->
    <div class="card mt-3 mx-auto" style="width: 55rem;">
        <h5 class="card-header text-center">List of Friends</h5> 
        <div class="card-body">
            <form action="MyFriends.php" method="post" onsubmit="confirmDeletion(event)">
                <table class="table align-middle mb-0 bg-white">
                    <thead class="bg-light text-start">
                        <tr>
                            <th>Name</th>
                            <th>Shared Albums</th>
                            <th>Defriend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $friends = getFriends($userId);
                            if(count($friends) > 0) {
                                foreach($friends as $friend) {
                                    echo "<tr>
                                            <td class='text-start'>
                                                <div class='d-flex align-items-center'>
                                                    <a href='FriendPictures.php?user=$friend' class='btn btn-link'>$friend</a>
                                                </div>
                                            </td>
                                            <td class='text-start'>
                                                <p class='fw-normal mb-1'>0</p>
                                            </td>
                                            <td class='text-start'>
                                                <input type='checkbox' value='$friend' name='friends[]'>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>You do not have friends yet</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            <div class="d-flex justify-content-end">
                <button type="submit" value="Submit" name="defriend" class="btn mt-3" style="background-color: DarkSlateBlue; color: white;">Deny Selected</button>
            </div>
        </form>
        </div>
    </div>
    
    <!-- Friend Requests Card -->
    <div class="card mt-3 mx-auto" style="width: 55rem;">
        <h5 class="card-header text-center">Friend Requests</h5> 
        <div class="card-body">
        <form action="MyFriends.php" method="post" onsubmit="confirmDeny(event)">
            <table class="table align-middle mb-0 bg-white">
                <thead class="bg-light text-start">
                    <tr>
                        <th>Name</th>
                        <th>Accept or Deny</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $friends = getFriendRequesters($userId);
                    if(count($friends) > 0) {
                        foreach($friends as $friend) {
                            echo "<tr>
                                    <td class='text-start'>
                                        <div class='d-flex align-items-center'>
                                            <button type='button' class='btn btn-link'>$friend</button>
                                        </div>
                                    </td>
                                    <td class='text-start'>
                                        <p class='fw-normal mb-1'>0</p>
                                    </td>
                                    <td class='text-start'>
                                        <input type='checkbox' name='requests[]' value='$friend'>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>You do not have friend requests yet</td></tr>";
                    }
                ?>
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-end">
                <button type="submit" name="acceptRequest" class="btn me-2" style="background-color: DarkSlateBlue; color: white;">Accept Selected</button>
                <button type="clear" name="clear" class="btn btn-outline-secondary">Deny Selected</button>
            </div>
        </form>
        </div>
    </div>
</div>

<?php include('./common/footer.php'); ?>

