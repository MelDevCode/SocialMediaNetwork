<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();
  
   if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userName = $user['Name'];  // Get the student's name
    $userId = $user['UserId'];
    } else {
        header("Location: Login.php");
        exit();
    }

  $getAccesibility = getAccessibility();
  $title = $Description =  $valNewAlbum =  $selectAccessibility = $Error = "";
  
   if(filter_input(INPUT_SERVER,'REQUEST_METHOD') == 'POST')
   {
       $title = trim(filter_input(INPUT_POST,'titleName'));
       $Description = trim(filter_input(INPUT_POST,'albumDescription'));
       $selectAccessibility = trim(filter_input(INPUT_POST,'Accessibility'));
       
       
       if (!empty($title))
       {
          try
          {
              $valNewAlbum = AddNewAlbum($title, $Description, $userId, $selectAccessibility);
              $Error = "OK";
              echo $valNewAlbum;
          } 
          catch (Exception $ex) 
          {
             $Error =  "Error: " . $ex ;
             echo $Error;
          }
       }
       else
       {
         $tittle = "Enter Albun Name." ; 
       }
       
   }

?>

<?php include("./common/header.php"); ?>

<div class="container" style="width: 60rem;">
    <h2 class="mt-3">Create New Album</h2>
    <h6>Welcome Back <span style="color:blue;"> <?php echo $userName ?> </span> (You can change user <a href="Login.php">here</a> if this is not you.)</h6>
    
    <form method="post" id="addAlbum">
        <table class="table align-middle">
            <tbody>
                <tr>
                    <td class="fw-bold">Title</td>
                    <td>
                        <input type="text" name="titleName" class="form-control" placeholder="Enter album name">
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Accesibility</td>
                    <td>
                        <select name ="Accessibility" id="Accessibility" class="form-select">
                            <?php
                             foreach($getAccesibility as $row){
                                echo "<option value='".$row['accessibility_Code']."'>".$row['Description']."</option>";
                             }                    

                            ?>

                      </select>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Description</td>
                    <td>
                        <textarea name="albumDescription" class="form-control" placeholder="Enter description" rows="3"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Buttons -->
        <div class="mt-3">
          <button type="submit" name="submit" id="submitAddAlbum" class="btn" style="background-color: DarkSlateBlue; color: white;">Submit</button> 
          <button type="submit" name="clear" class="btn" onclick="clearForm()" style="background-color: DarkSlateBlue; color: white;">Clear</button> 
        </div>
  </form>
</div>
<script>
    function clearForm(){
        document.getElementById("addAlbum").reset();
    }
</script>

<?php include('./common/footer.php'); ?>

