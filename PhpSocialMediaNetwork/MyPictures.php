<style>
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.main-image {
    width: 500px;
    height: 300px;
    margin-bottom: 20px;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnails {
    display: flex;
    overflow-x: auto; /* Enable horizontal scrolling */
    padding-bottom: 8px;
    max-width: 500px; /* Matches the width of the main image */
    white-space: nowrap; /* Prevent thumbnails from wrapping */
}

.thumbnails img {
    width: 120px;
    height: 100px;
    margin: 0 8px;
    cursor: pointer;
    border: 3px solid transparent; /* Default border */
    transition: border 0.2s ease;
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
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userName = $user['Name'];
    $userId = $user['UserId'];
} else {
    // Save the requested page in the session
    header("Location: Login.php");
    exit();
}

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

<div class="container">
    <h2 class="mt-3">My Pictures </h2>
    <select class="form-select mb-3" id="albumSelect" name="albumSelect" onchange="fetchPhotosByAlbum(this.value)">
    <option value="">Select an Album</option>
    <?php foreach ($allAlbums as $index => $album): ?>
    <option name="albumId" value="<?php echo $album['Album_Id']; ?>" <?php echo $index === 0 ? 'selected' : ''; ?>>
        <?php echo htmlspecialchars($album['Title']); ?>
    </option>
<?php endforeach; ?>
    </select>
        <div class="row">
            <div class="col-7">
                <div class="card mt-2 pt-2 pb-3">
                <section>
        <div class="container">
            <!-- Set the default title using the first photo or a placeholder -->
            <h5 class="fw-bold" id="imageTitle">
                <?= !empty($photos) ? htmlspecialchars($photos[0]['Title']) : "No images available"; ?>
            </h5>

            <!-- Set the main image source using the first photo or a placeholder -->
            <div class="main-image">
              <img id="mainImage" 
      src="<?= !empty($photos) ? 'uploads/' . htmlspecialchars($photos[0]['File_Name']) : 'placeholder.jpg'; ?>" 
      alt="<?= !empty($photos) ? htmlspecialchars($photos[0]['Title']) : 'No Image'; ?>">
            </div>

            <!-- Populate thumbnails -->
            <div class="thumbnails">
                <?php if (!empty($photos)): ?>
                    <?php foreach ($photos as $index => $photo): ?>
                        <img 
                            src="<?= htmlspecialchars($photo['File_Name']) ?>" 
                            alt="<?= htmlspecialchars($photo['Title']) ?>" 
                            onclick="changeImage(
                                '<?= htmlspecialchars($photo['File_Name']) ?>', 
                                this, 
                                '<?= $photo['Picture_Id'] ?>', 
                                '<?= htmlspecialchars($photo['Title']) ?>', 
                                '<?= htmlspecialchars($photo['Description']) ?>')"
                            class="<?= $index === 0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No thumbnails available</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
            </div>
        </div>
        <div class="col-5">
        <section>
            <h6 class="fw-bold">Description</h6>
              <div class="card mb-2">
                <div class="card-body">
                  <p class=""></p>
                </div>
      </section>
      <section>
        <h6 class="fw-bold">Comments</h6>
        <div class="card mb-2">
            <div class="card-body" id="commentsSection">
            <?php if (!empty($photos) && $pictureId): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="d-flex flex-start align-items-center">
                        <div>
                            <h6 class="fw-bold text-primary"><?= htmlspecialchars($comment['AuthorName']) ?></h6>
                            <p><?= htmlspecialchars($comment['Comment_Text']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments available</p>
            <?php endif; ?>
            </div>
        </div>
      </section>
      <section>
        <form id="addCommentForm" action="MyPictures.php" method="POST">
            <textarea class="form-control" name="commentText" rows="4" placeholder="Leave a comment..." required></textarea>
            <input type="hidden" name="pictureId" value="<?= $pictureId ?? '' ?>">
            <input type="hidden" name="authorId" value="<?= $userId ?? '' ?>">
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
    // Update the main image and fetch comments dynamically
    function changeImage(newSrc, thumbnail, pictureId, title = "", description = "") {
        // Change the main image source
        const mainImage = document.querySelector('.main-image img');
        const titleElement = document.querySelector('#imageTitle');
        const descriptionElement = document.querySelector('.card-body p');
        const commentPictureIdInput = document.querySelector('input[name="pictureId"]'); // Get the hidden input for pictureId

        mainImage.src = newSrc;
        titleElement.textContent = title || "No Title";
        descriptionElement.textContent = description || "No Description";

        commentPictureIdInput.value = pictureId;

        // Remove 'active' class from all thumbnails
        document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
        // Add 'active' class to the clicked thumbnail
        thumbnail.classList.add('active');

        // Fetch and display comments for the selected picture
        fetchCommentsByPictureId(pictureId);
    }

    // Event listener for thumbnail clicks
    document.querySelector('.thumbnails').addEventListener('click', function (event) {
        if (event.target.tagName === 'IMG') {
            const thumbnail = event.target;
            const newSrc = thumbnail.src;
            const pictureId = thumbnail.getAttribute('data-picture-id');
            const title = thumbnail.getAttribute('data-title') || "No Title";
            const description = thumbnail.getAttribute('data-description') || "No Description";

            changeImage(newSrc, thumbnail, pictureId, title, description);
        }
    });

    // Fetch photos by album and dynamically update the gallery
    function fetchPhotosByAlbum(albumId) {
        if (albumId === "") {
            document.querySelector('.thumbnails').innerHTML = "";
            document.querySelector('.main-image img').src = "";
            document.querySelector('#imageTitle').textContent = "No Title";
            document.querySelector('.card-body p').textContent = "No Description";
            document.querySelector('input[name="pictureId"]').value = ""; // Clear pictureId
            return;
            
        }

        fetch(`fetch_photos.php?albumId=${albumId}`)
            .then(response => response.json())
            .then(photos => {
                const thumbnailsDiv = document.querySelector('.thumbnails');
                const mainImage = document.querySelector('.main-image img');
                const titleElement = document.querySelector('#imageTitle');
                const descriptionElement = document.querySelector('.card-body p');
                const commentPictureIdInput = document.querySelector('input[name="pictureId"]'); // Hidden input for pictureId

                thumbnailsDiv.innerHTML = ""; // Clear existing thumbnails

                if (photos.length > 0) {
                    // Set the first photo as the main image
                    mainImage.src = `${photos[0].File_Name}`;
                    titleElement.textContent = photos[0].Title;
                    descriptionElement.textContent = photos[0].Description;
                    commentPictureIdInput.value = photos[0].Picture_Id; // Update pictureId

                    // Populate thumbnails
                    photos.forEach(photo => {
                        const img = document.createElement('img');
                        img.src = `${photo.File_Name}`;
                        img.alt = photo.Title;
                        img.setAttribute('data-picture-id', photo.Picture_Id);
                        img.setAttribute('data-title', photo.Title);
                        img.setAttribute('data-description', photo.Description);
                        img.className = "thumbnail";

                        img.onclick = () => changeImage(
                            `${photo.File_Name}`,
                            img,
                            photo.Picture_Id,
                            photo.Title,
                            photo.Description
                        );

                        thumbnailsDiv.appendChild(img);
                    });

                    // Fetch comments for the first photo
                    fetchCommentsByPictureId(photos[0].Picture_Id);
                } else {
                    // Handle case when no photos are found
                    mainImage.src = "";
                    titleElement.textContent = "No images available";
                    descriptionElement.textContent = "No description available";
                    commentPictureIdInput.value = ""; // Clear pictureId
                }
            })
            .catch(error => console.error('Error fetching photos:', error));
    }

    // Fetch comments by pictureId and update the comments section
    function fetchCommentsByPictureId(pictureId) {
        fetch(`fetch_comments.php?pictureId=${pictureId}`)
            .then(response => response.json())
            .then(comments => {
                const commentsSection = document.getElementById('commentsSection');
                commentsSection.innerHTML = ""; // Clear existing comments

                if (comments.length > 0) {
                    comments.forEach(comment => {
                        const commentDiv = document.createElement('div');
                        commentDiv.classList.add('d-flex', 'flex-start', 'align-items-center', 'mb-2');

                        const commentContent = `
                            <div>
                                <h6 class="fw-bold text-primary">${comment.AuthorName}</h6>
                                <p>${comment.Comment_Text}</p>
                            </div>`;
                        commentDiv.innerHTML = commentContent;
                        commentsSection.appendChild(commentDiv);
                    });
                } else {
                    commentsSection.innerHTML = "<p>No comments available</p>";
                }
            })
            .catch(error => console.error('Error fetching comments:', error));
    }
    document.addEventListener('DOMContentLoaded', function () {
    const firstAlbum = document.querySelector('#albumSelect option[selected]');
    if (firstAlbum && firstAlbum.value) {
        fetchPhotosByAlbum(firstAlbum.value);
    }
});
</script>
<?php include('./common/footer.php'); ?>

    