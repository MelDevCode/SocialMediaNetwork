<style>
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 100%; /* Prevent the entire container from overflowing */
    box-sizing: border-box;
}

.main-image {
    width: 100%; /* Use relative sizing to prevent overflow */
    max-width: 500px; /* Matches the defined width */
    height: 300px;
    margin-bottom: 20px;
    overflow: hidden; /* Prevent content from spilling out */
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image scales properly */
    display: block; /* Remove inline gap */
}

.thumbnails {
    display: flex;
    overflow-x: auto; /* Enable horizontal scrolling for thumbnails */
    padding-bottom: 8px;
    width: 100%;
    max-width: 500px; /* Constrain thumbnails to match the gallery width */
    white-space: nowrap; /* Prevent thumbnails from wrapping */
}

.thumbnails img {
    width: 120px;
    height: 100px;
    margin-right: 8px;
    cursor: pointer;
    transition: border 0.2s ease;
    flex-shrink: 0; /* Prevent images from shrinking */
}

.thumbnails img.active {
    border-color: black; /* Highlight the selected thumbnail */
}

</style>

<?php
include_once "EntityClassLib.php";
include 'dbFunctions.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Save the requested page in the session
    if (!isset($_SESSION['redirect_to'])) {
        $_SESSION['redirect_to'] = basename($_SERVER['PHP_SELF']) . '?' . http_build_query($_GET);
    }
    header("Location: Login.php");
    exit();
}

// User is logged in
$user = $_SESSION['user'];
$userName = $user['Name'];
$userId = $user['UserId'];

// Initialize albumId if it's set
$albumId = isset($_GET['albumId']) ? $_GET['albumId'] : null;

// Fetch albums for the user
$allAlbums = getAlbums($userId);
// Fetch photos for the selected album
$photos = $albumId ? getPhotosByAlbumId($albumId) : [];
// Initialize the pictureId to be used for comments
$pictureId = !empty($photos) ? $photos[0]['Picture_Id'] : null;
// Fetch all the comments
$comments = $pictureId ? getCommentsByPictureId($pictureId) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentText'], $_POST['pictureId'])) {
    $commentText = $_POST['commentText'];
    $pictureId = $_POST['pictureId'];

    // Save the comment
    saveComment($userId, $pictureId, $commentText);

    // Fetch comments again
    $comments = getCommentsByPictureId($pictureId);

    // Refresh the page
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

?>

<?php include("./common/header.php"); ?>

<div class="container" style="margin-bottom: 120px;">
    <h2 class="mt-3">My Pictures </h2>
    <!-- Dropdown menu -->
    <select class="form-select mb-3" id="albumSelect" name="albumSelect" onchange="fetchPhotosByAlbum(this.value)">
        <option value="">Select an Album</option>
        <?php foreach ($allAlbums as $index => $album): ?>
        <option name="albumId" value="<?php echo $album['Album_Id']; ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($album['Title']); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <!-- Warning messages -->
    <div class="row">
        <div id="warningMessage" class="col-12 text-center text-muted">
            <?php if (empty($allAlbums)): ?>
                <p>You have no albums yet. Please <a href="AddAlbum.php">click here</a> to add an album.</p>
            <?php elseif (empty($photos) && $albumId): ?>
                <p>This album has no pictures. Please <a href="UploadPictures.php">click here</a> to add pictures.</p>
            <?php else: ?>
                <p>Please select an album from the dropdown menu to see the pictures.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Photo gallery and details (hidden by default) -->
    <div id="gallery" class="row" style="display: none;">
        <div class="col-7">
            <div class="card mt-2 pt-2 pb-3">
                <section>
                    <div class="container">
                        <h5 class="fw-bold" id="imageTitle">No Title</h5>
                        <div class="main-image">
                            <img id="mainImage" src="" alt="No Image">
                        </div>
                        <div class="thumbnails"></div>
                    </div>
                </section>
            </div>
        </div>

        <div class="col-5">
            <section>
                <h6 class="fw-bold">Description</h6>
                <div class="card mb-2">
                    <div class="card-body">
                        <p id="imageDescription">No Description</p>
                    </div>
                </div>
            </section>
            <section>
                <h6 class="fw-bold">Comments</h6>
                <div class="card mb-2">
                    <div class="card-body" id="commentsSection"></div>
                </div>
            </section>
            <section>
                <form id="addCommentForm" action="MyPictures.php" method="POST">
                    <textarea class="form-control" name="commentText" rows="4" placeholder="Leave a comment..." required></textarea>
                    <input type="hidden" name="pictureId" id="hiddenPictureId" value="">
                    <input type="hidden" name="authorId" value="<?= $userId ?>">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn mt-2" style="background-color: DarkSlateBlue; color: white;">Add a comment</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>


