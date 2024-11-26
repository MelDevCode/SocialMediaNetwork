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
  // Include external entity class library
  include_once "EntityClassLib.php";
  // Include the external file with database functions
  include 'dbFunctions.php';
  // Start session
  session_start();

?>

<?php include("./common/header.php"); ?>

<div class="container">
    <h2 class="mt-3">My Pictures </h2>
    <select class="form-select mb-3" id="" name="">
        <option value="">Select an option</option>
        <option value=""></option>
    </select>
    <div class="row">
        <div class="col-7">
            <div class="card mt-2 pt-2 pb-3">
            <section>
                <div class="container">
                    <h5 class="fw-bold">Title of the Album</h5>
                    <div class="main-image">
                        <img src="Common/img/landscape1.jpg" alt="Main Image">
                    </div>
                    <div class="thumbnails">
                        <img src="Common/img/landscape1.jpg" alt="Thumbnail 1" onclick="changeImage(this.src, this)">
                        <img src="Common/img/landscape2.jpg" alt="Thumbnail 2" onclick="changeImage(this.src, this)">
                        <img src="Common/img/landscape3.jpg" alt="Thumbnail 3" onclick="changeImage(this.src, this)">
                        <img src="Common/img/landscape4.jpg" alt="Thumbnail 4" onclick="changeImage(this.src, this)">
                        <img src="Common/img/landscape5.jpg" alt="Thumbnail 3" onclick="changeImage(this.src, this)">
                        <img src="Common/img/landscape6.jpg" alt="Thumbnail 4" onclick="changeImage(this.src, this)">
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
                  <p class="">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. 
                  </p>
                </div>
      </section>
      <section>
            <h6 class="fw-bold">Comments</h6>
              <div class="card mb-2">
                <div class="card-body">
                  <div class="d-flex flex-start align-items-center">
                    <div>
                      <h6 class="fw-bold text-primary">Gojo Satoru</h6>
                      </p>
                    </div>
                  </div>
                  <p class="">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. 
                  </p>
                </div>
      </section>
        <section>
            <textarea class="form-control" id="" rows="4" placeholder="Leave a comment..."></textarea>
            <div class="d-flex justify-content-end">
              <a href="#" class="btn mt-2 " style="background-color: DarkSlateBlue; color: white;">Add a comment</a>
            </div>
       </section>
    </div>
  </div>
    
</div>


<br>
    <script>
    function changeImage(newSrc, thumbnail) {
    // Change the main image source
    document.querySelector('.main-image img').src = newSrc;

    // Remove 'active' class from all thumbnails
    document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));

    // Add 'active' class to the clicked thumbnail
    thumbnail.classList.add('active');
}
    </script>
<?php include('./common/footer.php'); ?>

    