<?php 
include('authentication.php');
include('includes/header.php'); 
include('./includes/sidebar.php'); 

error_reporting(0);

if (isset($_SESSION['auth_admin']['admin_id'])) {
    $id_session = $_SESSION['auth_admin']['admin_id'];
}

$student_id = encryptor('decrypt',$_GET['a']);

$user_query = mysqli_query($con, "SELECT * FROM user WHERE student_id_no = '$student_id'");
$user_row = mysqli_fetch_array($user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Circulation</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item"><a href="circulation.php">Circulation</a></li>
                <li class="breadcrumb-item"><a href="circulation_borrow.php">Student Borrow Book</a></li>
                <li class="breadcrumb-item active">Borrow Book</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="col-12 col-md-6 mt-2">
                            <form action="" method="POST">
                                <div class="input-group mb-3 input-group-sm">
                                    <span class="input-group-text bg-primary text-white" id="basic-addon1">ACCESSION NO.</span>
                                    <input type="text" name="barcode" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" autofocus required>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT * FROM user WHERE student_id_no = '$student_id'";
                        $query_run = mysqli_query($con, $query);

                        if ($query_run) {
                            $row = mysqli_fetch_array($query_run);
                        ?>
                        <div class="text-muted mt-3">Student Name: <span class="h5 text-primary p-0 m-0 text-uppercase fw-semibold"><?php echo $row['firstname'].' '.$row['middlename'].' '.$row['lastname']; ?></span></div>
                        <div class="text-muted">Course: <span class="text-dark"><?php echo $row['course']; ?></span></div>
                        <div class="text-muted mb-5">Year Level: <span class="text-dark"><?php echo $row['year_level']; ?></span></div>
                        <?php
                        } else {
                            echo "No rows returned";
                        }
                        ?>

                        <div class="table-responsive">
                            <table class="table">
                                <form method="POST" action="">
                                    <thead class="border-top border-dark border-opacity-25">
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Copyright Date</th>
                                            <th>Publisher</th>
                                            <th>Barcode</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php 
                                        if (isset($_POST['barcode'])) {
                                            $barcode = $_POST['barcode'];
                                            
                                            $book_query = mysqli_query($con, "SELECT * FROM book WHERE accession_number = '$barcode' AND status = 'Available'");
                                            $book_count = mysqli_num_rows($book_query);
                                            $book_row = mysqli_fetch_array($book_query);
                                            
                                            if ($book_count == 0) {
                                                echo '<tr><td colspan="7"><div class="alert alert-info">No books match that accession number.</div></td></tr>';
                                            } elseif ($barcode == '') {
                                                echo '<tr><td colspan="7"><div class="alert alert-info">Enter the correct details!</div></td></tr>';
                                            } else {
                                        ?>
                                        <tr>
                                            <input type="hidden" name="user_id" value="<?php echo $user_row['user_id']; ?>">
                                            <input type="hidden" name="book_id" value="<?php echo $book_row['book_id']; ?>">
                                            <td class="auto-id" style="text-align: center;"></td>
                                            <td>
                                                <center>
                                                    <?php if ($book_row['book_image'] != ""): ?>
                                                        <img src="../uploads/books_img/<?php echo $book_row['book_image']; ?>" alt="" width="80px" height="80px">
                                                    <?php else: ?>
                                                        <img src="../uploads/books_img/book_image.jpg" alt="" width="80px" height="80px">
                                                    <?php endif; ?>
                                                </center>
                                            </td>
                                            <td><?php echo $book_row['title']; ?></td>
                                            <td><?php echo $book_row['author']; ?></td>
                                            <td><?php echo $book_row['copyright_date']; ?></td>
                                            <td><?php echo $book_row['publisher']; ?></td>
                                            <td><?php echo $book_row['barcode']; ?></td>
                                            <td><button name="borrow" class="btn btn-primary"><i class="fa fa-check"></i> Borrow</button></td>
                                        </tr>
                                        </tbody>
                                        <?php } } ?>

                                        <?php
                                        if (isset($_POST['borrow'])) {
                                            $user_id = $_POST['user_id'];
                                            $book_id = $_POST['book_id'];
                                            $date_borrowed = date('Y-m-d');
                                            $due_date = $_POST['due_date'];

                                            $restricted_categories = [3, 4, 6];
                                            $book_details_query = mysqli_query($con, "SELECT title, category_id FROM book WHERE book_id = $book_id");
                                            $book_details = mysqli_fetch_assoc($book_details_query);
                                            $book_title = $book_details['title'];
                                            $category_id = $book_details['category_id'];

                                            $user_details_query = mysqli_query($con, "SELECT student_id_no FROM user WHERE user_id = '$user_id'");
                                            $user_details = mysqli_fetch_assoc($user_details_query);
                                            $studentID = encryptor('encrypt', $user_details['student_id_no']);
                                            
                                            if (in_array($category_id, $restricted_categories)) {
                                                echo "<script>
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Not Allowed',
                                                        text: 'Books from this section cannot be borrowed!',
                                                        confirmButtonText: 'OK'
                                                    }).then(() => {
                                                        window.location = 'circulation_borrowing.php?student_id=" . $studentID . "';
                                                    });
                                                </script>";
                                            } else {
                                                $title_check_query = mysqli_query($con, "SELECT * FROM borrow_book 
                                                    INNER JOIN book ON borrow_book.book_id = book.book_id 
                                                    WHERE borrow_book.user_id = '$user_id' AND book.title = '$book_title' AND borrow_book.borrowed_status = 'borrowed'");
                                                if (mysqli_num_rows($title_check_query) > 0) {
                                                    echo "<script>
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Oops...',
                                                            text: 'You have already borrowed a book with the same title!',
                                                            confirmButtonText: 'OK'
                                                        }).then(() => {
                                                            window.location = 'circulation_borrowing.php?student_id=" . $studentID . "';
                                                        });
                                                    </script>";
                                                } else {
                                                    if ($category_id == 5) {
                                                        $due_date = date('Y-m-d', strtotime($date_borrowed . ' + 7 days'));
                                                    } elseif ($category_id == 1 || $category_id == 2) {
                                                        $due_date = date('Y-m-d', strtotime($date_borrowed . ' + 3 days'));
                                                    }
                                                
                                                    $trapBookCount = mysqli_query($con, "SELECT count(*) AS books_allowed FROM borrow_book WHERE user_id = '$user_id' AND borrowed_status = 'borrowed'");
                                                    $countBorrowed = mysqli_fetch_assoc($trapBookCount);
                                                    
                                                    $bookCountQuery = mysqli_query($con, "SELECT count(*) AS book_count FROM borrow_book WHERE user_id = '$user_id' AND borrowed_status = 'borrowed' AND book_id = $book_id");
                                                    $bookCount = mysqli_fetch_assoc($bookCountQuery);
                                                    
                                                    $allowed_book_query = mysqli_query($con, "SELECT * FROM allowed_book WHERE allowed_book_id = 1");
                                                    $allowed = mysqli_fetch_assoc($allowed_book_query);
                                                    if ($countBorrowed['books_allowed'] == $allowed['qntty_books']) {
                                                        echo "<script>
                                                            Swal.fire({
                                                                icon: 'warning',
                                                                title: 'Limit Reached',
                                                                text: 'You are allowed up to " . $allowed['qntty_books'] . " books!',
                                                                confirmButtonText: 'OK'
                                                            }).then(() => {
                                                                window.location = 'circulation_borrowing.php?student_id=" . $studentID . "';
                                                            });
                                                        </script>";
                                                    } elseif ($bookCount['book_count'] == 1) {
                                                        echo "<script>
                                                            Swal.fire({
                                                                icon: 'warning',
                                                                title: 'Already Borrowed',
                                                                text: 'This book has already been borrowed!',
                                                                confirmButtonText: 'OK'
                                                            }).then(() => {
                                                                window.location = 'circulation_borrowing.php?student_id=" . $studentID . "';
                                                            });
                                                        </script>";
                                                    } else {
                                                        mysqli_query($con, "UPDATE book SET status = 'Borrowed' WHERE book_id = '$book_id'");
                                                        mysqli_query($con, "INSERT INTO borrow_book(user_id, book_id, date_borrowed, due_date, borrowed_status)
                                                        VALUES ('$user_id', '$book_id', '$date_borrowed', '$due_date', 'borrowed')");
                                                        
                                                        $report_history = mysqli_query($con, "SELECT * FROM admin WHERE admin_id = $id_session");
                                                        $report_history_row = mysqli_fetch_array($report_history);
                                                        $admin_row = $report_history_row['firstname'] . " " . $report_history_row['middlename'] . " " . $report_history_row['lastname'];    
                                                        
                                                        mysqli_query($con, "INSERT INTO report 
                                                        (book_id, user_id, admin_name, detail_action, date_transaction)
                                                        VALUES ('$book_id', '$user_id', '$admin_row', 'Borrowed Book', NOW())");
                                                        
                                                        echo "<script>
                                                            Swal.fire({
                                                                icon: 'success',
                                                                title: 'Success',
                                                                text: 'Book Borrowed Successfully',
                                                                confirmButtonText: 'OK'
                                                            }).then(() => {
                                                                window.location = 'circulation_borrowing.php?student_id=" . $studentID . "';
                                                            });
                                                        </script>";
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </form>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php 
include('./includes/footer.php');
include('./includes/script.php');
include('message.php');   
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
     // Add auto-increment ID to Books Table
     let booksTable = document.querySelector('#myDataTable tbody');
     let bookRows = booksTable.querySelectorAll('tr');
     bookRows.forEach((row, index) => {
          row.querySelector('.auto-id').textContent = index + 1;
     });

     // Add auto-increment ID to Ebooks Table
     let ebooksTable = document.querySelector('#myDataTable2 tbody');
     let ebookRows = ebooksTable.querySelectorAll('tr');
     ebookRows.forEach((row, index) => {
          row.querySelector('.auto-id').textContent = index + 1;
     });
});
</script>
</body>
</html>