<br>
<script>
    // Show/hide gallery based on album selection
    function fetchPhotosByAlbum(albumId) {
        const warningMessage = document.getElementById('warningMessage');
        const gallery = document.getElementById('gallery');
        if (!albumId) {
            gallery.style.display = 'none';
            warningMessage.innerHTML = "<p>Please select an album from the dropdown menu to see the pictures.</p>";
            return;
        }

        fetch(`fetch_photos.php?albumId=${albumId}`)
            .then(response => response.json())
            .then(photos => {
                const thumbnailsDiv = document.querySelector('.thumbnails');
                const mainImage = document.getElementById('mainImage');
                const titleElement = document.getElementById('imageTitle');
                const descriptionElement = document.getElementById('imageDescription');
                const commentPictureIdInput = document.getElementById('hiddenPictureId');

                thumbnailsDiv.innerHTML = ""; // Clear existing thumbnails
                gallery.style.display = 'flex';

                if (photos.length > 0) {
                    warningMessage.innerHTML = ""; // Clear warning
                    mainImage.src = photos[0].File_Name;
                    titleElement.textContent = photos[0].Title;
                    descriptionElement.textContent = photos[0].Description;
                    commentPictureIdInput.value = photos[0].Picture_Id;

                    photos.forEach(photo => {
                        const img = document.createElement('img');
                        img.src = photo.File_Name;
                        img.alt = photo.Title;
                        img.onclick = () => changeImage(photo.File_Name, photo.Picture_Id, photo.Title, photo.Description);
                        thumbnailsDiv.appendChild(img);
                    });

                    fetchCommentsByPictureId(photos[0].Picture_Id);
                } else {
                    warningMessage.innerHTML = "<p>This album has no pictures. Please <a href='UploadPictures.php'>click here</a> to add pictures.</p>";
                    gallery.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching photos:', error));
    }

    function changeImage(newSrc, pictureId, title, description) {
        document.getElementById('mainImage').src = newSrc;
        document.getElementById('imageTitle').textContent = title || "No Title";
        document.getElementById('imageDescription').textContent = description || "No Description";
        document.getElementById('hiddenPictureId').value = pictureId;
        fetchCommentsByPictureId(pictureId);
    }

    function fetchCommentsByPictureId(pictureId) {
        fetch(`fetch_comments.php?pictureId=${pictureId}`)
            .then(response => response.json())
            .then(comments => {
                const commentsSection = document.getElementById('commentsSection');
                commentsSection.innerHTML = comments.length > 0
                    ? comments.map(comment => `
                        <div class="d-flex flex-start align-items-center mb-2">
                            <div>
                                <h6 class="fw-bold text-primary">${comment.AuthorName}</h6>
                                <small class="text-muted">${new Date(comment.Created_At).toLocaleString()}</small>
                                <p>${comment.Comment_Text}</p>
                            </div>
                        </div>`).join('')
                    : "<p>No comments available</p>";
            })
            .catch(error => console.error('Error fetching comments:', error));
    }

    // Handle comment submission
    document.getElementById('addCommentForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        const formData = new FormData(this);
        fetch('add_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear the comment text area
                this.commentText.value = '';
                // Fetch and update comments
                fetchCommentsByPictureId(data.pictureId);
            } else {
                alert('Failed to add comment: ' + data.message);
            }
        })
        .catch(error => console.error('Error adding comment:', error));
    });
</script>
<?php include('./common/footer.php'); ?>

    