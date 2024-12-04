<?php
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();

  // Check if the user is logged in
  if (isset($_SESSION['user']))
   {
    $user = $_SESSION['user'];
    $userName = $user['Name'];  // Get the student's name
    $userId = $user['UserId'];

    // Get Album List
    $getAlbums = getPictureAlbums($userId);
    $getAccesibility = getAccessibility();
    
    }
     elseif (!isset($_SESSION['user']))   
    {
        // Save the requested page in the session
        if (!isset($_SESSION['redirect_to'])) 
        {
             $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']) . '?' . http_build_query($_GET);
        }
        
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
                <th>Title</th>
                <th>Number of Pictures</th>
                <th>Accessibility</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
      
                <?php 
                   

                    foreach($getAlbums as $album)  
                    {
                        $AccessibilityDropdownList = "<select name ='Accessibility' class='form-select'>";
                        $selected = "";

                        foreach($getAccesibility as $row)
                        {
                            
                            if($row['accessibility_Code'])
                            {
                                if($album['Accessibility_Code'] == $row['accessibility_Code'])
                                {
                                    $AccessibilityDropdownList .= "<option value='" . $row['accessibility_Code'] ."-" .  $album['Album_Id'] . "' selected>" . $row['Description']."</option>";
                                } 
                                else
                                {
                                    $AccessibilityDropdownList .= "<option value='".$row['accessibility_Code'] . "-" .  $album['Album_Id'] ."'>".$row['Description']."</option>";
                                }
                            }
                        }  
                        $AccessibilityDropdownList .=  "</select> "; 

                            echo "<tr>";
                            echo "<td><a href='MyPictures.php?Album_Id=" . $album['Album_Id'] . "'>". htmlspecialchars($album['Title']) . "</a></td>";
                            echo "<td>". htmlspecialchars($album['NumberOfPictures']) . "</td>";
                            echo "<td>"; echo $AccessibilityDropdownList; echo "</td>";
                            echo "<td> <a href='delete_album.php?Album_Id=" . $album['Album_Id'] . "' 
                                    onclick = 'return confrimDelete(\"" . addslashes($album['Title']) . "\", ". $album['NumberOfPictures'] .");' >Delete</a>
                            
                                </td>";
                        echo "</tr>";
                    }
                        
                ?>
        </tbody>
    </table>
    
    <!-- Save Changes Button -->
    <div class="mt-3 d-flex justify-content-end">
       <!-- <button type="reset" class="btn" style="background-color: DarkSlateBlue; color: white;">Save Changes</button> -->
        <a class="btn" id="saveLink" style="background-color: DarkSlateBlue; color: white;" 
        href="saveAlbumChanges.php?rowsToSave="+ rowsToSave>Save Changes</a>
    </div>
</div>
<script>
    let rowsToSave = [];

    function confrimDelete(albumTitle, numOfPictures){
        return confirm(`Are you sure you want to delete the album ${albumTitle} with ${numOfPictures} pictures?`
        );
    }

    const selectElements = document.getElementsByName('Accessibility');

    selectElements.forEach(function(element) {

        // Add event listener to the select element
        element.addEventListener('change', function() {
            // Get the selected value;
            const selectedValue = element.value;
            
            // Split the value to get the album id
            const albumId = selectedValue.split("-")[1];
            const accessibilityCode = selectedValue.split("-")[0];
            let row = {Album_Id: albumId, Accessibility_Code: accessibilityCode};
            // Check if the Album_Id already exists in rowsToSave
            let existingRowIndex = rowsToSave.findIndex(item => item.Album_Id === albumId);

            if (existingRowIndex !== -1) {
                // If it exists, update the row (you can modify properties if needed)
                rowsToSave[existingRowIndex] = row;
            } else {
                // If it doesn't exist, push the new row to the array
                rowsToSave.push(row);
            }

            // Convert the rowsToSave array into a JSON string
            const rowsToSaveString = encodeURIComponent(JSON.stringify(rowsToSave));
            // Set the href of the anchor tag with the serialized data as a query parameter
            const saveLink = document.getElementById('saveLink');
            saveLink.href = "saveAlbumChanges.php?rowsToSave=" + rowsToSaveString;
        });
    });
</script>


<?php include('./common/footer.php'); ?>

