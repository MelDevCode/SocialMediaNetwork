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
    <h2 class="mt-3">Upload your Photos</h2>
    <p> Accepted picture types: JPG (JPEG), GIF and PNG. You can upload multiple pictures at a time by pressing the shift key while selecting pictures. When uploading multiple pictures, the title and description fields will be applied to all pictures.</p>
  
    <table class="table align-middle">
        <tbody>
            <tr>
                <td class="fw-bold">Upload to Album</td>
                <td>
                    <select class="form-select mb-3" id="" name="">
                        <option value="">Select album name</option>
                        <option value=""></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="fw-bold">File to Upload</td>
                <td>
                    <input type="file" class="form-control" multiple>
                </td>
            </tr>
            <tr>
                <td class="fw-bold">Title</td>
                <td>
                    <input type="text" class="form-control" placeholder="Enter title">
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
    <div class="mt-1 d-flex justify-content-end">
        <button type="submit" name="submit" class="btn me-2" style="background-color: DarkSlateBlue; color: white;">Submit</button> 
        <button type="button" name="clear" class="btn" style="background-color: DarkSlateBlue; color: white;">Clear</button> 
    </div>
    
</div>

<?php include('./common/footer.php'); ?>