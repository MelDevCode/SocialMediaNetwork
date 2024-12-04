<?php 
    // Include external entity class library
    include_once "EntityClassLib.php";
    // Include the external file with database functions
    include 'dbFunctions.php';
    include 'ValidationFunctions.php';
    // Start session
    session_start();

    define("IMAGE_MAX_WIDTH", 800);
    define("IMAGE_MAX_HEIGHT", 600);
    define("THUMB_MAX_WIDTH", 100);
    define("THUMB_MAX_HEIGHT", 100);
    define("UPLOAD_DIR", "uploads/");  // Directory to save images

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        // Set the target page only if not logged in
        if (!isset($_SESSION['redirect_to'])) {
            $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']);
        }
        // Redirect to login
        header("Location: Login.php");
        exit();
    }

    // User is logged in
    $user = $_SESSION['user'];
    $userName = $user['Name'];  // Get the user's name
    $userId = $user['UserId'];  // Get the user's UserId

    // Fetch all the albums for the user
    $allAlbums = getAlbums($userId);

    // Initialize error array
    $errors = [];

    if (isset($_POST['submit'])) {
        // Get form data
        $albumId = $_POST['album'];
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

        // Validate form data
        if (empty($albumId)) {
            $errors['album'] = 'Album is required';
        }

        // Check if files are uploaded
        if (isset($_FILES['pictures']) && !empty($_FILES['pictures']['name'][0])) {
            $fileCount = count($_FILES['pictures']['name']);
            $uploadedFiles = [];
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
            for ($i = 0; $i < $fileCount; $i++) {
                $fileTmp = $_FILES['pictures']['tmp_name'][$i];
                $fileName = basename($_FILES['pictures']['name'][$i]);
                $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    $errors['file'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
                    break;
                }
        
                // Process the image
                $imageDetails = getimagesize($fileTmp);
                if ($imageDetails) {
                    // Create an image resource
                    $srcImage = createImageResource($fileTmp, $imageDetails[2]);
        
                    if ($srcImage) {
                        // Resize the image
                        $resizedImage = resampleImage($srcImage, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
        
                        // Generate a unique file name to prevent overwriting
                        $newFileName = uniqid('img_') . '.' . $fileType;
                        $filePath = UPLOAD_DIR . $newFileName;
        
                        // Save resized image to the file system
                        if ($fileType == 'jpg' || $fileType == 'jpeg') {
                            imagejpeg($resizedImage, $filePath);
                        } elseif ($fileType == 'png') {
                            imagepng($resizedImage, $filePath);
                        } elseif ($fileType == 'gif') {
                            imagegif($resizedImage, $filePath);
                        }
        
                        // Save the file information (path) to the database
                        $uploadedFiles[] = [
                            'FileName' => $newFileName,  // Save the new file name
                            'Title' => $title,
                            'Description' => $description,
                            'AlbumId' => $albumId,
                            'User Id' => $userId,
                            'TempFilePath' => $_FILES['pictures']['tmp_name'][$i] // Add this line
                        ];
        
                        // Release resources
                        imagedestroy($srcImage);
                        imagedestroy($resizedImage);
                    } else {
                        $errors['file'] = "Error processing image: $fileName";
                        break;
                    }
                } else {
                    $errors['file'] = "File is not a valid image: $fileName";
                    break;
                }
            }
        
            if (empty($errors)) {
                foreach ($uploadedFiles as $fileData) {
                    savePicture(
                        $fileData['AlbumId'],
                        $fileData['FileName'],
                        $fileData['Title'],
                        $fileData['Description'],
                        $fileData['TempFilePath'] // Pass the temporary file path here
                    );
                }
                header('Location: MyPictures.php');
                exit();
            }
        }
    }
?>

<?php include("./common/header.php"); ?>
<form action="UploadPictures.php" method="POST" enctype="multipart/form-data">
    <div class="container" style="width: 60rem;">
        <h2 class="mt-3">Upload your Photos</h2>
        <p> Accepted picture types: JPG (JPEG), GIF, and PNG. You can upload multiple pictures at a time by pressing the shift key while selecting pictures. When uploading multiple pictures, the title and description fields will be applied to all pictures.</p>
        <!-- Display errors (if any) -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <table class="table align-middle">
            <tbody>
                <tr>
                    <td class="fw-bold">Upload to Album</td>
                    <td>
                        <?php if (!empty($allAlbums)) { ?>
                            <select class="form-select mb-3" id="album" name="album">
                                <option value="">Select album name</option>
                                <?php foreach ($allAlbums as $album) { ?>
                                    <option value="<?php echo htmlspecialchars($album['Album_Id']); ?>">
                                        <?php echo htmlspecialchars($album['Title']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <p>No albums available.</p>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">File to Upload</td>
                    <td>
                        <input type="file" class="form-control" name="pictures[]" multiple>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Title</td>
                    <td>
                        <input type="text" class="form-control" name="title" placeholder="Enter title">
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Description</td>
                    <td>
                        <textarea class="form-control" name="description" placeholder="Enter description" rows="3"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Buttons -->
        <div class="mt-1 d-flex justify-content-end">
            <button type="submit" name="submit" class="btn me-2" style="background-color: DarkSlateBlue; color: white;">Submit</button>
            <button type="reset" name="clear" class="btn" style="background-color: DarkSlateBlue; color: white;">Clear</button>
        </div>

    </div>
</form>

<?php include('./common/footer.php'); ?>