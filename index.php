<?php 
ini_set('session.cookie_httponly', 1);
include('includes/header.php');
include('includes/navbar.php');
include('admin/config/dbcon.php');
include('includes/url.php');

if (empty($_SESSION['auth'])) {
  header("Location: 404.php");
  exit(0);
}

if ($_SESSION['auth_role'] != "student" && $_SESSION['auth_role'] != "faculty" && $_SESSION['auth_role'] != "staff") 
{
  header("Location:index.php");
  exit(0);
}
?>

<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

<!-- SweetAlert JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="card mt-4" data-aos="fade-up">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-center mt-2">
            <div class="mx-2">
              <img src="images/mcc-lrc.png" class="d-sm-none d-md-block me-4" style="height: 100px; width: 100px;" alt="MCC Logo">
            </div>
            <div class="col-8 mt-2">
              <center>
                <h3 class="fw-semibold">Madridejos Community College</h3>
                <h4 class="fw-semibold">Learning Resource Center</h4>
              </center>
              <form method="GET" onsubmit="return validateSearch();">
                <div class="d-flex">
                  <div class="input-group mb-3 me-6">
                    <input type="text" name="search" id="searchInput" value="<?php if (isset($_GET['search'])) { echo htmlspecialchars($_GET['search']); } ?>" class="form-control" placeholder="Type here to search" required>
                    <button class="btn btn-primary px-md-5 px-sm-1">Search</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="card-body border border-0">
          <?php if (!isset($_GET['search'])) : ?>
          <center>
            <a href="#new_books" class="btn btn-primary mt-2" data-aos="zoom-in">
              New Acquisitions
            </a>
          </center>
          <hr class="mt-2 mb-2 text-black">
          <?php endif;?>
          <div id="new_books" class="row row-cols-1 row-cols-md-12 g-4">
            <?php 
            if (isset($_GET['search'])) { 
              $filtervalues = strip_tags(trim(mysqli_real_escape_string($con, $_GET['search'])));
              $query = "SELECT book.*, COUNT(book.accession_number) AS copy_count,
                        SUM(CASE WHEN book.status = 'available' THEN 1 ELSE 0 END) AS available_count
                        FROM book 
                        WHERE title LIKE '%$filtervalues%' AND status_hold = ''
                        GROUP BY title, author, copyright_date, isbn
                        ORDER BY title, author, copyright_date, isbn DESC";
              $query_run = mysqli_query($con, $query);
              if (mysqli_num_rows($query_run) > 0) {
                foreach ($query_run as $book) {
                  $unavailable_count = $book['copy_count'] - $book['available_count'];
            ?>
            <div class="card mt-1">
              <div class="card-body pt-3 d-md-flex d-sm-block">
                <div class="col-xl-2">
                  <a href="book_details.php?a=<?= urlencode(encryptor('encrypt', $book['book_id'])); ?>&b=<?= urlencode(encryptor('encrypt', $book['title'])); ?>&c=<?= urlencode(encryptor('encrypt', $book['author'])); ?>&d=<?= urlencode(encryptor('encrypt', $book['copyright_date'])); ?>&e=<?= urlencode(encryptor('encrypt', $book['isbn'])); ?>" class="text-decoration-none">
                    <?php if ($book['book_image'] != ""): ?>
                    <img src="uploads/books_img/<?php echo htmlspecialchars($book['book_image']); ?>" width="100px" alt="">
                    <?php else: ?>
                    <img src="uploads/books_img/book_image.jpg" alt="">
                    <?php endif; ?>
                  </a>
                </div>
                <div class="col-xl-10">
                  <div class="row mt-3">
                    <div class="col-lg-12 col-md-12 fs-6">
                      <a href="book_details.php?a=<?= urlencode(encryptor('encrypt', $book['book_id'])); ?>&b=<?= urlencode(encryptor('encrypt', $book['title'])); ?>&c=<?= urlencode(encryptor('encrypt', $book['author'])); ?>&d=<?= urlencode(encryptor('encrypt', $book['copyright_date'])); ?>&e=<?= urlencode(encryptor('encrypt', $book['isbn'])); ?>" style="text-decoration: none" class="fw-bold">
                        <?= htmlspecialchars($book['title']) ?>
                      </a>
                      (<?= htmlspecialchars($book['copyright_date']) ?>)
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-9 col-md-8">
                      by&nbsp;<?= htmlspecialchars($book['author']); ?>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-9 col-md-8">
                      Call Number: &nbsp;<?= htmlspecialchars($book['call_number']); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
                } 
              } else {
                echo '<div class="col-md-12 alert alert-info h5 text-center">No Book Found</div>';
              }
            } else {
              $query = "SELECT book.*, COUNT(book.accession_number) AS copy_count, 
                        SUM(CASE WHEN book.status = 'available' THEN 1 ELSE 0 END) AS available_count
                        FROM book 
                        WHERE status_hold = ''
                        GROUP BY title, author, copyright_date, isbn 
                        ORDER BY title, author, copyright_date, isbn DESC";
              $query_run = mysqli_query($con, $query);
              if (mysqli_num_rows($query_run)) {
                foreach ($query_run as $book) {
            ?>
            <div class="col-12 col-md-3" data-aos="zoom-in">
              <a style="text-decoration: none !important;" href="book_details.php?a=<?= urlencode(encryptor('encrypt', $book['book_id'])); ?>&b=<?= urlencode(encryptor('encrypt', $book['title'])); ?>&c=<?= urlencode(encryptor('encrypt', $book['author'])); ?>&d=<?= urlencode(encryptor('encrypt', $book['copyright_date'])); ?>&e=<?= urlencode(encryptor('encrypt', $book['isbn'])); ?>">
                <div class="card h-100 shadow">
                  <?php if ($book['book_image'] != ""): ?>
                    <p class="text-center"><?php echo htmlspecialchars($book['title']) ?></p>
                  <img src="uploads/books_img/<?php echo htmlspecialchars($book['book_image']); ?>" alt="">
                  <?php else: ?>
                  <img src="uploads/books_img/book_image.jpg" alt="">
                  <?php endif; ?>
                </div>
              </a>
            </div>
            <?php
                }
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function validateSearch() {
    const searchInput = document.getElementById('searchInput').value;
    
    // Regular expression to detect HTML tags
    const regex = /<[^>]*>/;
    
    // Check if the input contains any HTML tags
    if (regex.test(searchInput)) {
        // Show SweetAlert
        swal({
            title: "Invalid Input!",
            text: "Stop that!",
            icon: "warning",
            button: "OK",
        });
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
</script>

<?php
include('includes/footer.php');
include('includes/script.php');
include('message.php'); 
?>