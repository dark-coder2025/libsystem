<?php
ini_set('session.cookie_httponly', 1);
ob_start(); // Start output buffering
include('includes/header.php');
include('includes/navbar.php');
include('admin/config/dbcon.php');
include('includes/url.php');

if (!isset($_SESSION['auth'])) {
    header("Location: 404.php");
    exit(0);
}

if ($_SESSION['auth_role'] != "student" && $_SESSION['auth_role'] != "faculty" && $_SESSION['auth_role'] != "staff") 
{
  header("Location:index.php");
  exit(0);
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card mt-4">
                <div class="card-header">
                    <a href="index.php" class="btn btn-primary">Back</a>
                </div>
                <div class="card-body">
                    <section class="section profile">
                        <div class="row">
                            <?php
                            if (isset($_GET['a']) || isset($_GET['b']) || isset($_GET['c']) || isset($_GET['d']) || isset($_GET['e'])) {
                                $book_id = filter_var(encryptor('decrypt', $_GET['a']), FILTER_VALIDATE_INT);
                                $book_title = encryptor('decrypt', $_GET['b']);
                                $author = encryptor('decrypt', $_GET['c']);
                                $copyright_date = encryptor('decrypt', $_GET['d']);
                                $isbn = encryptor('decrypt', $_GET['e']);

                                $query = $con->prepare("SELECT 
                                                           book.*, 
                                                           COUNT(book.accession_number) AS copy_count, 
                                                           SUM(CASE 
                                                                WHEN book.status IN ('available', 'missing', 'damage', 'storage room') THEN 1 
                                                                ELSE 0 
                                                            END) AS available_count
                                                      FROM book 
                                                      WHERE title = ? AND author = ? AND copyright_date = ? AND isbn = ?
                                                      GROUP BY title, author, copyright_date, isbn 
                                                      ORDER BY title, author, copyright_date, isbn DESC");
                                $query->bind_param('ssss', $book_title, $author, $copyright_date, $isbn);
                                $query->execute();
                                $result = $query->get_result();

                                if ($result->num_rows > 0) {
                                    $book = $result->fetch_assoc();
                                    $unavailable_count = $book['copy_count'] - $book['available_count'];
                                    ?>
                                    <div class="col-xl-4">
                                        <div class="card">
                                            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                                                <img src="uploads/books_img/<?php echo htmlspecialchars($book['book_image'] ? $book['book_image'] : 'book_image.jpg'); ?>" height="300px" alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-8">
                                        <div class="card">
                                            <div class="card-body pt-3">
                                                <ul class="nav nav-tabs nav-tabs-bordered border-info">
                                                    <li class="nav-item">
                                                        <button class="nav-link active text-info border-info fw-semibold" data-bs-toggle="tab" data-bs-target="#profile-overview">Book Details</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content pt-2">
                                                    <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                                        <div class="row mt-3">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Title</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['title']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Author</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['author']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Copyright Date</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['copyright_date']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Publisher</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['publisher']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Place of Publication</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['place_publication']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">ISBN</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['isbn']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Call Number</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['call_number']) ?></div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-lg-3 col-md-4 label fw-semibold">Page</div>
                                                            <div class="col-lg-9 col-md-8"><?= htmlspecialchars($book['page']) ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card mt-2">
                                            <div class="card-body">
                                                <div class="row mt-2">
                                                    <div class="col-lg-3 col-md-4 label fw-semibold">Books Available</div>
                                                    <div class="col-lg-6 col-md-4">
                                                        <?= htmlspecialchars($book['available_count']) ?> of <?= htmlspecialchars($book['copy_count']) ?> available
                                                    </div>
                                                    <div class="col-lg-3 col-md-4 fw-semibold text-primary">
                                                        <form action="" method="POST">
                                                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
                                                            <button type="submit" name="hold" class="btn btn-primary px-4" <?= $book['available_count'] > 2 ? 'disabled' : '' ?>>
                                                                Hold
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo "No Book details found";
                                }
                            }
                            ?>
                        </div>
                    </section>
                </div>
                <div id="searchresult" class="text-center"></div>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($_POST['hold'])) {
    $book_id = mysqli_real_escape_string($con, $_POST['book_id']);

    // Get the book title
    $book_title_query = $con->prepare("SELECT title FROM book WHERE book_id = ?");
    $book_title_query->bind_param('i', $book_id);
    $book_title_query->execute();
    $book_title_result = $book_title_query->get_result();
    $book_title_row = $book_title_result->fetch_assoc();
    $book_title = $book_title_row['title'];

    // Get user ID or faculty ID
    $user_id = $_SESSION['auth_role'] == "student" ? $_SESSION['auth_stud']['stud_id'] : null;
    $faculty_id = ($_SESSION['auth_role'] == "faculty" || $_SESSION['auth_role'] == "staff") ? $_SESSION['auth_stud']['stud_id'] : null;

    // Check if the user is already holding or borrowing the book
    $check_query = $con->prepare("
        SELECT * FROM holds 
        WHERE book_title = ? 
        AND (user_id = ? OR faculty_id = ?)
    ");
    $check_query->bind_param('sii', $book_title, $user_id, $faculty_id);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['status'] = "You have already placed a hold on this book title!";
        $_SESSION['status_code'] = "warning";
        header("Location: book_details.php?title=" . urlencode($book_title) . "&id=" . urlencode($book_id));
        exit(0);
    }

    $borrow_check_query = $con->prepare("
    SELECT * FROM borrow_book
    WHERE book_id = ? 
    AND borrowed_status = 'borrowed' 
    AND (user_id = ? OR faculty_id = ?)
    ");
    $borrow_check_query->bind_param('iii', $book_id, $user_id, $faculty_id);
    $borrow_check_query->execute();
    $borrow_check_result = $borrow_check_query->get_result();

    if ($borrow_check_result->num_rows > 0) {
        $_SESSION['status'] = "You are already borrowing this book!";
        $_SESSION['status_code'] = "warning";
        header("Location: book_details.php?title=" . urlencode($book_title) . "&id=" . urlencode($book_id));
        exit();
    }

    // Check the current number of holds for the user
    $count_query = $con->prepare("SELECT COUNT(*) AS count_books FROM holds WHERE (user_id = ? OR faculty_id = ?)");
    $count_query->bind_param('ii', $user_id, $faculty_id);
    $count_query->execute();
    $count_result = $count_query->get_result();
    $count_row = $count_result->fetch_assoc();
    $current_hold_count = $count_row['count_books'];

    if ($current_hold_count >= 3) {
        $_SESSION['status'] = "You cannot hold more than 3 books!";
        $_SESSION['status_code'] = "warning";
        header("Location: book_details.php?title=" . urlencode($book_title) . "&id=" . urlencode($book_id));
        exit(0);
    } else {
        // Update the book status to "Hold"
        $update_query = $con->prepare("UPDATE book SET status_hold = 'Hold' WHERE book_id = ?");
        $update_query->bind_param('i', $book_id);
        $update_query->execute();

        // Insert hold record
        if ($_SESSION['auth_role'] == "student") {
            $insert_query = $con->prepare("INSERT INTO holds (book_id, book_title, user_id, hold_status, hold_date) VALUES (?, ?, ?, 'Hold', NOW())");
            $insert_query->bind_param('isi', $book_id, $book_title, $user_id);
        } elseif ($_SESSION['auth_role'] == "faculty" || $_SESSION['auth_role'] == "staff") {
            $insert_query = $con->prepare("INSERT INTO holds (book_id, book_title, faculty_id, hold_status, hold_date) VALUES (?, ?, ?, 'Hold', NOW())");
            $insert_query->bind_param('isi', $book_id, $book_title, $faculty_id);
        }

        if ($insert_query->execute()) {
            $_SESSION['status'] = "Book held successfully";
            $_SESSION['status_code'] = "success";
            header("Location:index.php");
            exit(0);
        } else {
            $_SESSION['status'] = "Failed to hold the book";
            $_SESSION['status_code'] = "error";
            header("Location: book_details.php?title=" . urlencode($book_title) . "&id=" . urlencode($book_id));
            exit(0);
        }
    }
}
?>

<?php
include('includes/footer.php');
include('includes/script.php');
include('message.php'); 
?>

<?php
ob_end_flush(); // End output buffering and flush output
?>
